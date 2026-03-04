<?php

namespace App\Http\Controllers;

use App\Models\GiftCertificate;
use App\Models\Order;
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
        $plan      = (int) $request->input('plan');
        $isVideo   = $plan === 3;
        $orderType = $isVideo ? 'video' : 'song';

        $commonRules = [
            'performer_name'        => ['required', 'string', 'max:255'],
            'song_name'             => ['required', 'string', 'max:255'],
            'plan'                  => ['required', 'in:1,2,3'],
            'promo_code'            => ['nullable', 'string', 'max:50'],
            'gift_certificate_code' => ['nullable', 'string', 'max:50'],
            'lyrics_edit_permission' => ['nullable', 'in:none,minor,any'],
            'disclaimer'            => ['accepted'],
            'accept_terms'          => ['accepted'],
        ];

        if ($isVideo) {
            $rules = array_merge($commonRules, [
                'singer_description'  => ['required', 'string', 'max:3000'],
                'cover_description'   => ['required', 'string', 'max:2000'],
                'video_audio'         => ['required', 'file', 'mimes:mpga,mp3,m4a,wav', 'max:51200'],
                'video_images'        => ['nullable', 'array', 'max:6'],
                'video_images.*'      => ['image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            ]);
        } else {
            $rules = array_merge($commonRules, [
                'lyrics'      => ['required', 'string', 'max:10000'],
                'music_style' => ['required', 'string', 'max:2000'],
            ]);
        }

        $data = $request->validate($rules);

        $basePrice      = Order::plans()[$plan]['price'];
        $discountAmount = 0;
        $promoCodeId    = null;
        $giftCertCode   = null;

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

        // Store video audio if uploaded
        $videoAudioPath = null;
        if ($isVideo && $request->hasFile('video_audio')) {
            $videoAudioPath = $request->file('video_audio')->store('video-audio', 'public');
        }

        // Store video images (up to 6)
        $videoImagePaths = null;
        if ($isVideo && $request->hasFile('video_images')) {
            $videoImagePaths = [];
            foreach ($request->file('video_images') as $image) {
                $videoImagePaths[] = $image->store('video-images', 'public');
            }
        }

        // Create order (pending_payment until confirmed)
        $order = Order::create([
            'user_id'               => auth()->id(),
            'order_type'            => $orderType,
            'lyrics'                    => $isVideo ? null : ($data['lyrics'] ?? null),
            'lyrics_edit_permission'    => $isVideo ? null : ($data['lyrics_edit_permission'] ?? null),
            'performer_name'        => $data['performer_name'],
            'song_name'             => $data['song_name'] ?? null,
            'music_style'           => $isVideo ? null : ($data['music_style'] ?? null),
            'plan'                  => $plan,
            'cover_description'     => $data['cover_description'] ?? null,
            'singer_description'    => $data['singer_description'] ?? null,
            'video_audio_path'      => $videoAudioPath,
            'video_images'          => $videoImagePaths,
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

        $order->load(['audioFiles', 'coverFiles', 'statusLogs', 'chatMessages.user', 'editRequests.payment', 'review']);

        return view('orders.show', compact('order'));
    }

    public function files(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $files = $order->files()
            ->orderBy('id')
            ->get()
            ->map(fn($f) => [
                'id'    => $f->id,
                'type'  => $f->type,
                'label' => $f->label,
                'url'   => \Illuminate\Support\Facades\Storage::url($f->path),
            ]);

        $canRequestEdit = $order->audioFiles()->exists()
            && !in_array($order->status, ['pending_payment', 'canceled']);

        $hasAudio = $order->audioFiles()->exists();
        $canComplete = $hasAudio
            && !in_array($order->status, ['completed', 'pending_payment', 'canceled']);

        $order->loadMissing('statusLogs');
        $statusLogs = $order->statusLogs->map(fn($log) => [
            'id'           => $log->id,
            'status_label' => $log->statusLabel(),
            'comment'      => $log->comment,
            'time'         => $log->created_at->format('d.m.Y H:i'),
        ]);

        return response()->json([
            'files'            => $files,
            'can_request_edit' => $canRequestEdit,
            'can_complete'     => $canComplete,
            'status_label'     => $order->statusLabel(),
            'status_logs'      => $statusLogs,
        ]);
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

    public function complete(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if(!$order->audioFiles()->exists(), 403);
        abort_if(in_array($order->status, ['completed', 'pending_payment', 'canceled']), 403);

        $order->update(['status' => 'completed']);
        $order->statusLogs()->create(['status' => 'completed', 'comment' => null]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Заказ завершён! Спасибо, что воспользовались сервисом.');
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
