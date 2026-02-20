# SongMaster — Documentation Index

Service: **"Превращаем ваши стихи в песню"**

Use these files as context when working on specific features.

## Feature Docs

| File | What it covers |
|---|---|
| [authentication.md](authentication.md) | Registration, login, logout, User model, is_admin |
| [orders.md](orders.md) | Order form, statuses, plans, OrderController, Order model |
| [payments.md](payments.md) | YooKassa integration, webhook flow, Payment model, OrderService |
| [plan-upgrade.md](plan-upgrade.md) | Plan 1→2→3 upgrade flow, OrderUpgrade model |
| [edit-requests.md](edit-requests.md) | 400 ₽ edit requests, EditRequest model |
| [promo-codes.md](promo-codes.md) | Discount codes, PromoCode model, checkout logic |
| [gift-certificates.md](gift-certificates.md) | Certificate purchase & redemption, GiftCertificate model |
| [admin-panel.md](admin-panel.md) | Filament resources, status changes, file uploads, chat |
| [profile.md](profile.md) | Personal account, settings (name/password/delete) |
| [chat.md](chat.md) | User↔admin chat per order, ChatMessage model |
| [order-files.md](order-files.md) | Audio/cover file upload, storage, player display |
| [status-history.md](status-history.md) | OrderStatusLog, when entries are created |
| [reviews.md](reviews.md) | Post-completion reviews, rating, publishing |
| [telegram-notifications.md](telegram-notifications.md) | Bot notifications (TODO Phase 6) |
| [contact-form.md](contact-form.md) | Contact form, delivery (TODO Phase 6) |
| [static-pages.md](static-pages.md) | Home, Privacy, Terms, INN pages |
| [layout-and-frontend.md](layout-and-frontend.md) | Bootstrap layout, Blade conventions, flash messages |

## Quick Reference

### Run commands
```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker
./vendor/bin/sail composer require vendor/package
```

### Key URLs
- App: http://localhost
- Admin: http://localhost/admin (admin@test.com / password)

### Order Status Flow
```
pending_payment → new → in_progress → generated
  → sent_for_revision → under_revision
  → publication_queue → publishing → sent_to_distributor
  → approved_by_distributor → completed
                           → rejected_by_distributor
                           → rejected_by_platforms
```

### Pricing
- Plan 1: 600 ₽ (Просто попробовать)
- Plan 2: 5 000 ₽ (Хочу профессиональную песню)
- Plan 3: 6 500 ₽ (Проработанная песня + помощь в публикации)
- Edit request: 400 ₽
- Plan 1→2 upgrade: 4 400 ₽
- Plan 2→3 upgrade: 1 500 ₽

> Plan names and prices are configured in `config/plans.php`.
