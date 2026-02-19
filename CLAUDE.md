# SongMaster — CLAUDE.md

Service: **"Превращаем ваши стихи в песню"** (Turning Your Poems into a Song)
All website text is **Russian only**. No translations, no i18n layer.

---

## Stack

- **Laravel 12** — backend, routing, auth (custom, no Breeze/Jetstream)
- **Bootstrap 5** — frontend via CDN (no Tailwind, no npm build for CSS)
- **Filament v3** — admin panel at `/admin`
- **YooKassa** (`yoomoney/yookassa-sdk-php`) — payments
- **Local disk** (`storage/app/public`) — audio files and cover images
- **Laravel Sail** (Docker) — development environment

---

## Running Commands

All artisan/composer commands run via Sail:

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail composer require some/package
./vendor/bin/sail artisan tinker
```

App is at **http://localhost**. Admin at **http://localhost/admin**.

---

## Environment Variables (`.env`)

```
YOOKASSA_SHOP_ID=...
YOOKASSA_SECRET_KEY=...

TELEGRAM_BOT_TOKEN=...
TELEGRAM_ADMIN_CHAT_ID=...
```

Config entries: `config/services.php` → `yookassa` and `telegram` keys.

---

## Key Files & Directories

```
app/
  Http/Controllers/
    Auth/RegisterController.php     # Custom registration (no email confirm)
    Auth/LoginController.php        # Custom login/logout
    OrderController.php             # Order form, show, upgrade, edit request, review
    PaymentController.php           # YooKassa webhook + success/cancel redirects
    ProfileController.php           # Profile settings (name, password, delete)
    ChatController.php              # User → admin chat messages
    GiftCertificateController.php   # Gift certificate purchase page
    ContactController.php           # Contact form
    HomeController.php              # Landing page

  Models/
    User.php              # is_admin boolean, implements FilamentUser
    Order.php             # STATUSES and PLANS constants, all relationships
    OrderFile.php         # type: audio|cover
    OrderStatusLog.php    # append-only log (no updated_at)
    ChatMessage.php       # is_admin flag
    Payment.php           # polymorphic (payable_type/payable_id)
    PromoCode.php         # isValid() method
    GiftCertificate.php   # code, amount_rub, is_used
    EditRequest.php       # PRICE = 400 constant
    OrderUpgrade.php      # from_plan, to_plan, amount
    Review.php            # is_published (admin publishes)

  Services/
    YooKassaService.php   # createPayment, createUpgradePayment, createEditPayment, getPayment, parseNotification
    OrderService.php      # confirm(Order), cancel(Order)

  Filament/Resources/
    OrderResource.php             # + Pages/ListOrders, ViewOrder
    OrderResource/RelationManagers/
      ChatMessagesRelationManager.php
      StatusLogsRelationManager.php
    UserResource.php              # + Pages/ListUsers
    PromoCodeResource.php         # Full CRUD + Pages/
    GiftCertificateResource.php   # Read-only list
    EditRequestResource.php       # List + status change action

resources/views/
  layouts/app.blade.php     # Bootstrap navbar, footer, flash messages
  home.blade.php            # Landing page (banner placeholder, plan cards)
  auth/register.blade.php
  auth/login.blade.php
  profile/index.blade.php   # Orders list + account settings
  orders/create.blade.php   # Order form with dynamic plan 3 fields
  orders/show.blade.php     # Audio player, covers, chat, status history, upgrade
  certificates/index.blade.php
  pages/contact.blade.php
  pages/privacy.blade.php
  pages/terms.blade.php
  pages/inn.blade.php

