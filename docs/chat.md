# Order Chat

## Overview
Each order has a persistent chat thread between the user and admin. Users can message at any time regardless of order status. Admin replies via Filament.

## Route
```
POST /orders/{order}/chat  → ChatController@store  [auth]
```

## Controller
`app/Http/Controllers/ChatController.php`

## Database
Table: `chat_messages`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| order_id | bigint | FK → orders (cascade delete) |
| user_id | bigint | nullable FK → users (null if user deleted) |
| is_admin | boolean | default false — true for admin replies |
| body | text | Message content |
| created_at / updated_at | timestamps | |

## Model: `App\Models\ChatMessage`
```php
order()  // belongsTo Order
user()   // belongsTo User (nullable)
```

`Order::chatMessages()` → hasMany ChatMessage, ordered by created_at

## User Flow
On the order detail page (`orders/show.blade.php`):
- Chat section always visible at bottom of right column
- Messages displayed in bubble style:
  - Admin messages: left-aligned, light gray background
  - User messages: right-aligned, blue background
- Textarea + "Отправить" button form
- `POST /orders/{order}/chat` → creates message with `is_admin = false`
- Page scrolls to bottom of chat on load

## Admin Flow (Filament)
In the Order ViewOrder page → "Чат с клиентом" tab:
- `ChatMessagesRelationManager` shows all messages
- "Ответить клиенту" button → create form with body textarea
- Message created with `is_admin = true`, `user_id = auth()->id()`

## Authorization
User can only post to orders they own: `abort_if($order->user_id !== auth()->id(), 403)`

## Telegram Notification (TODO — Phase 6)
When user sends a chat message → notify admin via Telegram bot.
Triggered in `ChatController@store` after message is saved.
