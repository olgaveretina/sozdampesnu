# Plan Upgrade

## Overview
Users can upgrade their order from Plan 1→2 or Plan 2→3 by paying the price difference. After successful payment, the order plan is updated and sent for revision.

## Route
```
POST /orders/{order}/upgrade  → OrderController@upgrade  [auth]
```

## Controller Method
`app/Http/Controllers/OrderController@upgrade`

## View
Upgrade button/form in `resources/views/orders/show.blade.php`.

Shown when:
- `$order->plan < 3`
- `$order->status` is NOT `pending_payment` or `canceled`

Displays the exact price difference to pay.

## Database
Table: `order_upgrades`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| order_id | bigint | FK → orders |
| from_plan | tinyint | e.g. 1 |
| to_plan | tinyint | e.g. 2 (always from_plan + 1) |
| amount | int | Price difference in rubles |
| status | string | pending_payment / paid / canceled |
| created_at / updated_at | timestamps | |

## Model: `App\Models\OrderUpgrade`
```php
order()    // belongsTo Order
payment()  // morphOne Payment
```

`Order::upgrades()` → hasMany OrderUpgrade

## Upgrade Prices
| Upgrade | Amount |
|---|---|
| Plan 1 → Plan 2 | 4 400 ₽ (5000 − 600) |
| Plan 2 → Plan 3 | 10 000 ₽ (15000 − 5000) |

Calculated dynamically: `Order::PLANS[$toPlan]['price'] - Order::PLANS[$order->plan]['price']`

## Flow
```
User clicks "Улучшить тариф" button
  → POST /orders/{order}/upgrade
  → OrderController@upgrade:
      1. Authorize: user owns order, plan < 3, valid status
      2. Calculate toPlan = order.plan + 1, amount = price diff
      3. Create OrderUpgrade { from_plan, to_plan, amount, status: pending_payment }
      4. Create Payment { amount, status: pending }
      5. YooKassaService::createUpgradePayment() → redirect to YooKassa

YooKassa webhook (payment.succeeded)
  → PaymentController::handleSucceeded()
  → OrderUpgrade: status → paid
  → Order: plan → to_plan, status → sent_for_revision
  → OrderStatusLog: created with comment about plan upgrade
```

## YooKassa Payment Description
`"Улучшение тарифа заказа #{id} → {plan name}"`
