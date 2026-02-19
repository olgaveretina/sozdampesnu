# Static Pages

## Overview
Informational pages with no dynamic data. Routes return views directly.

## Routes
```
GET /privacy  → view('pages.privacy')   — Политика конфиденциальности
GET /terms    → view('pages.terms')     — Пользовательское соглашение
GET /inn      → view('pages.inn')       — Реквизиты (INN)
```

## Views
- `resources/views/pages/privacy.blade.php`
- `resources/views/pages/terms.blade.php`
- `resources/views/pages/inn.blade.php`

All extend `layouts/app.blade.php`.

## Privacy Policy (`/privacy`)
Covers: data collection, usage, payment data (YooKassa), user rights, contacts.
Content is placeholder — **fill in with real legal text before launch**.

## Terms of Use (`/terms`)
Covers: scope, text ownership disclaimer, rights to finished songs, refund policy (50% for Plan 3 if rejected), limitation of liability.
Content is placeholder — **fill in with real legal text before launch**.

## INN Page (`/inn`)
Shows company legal details:
- Company name
- INN (ИНН)
- OGRN/OGRNIP (ОГРН/ОГРНИП)
- Email

Currently shows placeholder "— (заполнить)" values.
**Fill in real details before launch.**

## Footer Links
All static pages are linked in the footer of `layouts/app.blade.php`.
Contact page (`/contact`) is also in the footer.

## Home Page (`/`)
Route: `GET / → HomeController@index → view('home')`
View: `resources/views/home.blade.php`

Contains:
- Hero banner section (dark background, placeholder area for real banner)
- Plan cards (3 plans with prices and descriptions)
- "How it works" section (4 steps)

**The banner area** is a `div` with `min-height: 280px` — replace content or add an `<img>` tag when a real banner is ready.
