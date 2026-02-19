<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $update = $request->all();

        $message = $update['message'] ?? null;
        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'];
        $text   = trim($message['text'] ?? '');

        if (str_starts_with($text, '/start ')) {
            $token = substr($text, 7);
            $user  = User::where('telegram_bind_token', $token)->first();

            if ($user) {
                $user->update([
                    'telegram_chat_id'    => $chatId,
                    'telegram_bind_token' => null,
                ]);

                $this->sendMessage($chatId, "Аккаунт успешно привязан!\nТеперь вы будете получать уведомления об изменении статуса заказов.");
            } else {
                $this->sendMessage($chatId, 'Код привязки недействителен или уже использован. Вернитесь в личный кабинет и получите новый код.');
            }
        }

        return response()->json(['ok' => true]);
    }

    private function sendMessage(int $chatId, string $text): void
    {
        Http::post('https://api.telegram.org/bot' . config('services.telegram.bot_token') . '/sendMessage', [
            'chat_id' => $chatId,
            'text'    => $text,
        ]);
    }
}
