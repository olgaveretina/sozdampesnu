<?php

namespace App\Services;

use App\Models\GiftCertificate;
use App\Models\Order;

class OrderService
{
    public function confirm(Order $order): void
    {
        if ($order->status !== 'pending_payment') {
            return;
        }

        $order->update(['status' => 'new']);

        $order->statusLogs()->create([
            'status'  => 'new',
            'comment' => 'Заказ оплачен и принят в очередь.',
        ]);

        if ($order->promoCode) {
            $order->promoCode->increment('used_count');
        }

        if ($order->gift_certificate_code) {
            GiftCertificate::where('code', $order->gift_certificate_code)
                ->where('is_used', false)
                ->update([
                    'is_used'           => true,
                    'used_by_order_id'  => $order->id,
                    'used_at'           => now(),
                ]);
        }
    }

    public function cancel(Order $order): void
    {
        if ($order->status !== 'pending_payment') {
            return;
        }

        $order->update(['status' => 'canceled']);

        $order->statusLogs()->create([
            'status'  => 'canceled',
            'comment' => 'Оплата не была завершена.',
        ]);
    }
}
