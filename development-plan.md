# Development Plan: SongMaster
### "Turning Your Poems into a Song"

## Stack
- **Laravel** (backend + routing + auth)
- **Bootstrap** (frontend)
- **Filament** (admin panel)
- **YooKassa** (payments)
- **Local disk** (file storage for audio/covers)

---

## Database Models

| Model | Key Fields |
|---|---|
| `User` | name, email, password |
| `Order` | user_id, lyrics, performer_name, music_style, plan (1/2/3), cover_description, cover_image, status, promo_code_id |
| `OrderFile` | order_id, type (audio/cover), path, label |
| `OrderStatusLog` | order_id, status, comment |
| `ChatMessage` | order_id, user_id, is_admin, body |
| `Payment` | payable_type, payable_id, amount, yookassa_id, status |
| `PromoCode` | code, discount_percent, max_uses, used_count |
| `GiftCertificate` | code, amount_rub, is_used, buyer_user_id, used_by_order_id, payment_id |
| `EditRequest` | order_id, instructions, payment_id, status |
| `Review` | order_id, user_id, text, rating |

---

## Phase 1 — Foundation
- Migrations & models with relationships
- Registration (email, name, password) + login/logout
- Base Bootstrap layout: navbar, footer, flash messages

## Phase 2 — Order Form & Payments
- Order form page: lyrics, performer, style, plan selector (radio)
  - Plan 3 reveals extra fields: cover description / image upload / ready cover
  - Promo code field + gift certificate code field
  - Disclaimer checkbox
- YooKassa integration (`yookassa/yookassa-sdk-php`)
- Payment webhook handler (update payment status → update order)
- After payment: order created with status "New", user redirected to personal account

## Phase 3 — Personal Account
- Orders list: title (auto from performer name), date, status, plan
- Order detail page:
  - Status history with admin comments
  - Audio files player (if uploaded)
  - Cover images (if uploaded)
  - User comment field
  - For Plan 3: song + cover selection buttons
  - Upgrade plan button (if not highest tier) → YooKassa payment
  - Edit request form (400 RUB): textarea for instructions → YooKassa
  - Review form (when status = Completed)
  - Chat dialog (always visible)
- Profile settings: change name, change password, delete account

## Phase 4 — Admin Panel (Filament)
- Users resource (list, view)
- Orders resource:
  - List with filters (status, plan, date)
  - Order detail: change status + comment, upload audio files, upload covers, view selection by user
  - Chat panel per order
- Promo codes CRUD (code, discount %, max uses)
- Gift certificates list (view purchases, usage)
- Edit requests list

## Phase 5 — Gift Certificates
- "Gift Certificates" page: choose amount (600 / 5000 / 15000 / custom RUB), pay via YooKassa
- After payment: generate unique code, display to buyer + send by email
- Code is applied in order form for full or partial payment

## Phase 6 — Notifications & Remaining Pages
- **Telegram bot**: notify admin on new registration, new order, new chat message
- **Home page**: landing with hero, plan cards, CTA button
- **Contact form**: sends email + Telegram to admin
- **Static pages**: Privacy Policy, Terms of Use, INN page

---

## Order Statuses

```
new → in_progress → generated → sent_for_revision → under_revision
  → publication_queue → publishing → sent_to_distributor
  → approved_by_distributor → rejected_by_distributor
  → rejected_by_platforms → completed
```

| Status | Russian Label |
|---|---|
| new | Новый |
| in_progress | В работе |
| generated | Песня сгенерирована |
| sent_for_revision | Отправлен на доработку |
| under_revision | На доработке |
| publication_queue | В очереди на публикацию |
| publishing | Публикация началась |
| sent_to_distributor | Отправлен дистрибьютору |
| approved_by_distributor | Одобрен дистрибьютором |
| rejected_by_distributor | Отклонён дистрибьютором |
| rejected_by_platforms | Отклонён площадками |
| completed | Заказ выполнен |

---

## Pricing Plans

| Plan | Price | Description |
|---|---|---|
| 1 — Just Try It | 600 RUB | 4 AI versions, no refinement |
| 2 — I Want a Great Song | 5 000 RUB | 4 versions with refinement |
| 3 — Great Song + Publication | 15 000 RUB | Refinement + Yandex Music & platforms |

---

## Simplicity Decisions
- No email confirmation on registration
- No queue workers (sync mail/Telegram — simple enough for low traffic)
- No separate front-end build (Bootstrap via CDN or simple Vite setup)
- Russian only throughout (no i18n layer)
