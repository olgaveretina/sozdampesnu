# Orders

## Overview
Core feature. Users submit song lyrics + style (plans 1–2) or videoclip details (plan 3) → pay → admin works on the order → files appear in personal account.

## Routes
```
GET   /order                        → OrderController@create          [auth]
POST  /order                        → OrderController@store           [auth]
GET   /orders/{order}               → OrderController@show            [auth]
PATCH /orders/{order}/comment       → OrderController@updateComment   [auth]
POST  /orders/{order}/select        → OrderController@selectVersion   [auth]
POST  /orders/{order}/review        → OrderController@submitReview    [auth]
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
| plan | tinyint | 1, 2, or 3 |
| order_type | string | 'song' (plans 1–2) or 'video' (plan 3) |
| lyrics | text | nullable; song orders only |
| performer_name | string | shown as order title |
| song_name | string | nullable |
| music_style | text | nullable; song orders only |
| cover_description | text | nullable; video orders: used as video description |
| cover_image_path | string | nullable; song orders only |
| video_audio_path | string | nullable; video orders — user-uploaded audio file |
| video_images | json | nullable; video orders — array of up to 6 image paths |
| singer_description | text | nullable; video orders — artist/character description |
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

### Constants & Methods
```php
Order::STATUSES   // array of status => Russian label
Order::TYPES      // ['song' => 'Песня', 'video' => 'Видеоклип']
Order::plans()    // static method — returns config('plans') array: plan => ['name' => ..., 'price' => ...]
```

Plan names and prices are defined in `config/plans.php`. Edit that file to change them.

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
$order->typeLabel()    // returns Russian type label ('Песня' or 'Видеоклип')
```

## Order Form (`OrderController@store`) Logic

**Shared (all plans):**
1. Validate: performer_name, song_name, plan (1/2/3), disclaimer (required accepted)
2. Apply promo code (percentage off base price)
3. Apply gift certificate (subtract up to remaining price)
4. If `finalAmount == 0`: confirm order immediately (no payment needed)
5. Otherwise: create Order (pending_payment) + Payment (pending) → YooKassa redirect

**Song orders (plans 1 & 2) — `order_type = 'song'`:**
- Additional required fields: `lyrics`, `music_style`

**Video orders (plan 3) — `order_type = 'video'`:**
- Additional required fields: `singer_description`, `cover_description` (video description), `video_audio` (MP3/M4A/WAV, max 50 MB)
- Optional: `video_images[]` — up to 6 images (JPG/PNG/WebP, max 10 MB each); stored in `storage/app/public/video-images/`; paths saved as JSON array in `video_images` column

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
| publication_queue | В очереди на публикацию | |
| publishing | Публикация началась | |
| sent_to_distributor | Отправлен дистрибьютору | |
| approved_by_distributor | Одобрен дистрибьютором | |
| rejected_by_distributor | Отклонён дистрибьютором | |
| rejected_by_platforms | Отклонён площадками | |
| completed | Заказ выполнен | Final state |

## Pricing Plans
| Plan | Russian Name | Price | Type |
|---|---|---|---|
| 1 | Просто попробовать | 600 ₽ | Песня |
| 2 | Хочу профессиональную песню | 5 000 ₽ | Песня |
| 3 | Видеоклип | 15 000 ₽ | Видеоклип |

## Order Detail Page Features
- Audio files: HTML5 `<audio>` player per file (if uploaded by admin)
- Cover images: thumbnail grid (if uploaded by admin)
- **Song orders**: shows lyrics, music style
- **Video orders**: shows singer description, video description, uploaded audio player, uploaded images grid (clickable thumbnails)
- User comment: editable textarea saved via PATCH
- Status history: chronological log with admin comments
- Chat: message thread, user can send messages at any time
- Edit request form: 400 ₽, textarea for instructions
- Review form: shown only when status = completed and no review yet

## Authorization
All order actions check `$order->user_id === auth()->id()` — returns 403 otherwise.
