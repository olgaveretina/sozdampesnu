<?php

namespace App\Http\Controllers;

use App\Models\GiftCertificate;
use App\Models\Order;
use App\Models\OrderUpgrade;
use App\Models\PromoCode;
use App\Services\OrderService;
use App\Services\YooKassaService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request, OrderService $orderService, YooKassaService $yooKassa)
    {
        $plan = (int) $request->input('plan');

        $rules = [
            'lyrics'                  => ['required', 'string', 'max:10000'],
            'performer_name'          => ['required', 'string', 'max:255'],
            'music_style'             => ['required', 'string', 'max:2000'],
            'plan'                    => ['required', 'in:1,2,3'],
            'promo_code'              => ['nullable', 'string', 'max:50'],
            'gift_certificate_code'   => ['nullable', 'string', 'max:50'],
            'disclaimer'              => ['accepted'],
        ];

        if ($plan === 3) {
            $rules['cover_description'] = ['nullable', 'string', 'max:2000'];
            $rules['cover_image']       = ['nullable', 'image', 'mimes:jpeg,png', 'max:20480'];
        }

        $data = $request->validate($rules);

        // Plan 3 requires at least one cover option
        if ($plan === 3 && empty($data['cover_description']) && !$request->hasFile('cover_image')) {
            return back()
                ->withErrors(['cover_description' => 'Для тарифа с публикацией необходимо описание обложки или файл.'])
                ->withInput();
        }

        $basePrice      = Order::PLANS[$plan]['price'];
        $discountAmount = 0;
        $promoCodeId    = null;
        $giftCertCode   = null;
        $promoCode      = null;
        $giftCert       = null;

        // Validate and apply promo code
        if (!empty($data['promo_code'])) {
            $promoCode = PromoCode::where('code', strtoupper($data['promo_code']))->first();
            if (!$promoCode || !$promoCode->isValid()) {
                return back()
                    ->withErrors(['promo_code' => 'Промокод недействителен или исчерпан.'])
                    ->withInput();
            }
            $promoCodeId     = $promoCode->id;
            $discountAmount += (int) round($basePrice * $promoCode->discount_percent / 100);
        }

        $priceAfterPromo = max(0, $basePrice - $discountAmount);

        // Validate and apply gift certificate
        if (!empty($data['gift_certificate_code'])) {
            $giftCert = GiftCertificate::where('code', strtoupper($data['gift_certificate_code']))
                ->where('is_used', false)
                ->first();
            if (!$giftCert) {
                return back()
                    ->withErrors(['gift_certificate_code' => 'Сертификат недействителен или уже использован.'])
                    ->withInput();
            }
            $discountAmount += min($giftCert->amount_rub, $priceAfterPromo);
            $giftCertCode    = $giftCert->code;
        }

        $finalAmount = max(0, $basePrice - $discountAmount);

        // Store cover image if uploaded
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('covers', 'public');
        }

        // Create order (pending_payment until confirmed)
        $order = Order::create([
            'user_id'               => auth()->id(),
            'lyrics'                => $data['lyrics'],
            'performer_name'        => $data['performer_name'],
            'music_style'           => $data['music_style'],
            'plan'                  => $plan,
            'cover_description'     => $data['cover_description'] ?? null,
            'cover_image_path'      => $coverImagePath,
            'status'                => 'pending_payment',
            'promo_code_id'         => $promoCodeId,
            'gift_certificate_code' => $giftCertCode,
            'discount_amount'       => $discountAmount,
            'amount_paid'           => $finalAmount,
        ]);

        // If gift certificate covers the full price — confirm immediately
        if ($finalAmount === 0) {
            $orderService->confirm($order);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Заказ оформлен! Ожидайте выполнения.');
        }

        // Create a pending payment record
        $payment = $order->payment()->create([
            'amount' => $finalAmount,
            'status' => 'pending',
        ]);

        // Create YooKassa payment and redirect
        try {
            $confirmationUrl = $yooKassa->createPayment($payment, $order);
            return redirect($confirmationUrl);
        } catch (\Exception $e) {
            $payment->delete();
            $order->delete();
            report($e);

            return back()
                ->withErrors(['general' => 'Ошибка создания платежа. Попробуйте ещё раз.'])
                ->withInput();
        }
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load(['audioFiles', 'coverFiles', 'statusLogs', 'chatMessages.user', 'editRequests', 'review']);

        return view('orders.show', compact('order'));
    }

    public function updateComment(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->update(['user_comment' => $request->input('user_comment')]);

        return back()->with('success', 'Комментарий сохранён.');
    }

    public function selectVersion(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'selected_audio_id' => ['nullable', 'integer'],
            'selected_cover_id' => ['nullable', 'integer'],
        ]);

        $order->update($data);

        return back()->with('success', 'Выбор сохранён.');
    }

    public function submitReview(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if($order->status !== 'completed', 403);
        abort_if($order->review()->exists(), 403);

        $data = $request->validate([
            'text'   => ['required', 'string', 'max:3000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $order->review()->create([
            'user_id' => auth()->id(),
            'text'    => $data['text'],
            'rating'  => $data['rating'] ?? null,
        ]);

        return back()->with('success', 'Отзыв отправлен. Спасибо!');
    }

    public function upgrade(Request $request, Order $order, YooKassaService $yooKassa)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if($order->plan >= 3, 403);
        abort_if(!in_array($order->status, ['generated', 'completed', 'sent_for_revision', 'new']), 403);

        $toPlan = $order->plan + 1;
        $prices = Order::PLANS;
        $amount = $prices[$toPlan]['price'] - $prices[$order->plan]['price'];

        $upgrade = $order->upgrades()->create([
            'from_plan' => $order->plan,
            'to_plan'   => $toPlan,
            'amount'    => $amount,
            'status'    => 'pending_payment',
        ]);

        $payment = $upgrade->payment()->create([
            'amount' => $amount,
            'status' => 'pending',
        ]);

        try {
            $confirmationUrl = $yooKassa->createUpgradePayment($payment, $order, $upgrade);
            return redirect($confirmationUrl);
        } catch (\Exception $e) {
            $payment->delete();
            $upgrade->delete();
            report($e);

            return back()->with('error', 'Ошибка создания платежа. Попробуйте ещё раз.');
        }
    }

    public function requestEdit(Request $request, Order $order, YooKassaService $yooKassa)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if($order->status === 'pending_payment', 403);

        $data = $request->validate([
            'instructions' => ['required', 'string', 'max:3000'],
        ]);

        $editRequest = $order->editRequests()->create([
            'instructions' => $data['instructions'],
            'status'       => 'pending_payment',
        ]);

        $payment = $editRequest->payment()->create([
            'amount' => \App\Models\EditRequest::PRICE,
            'status' => 'pending',
        ]);

        try {
            $confirmationUrl = $yooKassa->createEditPayment($payment, $order, $editRequest);
            return redirect($confirmationUrl);
        } catch (\Exception $e) {
            $payment->delete();
            $editRequest->delete();
            report($e);

            return back()->with('error', 'Ошибка создания платежа. Попробуйте ещё раз.');
        }
    }
}
