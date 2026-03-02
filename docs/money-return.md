# Money Return (Order Rejection & Refund)

## Overview
Admin can reject any active order with a full refund to the customer. The customer sees the reason in their order's status history.

A refund guarantee notice is shown on the landing page (`resources/views/home.blade.php`, bottom section).

## Admin Action: "Отклонить заказ"

Available in two places:
- Orders list page (`/admin/orders`) — row action
- Order view page (`/admin/orders/{id}`) — header action

Hidden for orders in terminal or pre-payment states: `rejected`, `canceled`, `pending_payment`, `completed`.

### Steps
1. Admin clicks **"Отклонить заказ"**
2. Modal opens — admin enters a required comment (reason for rejection)
3. On submit:
   - If `payment.status = succeeded` and `amount_paid > 0` → `YooKassaService::createRefund()` is called
   - If refund API call fails → error notification shown, order status is **not** changed
   - If refund succeeds (or `amount_paid = 0`) → order status set to `rejected`, status log created with the comment
4. Customer sees status **"Не сможем выполнить"** and the comment in their order page

## Order Status: `rejected`

| Key | Label |
|---|---|
| `rejected` | Не сможем выполнить |

Added to `Order::STATUSES`. Displayed with a red (`danger`) badge in the admin panel.

## Refund Logic

Handled in `YooKassaService::createRefund(string $yookassaPaymentId, int $amountRub, string $description)`.

Calls `$client->createRefund()` with the full `amount_paid` in rubles against the original YooKassa payment ID.

After a successful API call, `payment.status` is updated to `refunded`.

**Edge cases:**
- `amount_paid = 0` (order was fully covered by promo code / gift certificate) — no refund call, order is simply rejected
- Refund API error — shown as a danger notification; order status remains unchanged (no partial state)

## Files Changed

| File | Change |
|---|---|
| `app/Models/Order.php` | Added `rejected` to `STATUSES` |
| `app/Services/YooKassaService.php` | Added `createRefund()` method |
| `app/Filament/Resources/OrderResource.php` | Added reject action to table; `rejected` badge color |
| `app/Filament/Resources/OrderResource/Pages/ViewOrder.php` | Added reject action to header |
| `resources/views/home.blade.php` | Refund guarantee block at bottom of landing page |
