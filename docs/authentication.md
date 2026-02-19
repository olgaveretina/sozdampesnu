# Authentication

## Overview
Custom auth (no Breeze/Jetstream). Registration requires name, email, password only — no email confirmation.

## Routes
```
GET  /register  → Auth\RegisterController@create
POST /register  → Auth\RegisterController@store
GET  /login     → Auth\LoginController@create
POST /login     → Auth\LoginController@store
POST /logout    → Auth\LoginController@destroy   [auth middleware]
```

## Controllers
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/Auth/LoginController.php`

## Views
- `resources/views/auth/register.blade.php`
- `resources/views/auth/login.blade.php`

## Database
Table: `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| name | string | |
| email | string | unique |
| is_admin | boolean | default false — gates Filament access |
| password | string | hashed (bcrypt) |
| remember_token | string | nullable |
| email_verified_at | timestamp | nullable, unused |
| created_at / updated_at | timestamps | |

## Model: `App\Models\User`
- Implements `FilamentUser` — `canAccessPanel()` returns `$this->is_admin`
- `$fillable`: name, email, password, is_admin
- Relationships: `orders()`, `chatMessages()`, `giftCertificates()`

## Behavior
- Registration: validates → creates user → `Auth::login($user)` → redirect to home
- Login: `Auth::attempt($credentials, $remember)` → `session()->regenerate()` → redirect to intended or profile
- Logout: `Auth::logout()` → invalidate + regenerate token → redirect to home
- Guest middleware protects `/register` and `/login`

## Telegram Notification (TODO — Phase 6)
On successful registration, notify admin via Telegram bot.
