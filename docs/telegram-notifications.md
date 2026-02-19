# Telegram Notifications

## Overview
Admin receives Telegram notifications for key events. Implemented as simple synchronous HTTP calls to the Telegram Bot API (no queue workers needed at low traffic).

## Status
**TODO — Phase 6** (not yet implemented)

## Config
```php
// config/services.php
'telegram' => [
    'bot_token'     => env('TELEGRAM_BOT_TOKEN'),
    'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
]
```

`.env` variables:
```
TELEGRAM_BOT_TOKEN=...
TELEGRAM_ADMIN_CHAT_ID=...
```

## Events to Notify
| Event | Where to add | Message |
|---|---|---|
| New user registration | `Auth\RegisterController@store` | "👤 Новый пользователь: {name} ({email})" |
| New order placed | `OrderService::confirm()` | "🎵 Новый заказ #{id}: {performer_name}, план {plan}, {amount}₽" |
| New user chat message | `ChatController@store` | "💬 Сообщение в заказе #{order_id} от {user_name}: {message_preview}" |

## Implementation Plan

### Create a TelegramService
`app/Services/TelegramService.php`:
```php
class TelegramService
{
    public function notify(string $message): void
    {
        $token   = config('services.telegram.bot_token');
        $chatId  = config('services.telegram.admin_chat_id');

        if (!$token || !$chatId) return;

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
```

### Inject and Call
In `RegisterController@store` (after `Auth::login($user)`):
```php
app(TelegramService::class)->notify(
    "👤 Новый пользователь: <b>{$user->name}</b> ({$user->email})"
);
```

In `OrderService::confirm()` (after status update):
```php
app(TelegramService::class)->notify(
    "🎵 Новый заказ <b>#{$order->id}</b>\nИсполнитель: {$order->performer_name}\nПлан: {$order->planLabel()}\nСумма: {$order->amount_paid} ₽"
);
```

In `ChatController@store` (after message saved):
```php
$preview = mb_substr($data['body'], 0, 100);
app(TelegramService::class)->notify(
    "💬 Сообщение в заказе <b>#{$order->id}</b> от {$order->user->name}:\n{$preview}"
);
```

## Notes
- If `TELEGRAM_BOT_TOKEN` or `TELEGRAM_ADMIN_CHAT_ID` are empty, the service silently skips sending
- Uses Laravel's `Http` facade (no extra packages needed)
- Synchronous — no queues needed for low traffic
- `parse_mode: HTML` allows `<b>bold</b>` formatting in messages
