# Gift Certificates

## Overview
Users purchase a certificate for a fixed amount. They receive a unique code which can be given to anyone. The recipient enters the code at order checkout for a full or partial discount.

## Routes
```
GET  /certificates        → GiftCertificateController@index   [public]
POST /certificates        → GiftCertificateController@store   [auth]
```

## Controller
`app/Http/Controllers/GiftCertificateController.php`

## Views
- `resources/views/certificates/index.blade.php` — purchase page

## Database
Table: `gift_certificates`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| code | string | unique, uppercase, generated after payment |
| amount_rub | int | Certificate value in rubles |
| is_used | boolean | default false |
| buyer_user_id | bigint | nullable FK → users |
| used_by_order_id | bigint | nullable FK → orders |
| used_at | timestamp | nullable |
| created_at / updated_at | timestamps | |

## Model: `App\Models\GiftCertificate`
```php
buyer()        // belongsTo User (buyer_user_id)
usedByOrder()  // belongsTo Order (used_by_order_id)
payment()      // morphOne Payment
```

`User::giftCertificates()` → hasMany GiftCertificate (buyer_user_id)

## Purchase Flow (TODO — Phase 5)
```
User selects amount on /certificates page → submits form
  → GiftCertificateController@store:
      1. Validate amount (integer, min 100)
      2. Create GiftCertificate { amount_rub, buyer_user_id, is_used: false }
         (code is NULL at this point)
      3. Create Payment { amount, status: pending }
      4. YooKassaService::createCertificatePayment() → redirect to YooKassa

YooKassa webhook (payment.succeeded)
  → Generate unique code (12-char base64 alphanumeric, uppercase)
  → GiftCertificate: code = generated_code
  → TODO: send code by email to buyer
  → TODO: show code on success page
```

Code generation (in `PaymentController::handleSucceeded`):
```php
$code = strtoupper(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 12));
```

## Applying at Checkout
In `OrderController@store`:
1. User enters certificate code in `gift_certificate_code` field
2. Looked up: `GiftCertificate::where('code', strtoupper($code))->where('is_used', false)->first()`
3. Error if not found or already used
4. Discount = `min($cert->amount_rub, $priceAfterPromo)`
5. Code stored as string in `orders.gift_certificate_code` (not FK)

On payment success (`OrderService::confirm()`):
```php
GiftCertificate::where('code', $order->gift_certificate_code)
    ->where('is_used', false)
    ->update(['is_used' => true, 'used_by_order_id' => $order->id, 'used_at' => now()])
```

If certificate covers full price → order confirmed immediately, no YooKassa redirect.

## Purchase Page Amounts
Pre-set options: 600 ₽, 5 000 ₽, 15 000 ₽ (matching plan prices), plus custom amount input.

## Admin: Filament
Resource: `app/Filament/Resources/GiftCertificateResource.php`
Read-only list showing: code, amount, is_used, buyer name, used_by order, dates.

## Discount Priority
1. Promo code discount applied first (% off base price)
2. Gift certificate applied second (fixed amount off remaining price)
