# Telegram Notifications

## Overview
Admin receives Telegram notifications for key events. Implemented as simple synchronous HTTP calls to the Telegram Bot API (no queue workers needed at low traffic).

## Status
**Implemented** (Phase 6 partially done)

## Config
```php
// config/services.php
'telegram' => [
    'bot_token'     => env('TELEGRAM_BOT_TOKEN'),
    'bot_username'  => env('TELEGRAM_BOT_USERNAME'),
    'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
]
```

`.env` variables:
```
TELEGRAM_BOT_TOKEN=...
TELEGRAM_BOT_USERNAME=...
TELEGRAM_ADMIN_CHAT_ID=...
```

## Events Implemented
| Event | Where | Message |
|---|---|---|
| New user registration | `Auth\RegisterController@store` | 👤 Новый пользователь: name (email) |
| New order placed | `OrderService::confirm()` | 🎵 Новый заказ #id: performer, план, сумма |
| New user chat message | `ChatController@store` | 💬 Сообщение в заказе #id от user: preview |

## Service
`app/Services/TelegramService.php`

Public methods:
- `notifyNewUser(User $user)` — called on registration
- `notifyNewOrder(Order $order)` — called in `OrderService::confirm()` after status → new
- `notifyNewChatMessage(Order $order, string $body)` — called in `ChatController@store`
- `notifyAdmin(string $text)` — send arbitrary text to admin chat
- `send(int|string $chatId, string $text)` — send to any chat ID (used for user binding confirmations)

Uses `parse_mode: Markdown`. Errors are caught and logged — a failed send never breaks the main flow.

## Telegram Account Binding (User notifications)

Users can bind their Telegram account on the profile page to receive order status notifications.

### Flow
1. User clicks "Привязать Telegram" on `/profile`
2. `ProfileController@generateTelegramToken` generates a 32-char token, stores in `users.telegram_bind_token`
3. User is redirected to `https://t.me/{BOT_USERNAME}?start={token}`
4. Telegram opens the bot; user sends `/start` (deep link sends `/start {token}` automatically)
5. `TelegramController@webhook` receives the update, finds user by token, sets `telegram_chat_id`, clears token, sends confirmation message to user

### DB columns on `users`
- `telegram_chat_id` — bigint, nullable — set after successful binding
- `telegram_bind_token` — string 32, nullable, unique — one-time pairing token

### Routes
```
POST /profile/telegram        → ProfileController@generateTelegramToken  [auth]
DELETE /profile/telegram      → ProfileController@unlinkTelegram          [auth]
POST /webhooks/telegram       → TelegramController@webhook                [no CSRF]
```

### Bot webhook registration
Register once when the production domain is ready:
```bash
curl -X POST "https://api.telegram.org/bot{TOKEN}/setWebhook" \
  -d "url=https://sozdampesnu.ru/webhooks/telegram"
```

## Notes
- If `TELEGRAM_BOT_TOKEN` or `TELEGRAM_ADMIN_CHAT_ID` are empty, the service silently skips sending
- Uses Laravel's `Http` facade (no extra packages needed)
- Synchronous — no queues needed for low traffic
