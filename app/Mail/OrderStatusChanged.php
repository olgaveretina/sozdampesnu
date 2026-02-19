<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Order $order,
        public string $newStatus,
        public ?string $comment = null,
    ) {}

    public function envelope(): Envelope
    {
        $statusLabel = Order::STATUSES[$this->newStatus] ?? $this->newStatus;
        $songName    = $this->order->song_name ?? $this->order->performer_name;

        return new Envelope(
            subject: "Статус заказа «{$songName}» изменён: {$statusLabel}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-status-changed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
