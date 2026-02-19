# Orders

## Overview
Core feature. Users submit song lyrics + style → pay → admin works on the song → files appear in personal account.

## Routes
```
GET   /order                        → OrderController@create          [auth]
POST  /order                        → OrderController@store           [auth]
GET   /orders/{order}               → OrderController@show            [auth]
PATCH /orders/{order}/comment       → OrderController@updateComment   [auth]
POST  /orders/{order}/select        → OrderController@selectVersion   [auth]
POST  /orders/{order}/review        → OrderController@submitReview    [auth]
POST  /orders/{order}/upgrade       → OrderController@upgrade         [auth]
POST  /orders/{order}/edit-request  → OrderController@requestEdit     [auth]
POST  /orders/{order}/chat          → ChatController@store            [auth]
```

## Controller
`app/Http/Controllers/OrderController.php`

## Views
- `resources/views/orders/create.blade.php` — order form
- `resources/views/orders/show.blade.php` — order detail page

## Database
Table: `orders`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| user_id | bigint | FK → users |
| lyrics | text | |
| performer_name | string | shown as order title |
| music_style | text | |
| plan | tinyint | 1, 2, or 3 |
| cover_description | text | nullable, Plan 3 only |
| cover_image_path | string | nullable, user-uploaded cover |
| status | string | see statuses below |
| promo_code_id | bigint | nullable FK → promo_codes |
| gift_certificate_code | string | nullable, applied cert code |
| discount_amount | int | rubles, total discount applied |
| amount_paid | int | rubles, actual charge |
| user_comment | text | nullable, editable by user |
| selected_audio_id | bigint | nullable, user's pick for Plan 3 |
| selected_cover_id | bigint | nullable, user's pick for Plan 3 |
| created_at / updated_at | timestamps | |

## Model: `App\Models\Order`

### Constants
```php
Order::STATUSES  // array of status => Russian label
Order::PLANS     // array of plan => ['name' => ..., 'price' => ...]
```

### Relationships
```php
user()           // belongsTo User
files()          // hasMany OrderFile
audioFiles()     // hasMany OrderFile where type=audio
coverFiles()     // hasMany OrderFile where type=cover
statusLogs()     // hasMany OrderStatusLog, ordered by created_at
chatMessages()   // hasMany ChatMessage, ordered by created_at
promoCode()      // belongsTo PromoCode
payment()        // morphOne Payment
editRequests()   // hasMany EditRequest
upgrades()       // hasMany OrderUpgrade
review()         // hasOne Review
```

### Helpers
```php
$order->statusLabel()  // returns Russian status label
$order->planLabel()    // returns Russian plan name
```

## Order Form (`OrderController@store`) Logic
1. Validate: lyrics, performer_name, music_style, plan (1/2/3), disclaimer (required accepted)
2. Plan 3: require at least one of cover_description or cover_image
3. Apply promo code (percentage off base price)
4. Apply gift certificate (subtract up to remaining price)
5. If `finalAmount == 0`: confirm order immediately (no payment needed)
6. Otherwise: create Order (pending_payment) + Payment (pending) → YooKassa redirect

## Order Statuses
| Status | Russian | Notes |
|---|---|---|
| pending_payment | Ожидает оплаты | Internal — awaiting payment |
| canceled | Отменён | Internal — payment not completed |
| new | Новый | In queue |
| in_progress | В работе | Employee started |
| generated | Песня сгенерирована | Files uploaded to account |
| sent_for_revision | Отправлен на доработку | Waiting in revision queue |
| under_revision | На доработке | Employee revising |
| publication_queue | В очереди на публикацию | Plan 3 only |
| publishing | Публикация началась | Plan 3 only |
| sent_to_distributor | Отправлен дистрибьютору | Plan 3 only |
| approved_by_distributor | Одобрен дистрибьютором | Plan 3 only |
| rejected_by_distributor | Отклонён дистрибьютором | Plan 3 only |
| rejected_by_platforms | Отклонён площадками | Plan 3 only |
| completed | Заказ выполнен | Final state |

## Pricing Plans
| Plan | Russian Name | Price |
|---|---|---|
| 1 | Просто попробовать | 600 ₽ |
| 2 | Хочу профессиональную песню | 5 000 ₽ |
| 3 | Хочу профессиональную песню и публикацию | 15 000 ₽ |

## Order Detail Page Features
- Audio files: HTML5 `<audio>` player per file (if uploaded by admin)
- Cover images: thumbnail grid (if uploaded by admin)
- Plan 3: buttons to select preferred audio + cover version for publication
- User comment: editable textarea saved via PATCH
- Status history: chronological log with admin comments
- Chat: message thread, user can send messages at any time
- Plan upgrade button: shown if plan < 3 and status not pending_payment/canceled
- Edit request form: 400 ₽, textarea for instructions
- Review form: shown only when status = completed and no review yet

## Authorization
All order actions check `$order->user_id === auth()->id()` — returns 403 otherwise.