routes/web.php              # All routes (auth, orders, profile, payments, pages)
```

---

## Database Models & Relationships

### User
- `is_admin` (boolean) — gates Filament access via `canAccessPanel()`
- `orders()` hasMany, `chatMessages()` hasMany, `giftCertificates()` hasMany (buyer_user_id)

### Order
- `plan`: 1, 2, or 3
- `status`: see statuses below
- `promo_code_id` nullable FK → PromoCode
- `gift_certificate_code` nullable string (code stored as string, not FK)
- `discount_amount`, `amount_paid` in rubles (integer)
- `selected_audio_id`, `selected_cover_id` — user's choice for Plan 3 publication
- Relationships: user, files, audioFiles, coverFiles, statusLogs, chatMessages, promoCode, payment (morphOne), editRequests, upgrades, review

### Payment (polymorphic)
- `payable_type` / `payable_id` → can be: Order, OrderUpgrade, EditRequest, GiftCertificate
- `status`: pending | succeeded | canceled
- `yookassa_id` unique, nullable until created in YooKassa

### OrderUpgrade
- Created when user pays to move from plan N to plan N+1
- `amount` = price difference (e.g. plan 1→2: 4400 ₽, plan 2→3: 10000 ₽)
- On webhook success: order `plan` incremented, status → `sent_for_revision`

### EditRequest
- PRICE constant = 400 ₽
- On webhook success: status → `paid`, order status → `sent_for_revision`

### GiftCertificate
- Purchased by user via YooKassa; `code` generated in webhook on success
- Applied at order checkout as a discount (partial or full)

---

## Pricing Plans

| # | Name (RU) | Price |
|---|---|---|
| 1 | Просто попробовать | 600 ₽ |
| 2 | Хочу профессиональную песню | 5 000 ₽ |
| 3 | Хочу профессиональную песню и публикацию на площадках | 15 000 ₽ |

Defined in `Order::PLANS` constant.

Plan 3 requires one of: cover description text, uploaded image, or ready 3000×3000 px cover.

---

## Order Statuses

Internal only (not shown to users as active statuses):
- `pending_payment` — order created, awaiting payment
- `canceled` — payment was not completed

User-visible statuses (defined in `Order::STATUSES`):
- `new` → `in_progress` → `generated` → `sent_for_revision` → `under_revision`
- `publication_queue` → `publishing` → `sent_to_distributor`
- `approved_by_distributor` → `rejected_by_distributor` / `rejected_by_platforms`
- `completed`

---

## Payment Flow

```
User submits order form
  → OrderController@store validates + calculates discount
  → Order created (status: pending_payment)
  → Payment record created (status: pending)
  → YooKassaService::createPayment() → redirect to YooKassa

YooKassa calls POST /payments/webhook
  → PaymentController::webhook() parses notification
  → Re-fetches payment from API (security: prevents spoofing)
  → Updates Payment status
  → If succeeded:
      Order       → OrderService::confirm() → status: new, promo/cert side-effects
      OrderUpgrade → plan++, status: sent_for_revision
      EditRequest  → status: paid, order: sent_for_revision
      GiftCertificate → code generated
  → If canceled:
      Order       → OrderService::cancel() → status: canceled
      OrderUpgrade/EditRequest → status: canceled

User returns to /payments/success?order={id}
  → Redirected to order detail page
```

Webhook is exempt from CSRF verification (configured in `routes/web.php`).

---

## Admin Panel (Filament)

URL: `/admin` — only `is_admin = true` users can access.

**Orders** — main resource:
- List with status/plan filters and search
- Actions: Change Status (modal with comment), Upload File (audio or cover)
- View page with full form + header actions + relation managers (Chat, Status History)

**Users** — list + toggle admin rights action

**Promo Codes** — full CRUD (code, discount %, max uses, active)

**Gift Certificates** — read-only list

**Edit Requests** — list + status change action

---

## Auth

Custom (no Breeze). Controllers in `App\Http\Controllers\Auth\`.
- No email confirmation
- `remember_me` supported on login
- Guest middleware protects register/login routes

---

## File Storage

- Disk: `public` (`storage/app/public`)
- Symlink: `public/storage` → `storage/app/public` (run `artisan storage:link`)
- Cover images (user uploads): `covers/`
- Admin-uploaded order files: `order-files/`
- Access in views: `Storage::url($file->path)`

---

## Telegram Notifications (Phase 6 — TODO)

Send via bot to `TELEGRAM_ADMIN_CHAT_ID` for:
- New user registration
- New order placed
- New user chat message

Config: `config/services.php` → `telegram.bot_token`, `telegram.admin_chat_id`

---

## Remaining Phases

### Phase 5 — Gift Certificate Purchase
- `GiftCertificateController@store`: validate amount, create GiftCertificate (no code yet), create Payment, redirect to YooKassa
- Webhook generates the code on success
- Send code by email to buyer

### Phase 6 — Notifications & Polish
- Telegram notifications (registration, new order, chat message)
- Contact form: send email + Telegram to admin
- Home page: fill in banner area
- INN page: fill in real company details

---

## Simplicity Decisions (do not change without discussion)

- No email confirmation on registration
- No queue workers — all operations are synchronous
- Bootstrap via CDN — no Tailwind, no npm CSS build step
- Russian only — no Laravel localization layer
- No separate API — everything is server-rendered Blade
