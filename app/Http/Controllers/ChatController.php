<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $after = (int) $request->query('after', 0);

        return response()->json(
            $order->chatMessages()
                ->where('id', '>', $after)
                ->get(['id', 'is_admin', 'body', 'created_at'])
                ->map(fn($m) => [
                    'id'       => $m->id,
                    'is_admin' => $m->is_admin,
                    'body'     => $m->body,
                    'time'     => $m->created_at->format('d.m H:i'),
                ])
        );
    }

    public function store(Request $request, Order $order, TelegramService $telegram)
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

        $telegram->notifyNewChatMessage($order, $data['body']);

        return back()->with('success', 'Сообщение отправлено.');
    }
}
