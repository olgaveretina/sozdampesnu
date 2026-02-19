<?php

namespace App\Services;

use App\Mail\OrderStatusChanged;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TelegramService
{
    private string $token;
    private int|string $adminChatId;

    public function __construct()
    {
        $this->token       = config('services.telegram.bot_token');
        $this->adminChatId = config('services.telegram.admin_chat_id');
    }

    public function notifyNewUser(User $user): void
    {
        $this->notifyAdmin(
            "👤 *Новый пользователь*\n" .
            "Имя: {$user->name}\n" .
            "Email: {$user->email}"
        );
    }

    public function notifyNewOrder(Order $order): void
    {
        $planName  = Order::PLANS[$order->plan]['name'];
        $amount    = number_format($order->amount_paid, 0, '.', ' ');
        $userName  = $order->user->name;
        $userEmail = $order->user->email;

        $this->notifyAdmin(
            "🎵 *Новый заказ \#{$order->id}*\n" .
            "Пользователь: {$userName} ({$userEmail})\n" .
            "Исполнитель: {$order->performer_name}\n" .
            "Песня: {$order->song_name}\n" .
            "Тариф: {$planName}\n" .
            "Сумма: {$amount} ₽"
        );
    }

    public function notifyUserStatusChange(Order $order, string $newStatus, ?string $comment = null): void
    {
        $user = $order->user;

        if ($user->telegram_chat_id) {
            $statusLabel = Order::STATUSES[$newStatus] ?? $newStatus;
            $orderUrl    = route('orders.show', $order);
            $songName    = $order->song_name ?? $order->performer_name;

            $text = "📦 *Статус вашего заказа изменён*\n\n" .
                    "Заказ \#{$order->id} «{$songName}»\n" .
                    "Новый статус: *{$statusLabel}*";

            if ($comment) {
                $text .= "\n\nКомментарий: {$comment}";
            }

            $text .= "\n\n{$orderUrl}";

            $this->send($user->telegram_chat_id, $text);
        } else {
            try {
                Mail::to($user->email)->send(new OrderStatusChanged($order, $newStatus, $comment));
            } catch (\Throwable $e) {
                Log::warning('Order status email error: ' . $e->getMessage());
            }
        }
    }

    public function notifyNewChatMessage(Order $order, string $body): void
    {
        $userName = $order->user->name;
        $preview  = mb_strimwidth($body, 0, 200, '…');

        $this->notifyAdmin(
            "💬 *Сообщение от пользователя*\n" .
            "Заказ \#{$order->id} ({$order->performer_name})\n" .
            "От: {$userName}\n\n" .
            $preview
        );
    }

    public function notifyAdmin(string $text): void
    {
        $this->send($this->adminChatId, $text);
    }

    public function send(int|string $chatId, string $text): void
    {
        if (!$this->token || !$chatId) {
            return;
        }

        try {
            Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram send error: ' . $e->getMessage());
        }
    }
}
