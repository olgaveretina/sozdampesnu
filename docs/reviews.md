# Reviews

## Overview
Users can leave a text review (and optional 1–5 star rating) after their order reaches `completed` status. One review per order. Admin can publish reviews (make them public).

## Route
```
POST /orders/{order}/review  → OrderController@submitReview  [auth]
```

## Controller Method
`app/Http/Controllers/OrderController@submitReview`

## View
Review form in `resources/views/orders/show.blade.php`.

Shown only when:
- `$order->status === 'completed'`
- `!$order->review` (no review submitted yet)

## Database
Table: `reviews`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| order_id | bigint | unique FK → orders (one review per order) |
| user_id | bigint | FK → users |
| text | text | Review content |
| rating | tinyint | nullable, 1–5 |
| is_published | boolean | default false — admin publishes |
| created_at / updated_at | timestamps | |

## Model: `App\Models\Review`
```php
order()  // belongsTo Order
user()   // belongsTo User
```

`Order::review()` → hasOne Review

## Submit Flow
```
POST /orders/{order}/review
  1. Authorize: user owns order
  2. Authorize: order.status === 'completed'
  3. Authorize: no review exists yet
  4. Validate: text (required, max 3000), rating (nullable, 1–5 integer)
  5. Create Review { order_id, user_id, text, rating }
  6. Redirect back with success flash "Отзыв отправлен. Спасибо!"
```

## Admin Publishing (TODO)
`reviews.is_published` flag exists for admin to approve reviews before showing them publicly.
Currently no Filament resource for reviews — add one when needed.

To add to admin panel:
- Create `ReviewResource` in `app/Filament/Resources/`
- Toggle `is_published` action
- Show published reviews on home page or a reviews page
