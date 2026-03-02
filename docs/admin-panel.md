# Admin Panel (Filament)

## Overview
Built with Filament v3. Accessible at `/admin`. Only users with `is_admin = true` can log in.

## Access Control
`User::canAccessPanel(Panel $panel): bool` returns `$this->is_admin`.
Toggle admin status via UserResource action in the panel itself.

## Filament Config
`app/Providers/Filament/AdminPanelProvider.php`
- Panel ID: `admin`
- Path: `/admin`
- Login page: `/admin/login` (Filament's built-in)
- Auth guard: `web`
- Colors: Amber primary

## Resources

### Orders (`/admin/orders`)
File: `app/Filament/Resources/OrderResource.php`

**List page** (`ListOrders`):
- Columns: #, name, performer, client, plan, **type badge** (Песня/Видеоклип), status (colored badge), amount paid, date
- Filters: **type** (song/video), status (select), plan (select)
- Default sort: created_at desc
- Row actions:
  - **Change Status** — modal with status select + optional comment → updates order + creates status log
  - **Upload File** — modal with type (audio/cover), label, file upload → creates OrderFile
  - **View** → goes to ViewOrder page

**View page** (`ViewOrder`):
- Shows order form fields (read-only), always: performer, name, plan, **order type**, status, amount paid
- **Song orders** (plans 1–2): section "Текст и пожелания" — lyrics, music style
- **Video orders** (plan 3): section "Материалы видеоклипа" — singer/character description, video description, audio file link, image thumbnails (clickable)
- Shared: "Комментарий клиента" section
- Header actions: Change Status, Upload File (same as list row actions)
- Relation managers (tabs):
  - **Chat** (`ChatMessagesRelationManager`) — view all messages, reply as admin
  - **Status History** (`StatusLogsRelationManager`) — full chronological log

---

### Users (`/admin/users`)
File: `app/Filament/Resources/UserResource.php`

- Columns: #, name, email, is_admin (icon), orders count, registered date
- Search: name, email
- Action: **Toggle Admin** — flips `is_admin` with confirmation

---

### Promo Codes (`/admin/promo-codes`)
File: `app/Filament/Resources/PromoCodeResource.php`

Full CRUD. Form fields: code (auto-uppercased), discount_percent, max_uses, is_active toggle.

---

### Gift Certificates (`/admin/gift-certificates`)
File: `app/Filament/Resources/GiftCertificateResource.php`

Read-only list: code, amount, is_used, buyer, used_by order, dates. No actions.

---

### Edit Requests (`/admin/edit-requests`)
File: `app/Filament/Resources/EditRequestResource.php`

List with status change action (paid → in_progress → completed).

---

## Chat (Admin Reply)
In `ChatMessagesRelationManager`, admin creates a message via "Ответить клиенту":
```php
mutateFormDataUsing(fn($data) => array_merge($data, [
    'is_admin' => true,
    'user_id'  => auth()->id(),
]))
```
This sets `is_admin = true` so the user sees it as an admin reply.

## File Upload (by Admin)
Files are stored to `public` disk in `order-files/` directory.
OrderFile record created: `{ order_id, type: audio|cover, path, label }`.
Files are served via `Storage::url($file->path)` on the user's order page.

## Status Change Flow
1. Admin selects new status + optional comment
2. `$order->update(['status' => $data['status']])`
3. `$order->statusLogs()->create(['status' => ..., 'comment' => ...])`
4. Filament success notification shown

## Directory Structure
```
app/Filament/Resources/
  OrderResource.php
  OrderResource/
    Pages/
      ListOrders.php
      ViewOrder.php
    RelationManagers/
      ChatMessagesRelationManager.php
      StatusLogsRelationManager.php
  UserResource.php
  UserResource/Pages/ListUsers.php
  PromoCodeResource.php
  PromoCodeResource/Pages/
    ListPromoCodes.php
    CreatePromoCode.php
    EditPromoCode.php
  GiftCertificateResource.php
  GiftCertificateResource/Pages/ListGiftCertificates.php
  EditRequestResource.php
  EditRequestResource/Pages/ListEditRequests.php
```
