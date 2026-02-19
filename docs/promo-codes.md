# Promo Codes

## Overview
Fixed percentage discount codes. Applied by the user during order checkout. Admin creates and manages them via Filament.

## Database
Table: `promo_codes`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| code | string | Unique, stored uppercase |
| discount_percent | tinyint | 1–100 |
| max_uses | int | nullable — null = unlimited |
| used_count | int | default 0, incremented on order confirm |
| is_active | boolean | default true |
| created_at / updated_at | timestamps | |

## Model: `App\Models\PromoCode`
```php
isValid(): bool
  // Returns false if: not active, OR max_uses reached

orders()  // hasMany Order
```

## How It Works at Checkout
In `OrderController@store`:
1. User enters promo code in the order form
2. Looked up case-insensitively (`strtoupper()`)
3. `$promoCode->isValid()` checked — error shown if invalid
4. Discount calculated: `round(basePrice * discount_percent / 100)`
5. Discount applied before gift certificate
6. `promo_code_id` stored on the order

On payment success (`OrderService::confirm()`):
- `$order->promoCode->increment('used_count')` called

## Admin: Filament
Resource: `app/Filament/Resources/PromoCodeResource.php`
Pages: List, Create, Edit (with Delete action)

Fields in form:
- Code (auto-uppercased on input)
- Discount percent (1–100)
- Max uses (nullable = unlimited)
- Is active toggle

## Order Form
Field: `promo_code` (text input, optional)
Error shown on invalid/exhausted code: `"Промокод недействителен или исчерпан."`

## Interaction with Gift Certificates
Promo code discount applies first (on base price), then gift certificate applies to the remaining amount.
