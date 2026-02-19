<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $order->chatMessages()->create([
            'user_id'  => auth()->id(),
            'is_admin' => false,
            'body'     => $data['body'],
        ]);

        // TODO: Phase 6 — Telegram notification

        return back()->with('success', 'Сообщение отправлено.');
    }
}
