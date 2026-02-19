# Contact Form

## Overview
Public contact form. Sends the message to admin via email and/or Telegram.

## Routes
```
GET  /contact  → ContactController@index
POST /contact  → ContactController@store
```

## Controller
`app/Http/Controllers/ContactController.php`

## View
`resources/views/pages/contact.blade.php`

Fields:
- Name (pre-filled from auth user if logged in)
- Email (pre-filled from auth user if logged in)
- Message (textarea, max 5000 chars)

## Validation
```php
'name'    => required, string, max:255
'email'   => required, email
'message' => required, string, max:5000
```

## Current State
On success: redirects back with flash `"Сообщение отправлено. Мы свяжемся с вами в ближайшее время."`

Actual sending (email + Telegram) is a TODO for Phase 6.

## TODO — Phase 6
Add actual delivery:
```php
// Option 1: Email to admin
Mail::raw("От: {$data['name']} <{$data['email']}>\n\n{$data['message']}", function($msg) {
    $msg->to(config('mail.from.address'))->subject('Новое сообщение с сайта');
});

// Option 2: Telegram
app(TelegramService::class)->notify(
    "📩 Контактная форма\nОт: {$data['name']} ({$data['email']})\n\n{$data['message']}"
);
```
