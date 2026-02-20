<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Order;
use App\Models\OrderUpgrade;
use App\Models\Payment;
use App\Services\OrderService;
use App\Services\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private YooKassaService $yooKassa,
        private OrderService $orderService,
    ) {}

    public function webhook(Request $request)
    {
        $body = $request->json()->all();

        if (empty($body)) {
            return response()->json(['error' => 'Empty body'], 400);
        }

        try {
            $notification = $this->yooKassa->parseNotification($body);
        } catch (\Exception $e) {
            Log::warning('YooKassa webhook parse error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid notification'], 400);
        }

        $yooPaymentId = $notification->getObject()->getId();

        // Re-fetch from API to ensure authenticity
        try {
            $remotePayment = $this->yooKassa->getPayment($yooPaymentId);
        } catch (\Exception $e) {
            Log::error('YooKassa getPayment error: ' . $e->getMessage());
            return response()->json(['error' => 'Cannot verify payment'], 500);
        }

        $payment = Payment::where('yookassa_id', $yooPaymentId)->first();
        if (!$payment) {
            return response()->json(['ok' => true]); // Unknown payment — ignore
        }

        $newStatus = $remotePayment->getStatus();

        $payment->update([
            'status'        => $newStatus,
            'yookassa_data' => $remotePayment->jsonSerialize(),
        ]);

        if ($newStatus === 'succeeded') {
            $this->handleSucceeded($payment);
        } elseif ($newStatus === 'canceled') {
            $this->handleCanceled($payment);
        }

        return response()->json(['ok' => true]);
    }

    private function handleSucceeded(Payment $payment): void
    {
        $payable = $payment->payable;

        if ($payable instanceof Order) {
            $this->orderService->confirm($payable);
        } elseif ($payable instanceof OrderUpgrade) {
            $payable->update(['status' => 'paid']);
            $payable->order->update([
                'plan'   => $payable->to_plan,
                'status' => 'sent_for_revision',
            ]);
            $toPlanName = Order::plans()[$payable->to_plan]['name'];
            $comment = "Тариф улучшен до «{$toPlanName}». Отправлен на доработку.";
            $payable->order->statusLogs()->create([
                'status'  => 'sent_for_revision',
                'comment' => $comment,
            ]);
            app(\App\Services\TelegramService::class)
                ->notifyUserStatusChange($payable->order, 'sent_for_revision', $comment);
        } elseif ($payable instanceof EditRequest) {
            $payable->update(['status' => 'paid']);
            $payable->order->update(['status' => 'sent_for_revision']);
            $comment = 'Оплачена правка заказа.';
            $payable->order->statusLogs()->create([
                'status'  => 'sent_for_revision',
                'comment' => $comment,
            ]);
            $telegram = app(\App\Services\TelegramService::class);
            $telegram->notifyUserStatusChange($payable->order, 'sent_for_revision', $comment);
            $telegram->notifyNewEditRequest($payable->order, $payable);
        } elseif ($payable instanceof \App\Models\GiftCertificate) {
            // Gift certificate purchased — generate code
            $payable->update([
                'code' => strtoupper(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 12)),
            ]);
        }
    }

    private function handleCanceled(Payment $payment): void
    {
        $payable = $payment->payable;

        if ($payable instanceof Order) {
            $this->orderService->cancel($payable);
        } elseif ($payable instanceof OrderUpgrade) {
            $payable->update(['status' => 'canceled']);
        } elseif ($payable instanceof EditRequest) {
            $payable->update(['status' => 'canceled']);
        }
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order');

        if ($orderId && auth()->check()) {
            $order = Order::where('id', $orderId)
                ->where('user_id', auth()->id())
                ->first();

            if ($order) {
                // Proactively verify any pending payments in case webhook hasn't fired yet
                $this->processPendingPayments($order);

                return redirect()
                    ->route('orders.show', $order)
                    ->with('success', 'Оплата прошла! Мы получили ваш заказ и приступим к работе.');
            }
        }

        return redirect()->route('profile')
            ->with('success', 'Оплата прошла успешно!');
    }

    /**
     * Proactively pull payment status from YooKassa for all pending payments
     * linked to this order. Handles the case where the webhook hasn't arrived yet
     * (e.g. localhost dev environment).
     */
    private function processPendingPayments(Order $order): void
    {
        $order->loadMissing(['payment', 'editRequests.payment', 'upgrades.payment']);

        $candidates = collect();

        if ($order->payment && $order->payment->status === 'pending') {
            $candidates->push($order->payment);
        }

        foreach ($order->editRequests as $er) {
            if ($er->payment && $er->payment->status === 'pending') {
                $candidates->push($er->payment);
            }
        }

        foreach ($order->upgrades as $upgrade) {
            if ($upgrade->payment && $upgrade->payment->status === 'pending') {
                $candidates->push($upgrade->payment);
            }
        }

        foreach ($candidates as $payment) {
            if (!$payment->yookassa_id) {
                continue;
            }

            try {
                $remote    = $this->yooKassa->getPayment($payment->yookassa_id);
                $newStatus = $remote->getStatus();

                if ($newStatus === $payment->status) {
                    continue;
                }

                $payment->update([
                    'status'        => $newStatus,
                    'yookassa_data' => $remote->jsonSerialize(),
                ]);

                if ($newStatus === 'succeeded') {
                    $this->handleSucceeded($payment);
                } elseif ($newStatus === 'canceled') {
                    $this->handleCanceled($payment);
                }
            } catch (\Exception $e) {
                Log::warning('Success page payment check error: ' . $e->getMessage());
            }
        }
    }

    public function cancel(Request $request)
    {
        $orderId = $request->query('order');

        if ($orderId && auth()->check()) {
            $order = Order::where('id', $orderId)
                ->where('user_id', auth()->id())
                ->first();

            if ($order && $order->status === 'pending_payment') {
                return redirect()
                    ->route('orders.create')
                    ->with('error', 'Оплата отменена. Вы можете попробовать снова.');
            }
        }

        return redirect()->route('profile')
            ->with('error', 'Оплата отменена.');
    }
}
