# Payments (YooKassa)

## Overview
All payments go through YooKassa. The `payments` table is polymorphic — one table handles order payments, plan upgrades, edit requests, and gift certificate purchases.

## Routes
```
POST /payments/webhook   → PaymentController@webhook   [no CSRF]
GET  /payments/success   → PaymentController@success
GET  /payments/cancel    → PaymentController@cancel
```

## Controller
`app/Http/Controllers/PaymentController.php`

## Service
`app/Services/YooKassaService.php`

Methods:
- `createPayment(Payment, Order)` — initial order payment
- `createUpgradePayment(Payment, Order, OrderUpgrade)` — plan upgrade
- `createEditPayment(Payment, Order, EditRequest)` — edit request
- `getPayment(string $yookassaId)` — re-fetch from API (webhook security)
- `parseNotification(array $body)` — parse webhook JSON via SDK

## Config
```php
// config/services.php
'yookassa' => [
    'shop_id'    => env('YOOKASSA_SHOP_ID'),
    'secret_key' => env('YOOKASSA_SECRET_KEY'),
]
```

Package: `yoomoney/yookassa-sdk-php` (namespace: `YooKassa\`)

## Database
Table: `payments`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| payable_type | string | Model class name |
| payable_id | bigint | Model ID |
| amount | int | Rubles |
| yookassa_id | string | nullable, unique — assigned after createPayment() |
| status | string | pending / succeeded / canceled |
| yookassa_data | json | nullable — full API response stored on webhook |
| created_at / updated_at | timestamps | |

## Model: `App\Models\Payment`
```php
payable()  // morphTo — can be Order, OrderUpgrade, EditRequest, GiftCertificate
```

## Polymorphic Payable Types
| Payable | Triggered by | On success |
|---|---|---|
| `Order` | Order form submission | `OrderService::confirm()` → status: new |
| `OrderUpgrade` | Upgrade button on order page | plan++, status: sent_for_revision |
| `EditRequest` | Edit request form | EditRequest status: paid, order: sent_for_revision |
| `GiftCertificate` | Gift certificate purchase | Code generated and stored |

## Full Payment Flow

### Creating a Payment
```
1. Create payable record (Order, OrderUpgrade, etc.) with status pending_payment
2. Create Payment record { amount, status: pending }
3. Call YooKassaService::create*Payment() → stores yookassa_id → returns confirmation URL
4. Redirect user to YooKassa confirmation URL
```

### Webhook Processing
```
POST /payments/webhook (JSON body from YooKassa)
1. Parse notification body via SDK NotificationFactory
2. Extract yookassa_id from notification object
3. Re-fetch payment from YooKassa API (prevents webhook spoofing)
4. Find Payment by yookassa_id
5. Update Payment { status, yookassa_data }
6. Dispatch to handleSucceeded() or handleCanceled()
```

### handleSucceeded
- **Order**: `OrderService::confirm()` → status=new, log entry, promo+cert side-effects
- **OrderUpgrade**: mark paid, update order plan and status
- **EditRequest**: mark paid, update order status to sent_for_revision
- **GiftCertificate**: generate unique code

### handleCanceled
- **Order**: `OrderService::cancel()` → status=canceled, log entry
- **OrderUpgrade** / **EditRequest**: mark canceled

### Return URLs
- `GET /payments/success?order={id}` → redirect to order detail with success message
- `GET /payments/cancel?order={id}` → redirect to order form with error message

## OrderService
`app/Services/OrderService.php`
```php
confirm(Order $order)
  // Sets status → new
  // Creates status log entry
  // Increments promo_code.used_count if applied
  // Marks gift_certificate as used (is_used=true, used_by_order_id, used_at)

cancel(Order $order)
  // Sets status → canceled
  // Creates status log entry
```

## Security
Webhook re-fetches payment from YooKassa API before acting — prevents spoofed webhook attacks.
Webhook route is exempt from CSRF verification (configured in `routes/web.php`).

## YooKassa Payment Metadata
Each payment is created with `metadata.payment_db_id` = local Payment ID.
The `return_url` includes `?order={id}` for post-payment redirect.
