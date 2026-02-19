<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

class ConfirmPayment extends Command
{
    protected $signature = 'order:confirm {id : Order ID}';
    protected $description = 'Manually confirm payment for an order (dev use)';

    public function handle(OrderService $orderService): void
    {
        $order = Order::find($this->argument('id'));

        if (!$order) {
            $this->error("Order not found.");
            return;
        }

        if ($order->status !== 'pending_payment') {
            $this->warn("Order #{$order->id} status is «{$order->status}», not pending_payment. Nothing to do.");
            return;
        }

        $orderService->confirm($order);

        if ($order->payment) {
            $order->payment->update(['status' => 'succeeded']);
        }

        $this->info("Order #{$order->id} confirmed. Status → new.");
    }
}
