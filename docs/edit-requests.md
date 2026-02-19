# Edit Requests

## Overview
Users can request changes to their song for 400 ₽ per edit. They describe what to change (music style, lyrics, specific part to regenerate). The request is created with prepayment.

## Route
```
POST /orders/{order}/edit-request  → OrderController@requestEdit  [auth]
```

## Controller Method
`app/Http/Controllers/OrderController@requestEdit`

## View
Edit request form in `resources/views/orders/show.blade.php`.
- Always visible on the order detail page (any status except pending_payment)
- Textarea for instructions (max 3000 chars)

## Database
Table: `edit_requests`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| order_id | bigint | FK → orders |
| instructions | text | What the user wants changed |
| status | string | pending_payment / paid / in_progress / completed / canceled |
| created_at / updated_at | timestamps | |

## Model: `App\Models\EditRequest`
```php
const PRICE = 400;  // rubles

order()    // belongsTo Order
payment()  // morphOne Payment
```

`Order::editRequests()` → hasMany EditRequest

## Statuses
| Status | Meaning |
|---|---|
| pending_payment | Created, awaiting payment |
| paid | Payment confirmed, queued for work |
| in_progress | Admin is working on it |
| completed | Done |
| canceled | Payment failed |

## Flow
```
User fills instructions textarea → submits form
  → POST /orders/{order}/edit-request
  → OrderController@requestEdit:
      1. Authorize: user owns order, order not pending_payment
      2. Validate instructions (required, max 3000)
      3. Create EditRequest { instructions, status: pending_payment }
      4. Create Payment { amount: 400, status: pending }
      5. YooKassaService::createEditPayment() → redirect to YooKassa

YooKassa webhook (payment.succeeded)
  → EditRequest: status → paid
  → Order: status → sent_for_revision
  → OrderStatusLog: created with comment "Оплачена правка заказа"

YooKassa webhook (payment.canceled)
  → EditRequest: status → canceled
```

## Admin Side
Edit requests visible in Filament at `/admin/edit-requests`.
Admin can change status: paid → in_progress → completed.

## YooKassa Payment Description
`"Правка заказа #{order_id} — {performer_name}"`
