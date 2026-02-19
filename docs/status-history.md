# Order Status History

## Overview
Every status change creates an immutable log entry. Users see the full history on the order page. Admin adds a comment with each status change.

## Database
Table: `order_status_logs`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| order_id | bigint | FK → orders (cascade delete) |
| status | string | Status value at time of log |
| comment | text | nullable — admin's comment |
| created_at | timestamp | Auto-set, no updated_at |

## Model: `App\Models\OrderStatusLog`
```php
public $timestamps = false;           // only created_at, no updated_at
const UPDATED_AT = null;

order()         // belongsTo Order
statusLabel()   // returns Russian label from Order::STATUSES
```

`Order::statusLogs()` → hasMany OrderStatusLog, ordered by created_at (ascending)

## When Entries Are Created
| Trigger | Status | Comment source |
|---|---|---|
| Order confirmed (payment success) | new | "Заказ оплачен и принят в очередь." |
| Order canceled (payment failed) | canceled | "Оплата не была завершена." |
| Admin changes status via Filament | any | Admin's input (optional) |
| Plan upgrade paid | sent_for_revision | "Тариф улучшен до «{name}». Отправлен на доработку." |
| Edit request paid | sent_for_revision | "Оплачена правка заказа." |

## User Display (`orders/show.blade.php`)
Status history shown in the right column as a scrollable card.
Each entry shows:
- Status badge (Russian label)
- Admin comment (if present, shown as muted small text)
- Date/time

## Admin (Filament)
`StatusLogsRelationManager` on the Order ViewOrder page shows the full history in a table.
Admin cannot edit or delete log entries — it's append-only by design.

## Creating a Log Entry
```php
$order->statusLogs()->create([
    'status'  => 'in_progress',
    'comment' => 'Начали работу над вашей песней.',
]);
```
