# Layout & Frontend

## Overview
Bootstrap 5 via CDN. No Tailwind. No npm CSS build. Server-rendered Blade templates only.

## Base Layout
`resources/views/layouts/app.blade.php`

### Sections
- `@yield('title')` — page `<title>` tag, defaults to "Создаём песни"
- `@yield('content')` — main page content
- `@stack('styles')` — additional CSS per page
- `@stack('scripts')` — additional JS per page (before `</body>`)

### CDN Dependencies (in layout)
```html
Bootstrap 5.3.3 CSS + JS bundle
Bootstrap Icons 1.11.3
```

### Navbar
- Brand: "🎵 Создаём песни" → home
- Links: Главная, Заказать песню, Подарочные сертификаты
- Auth-aware:
  - Guest: Войти, Регистрация
  - Logged in: {user name} → profile, Выйти (POST form)
- Active link detection via `request()->routeIs('route.name')`

### Flash Messages
Displayed between navbar and content:
```php
session('success')  // green alert
session('error')    // red alert
```
Both are dismissible via Bootstrap `btn-close`.

### Footer
Links: Политика конфиденциальности, Пользовательское соглашение, ИНН, Контакты

## Page Structure
All pages use `<div class="container">` inside `<main class="flex-grow-1 py-4">`.
Footer is pushed to bottom via flexbox: `<body class="d-flex flex-column min-vh-100">`.

## Forms
- Method spoofing: `@method('PATCH')`, `@method('PUT')`, `@method('DELETE')`
- CSRF: `@csrf` in every form
- Validation errors: `@error('field')` → `is-invalid` class + `<div class="invalid-feedback">`
- Old values: `old('field', $default)`

## JavaScript
Minimal, inline per page via `@push('scripts')`. No build step, no npm.

Examples:
- Order form: show/hide Plan 3 fields on radio change
- Order show: auto-scroll chat to bottom on load
- Certificates page: show/hide custom amount input

## Key Views
| View | Route | Notes |
|---|---|---|
| `home.blade.php` | `/` | Landing, plan cards, how-it-works |
| `auth/register.blade.php` | `/register` | |
| `auth/login.blade.php` | `/login` | |
| `profile/index.blade.php` | `/profile` | Orders list + settings |
| `orders/create.blade.php` | `/order` | Order form, dynamic plan 3 fields |
| `orders/show.blade.php` | `/orders/{id}` | Full order detail |
| `certificates/index.blade.php` | `/certificates` | Certificate purchase |
| `pages/contact.blade.php` | `/contact` | Contact form |
| `pages/privacy.blade.php` | `/privacy` | |
| `pages/terms.blade.php` | `/terms` | |
| `pages/inn.blade.php` | `/inn` | |
