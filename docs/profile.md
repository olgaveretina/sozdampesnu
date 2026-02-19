# User Profile

## Overview
Personal account page showing orders list and account settings (change name, password, delete account).

## Routes
```
GET    /profile           → ProfileController@index          [auth]
PATCH  /profile           → ProfileController@update         [auth]
PUT    /profile/password  → ProfileController@updatePassword [auth]
DELETE /profile           → ProfileController@destroy        [auth]
```

## Controller
`app/Http/Controllers/ProfileController.php`

## View
`resources/views/profile/index.blade.php`

Layout:
- Left column (col-lg-8): orders list
- Right column (col-lg-4): account settings cards

## Orders List
- Fetched: `auth()->user()->orders()->latest()->get()`
- Each row: performer name, plan label, status badge, created date
- Clicking a row links to `/orders/{order}`
- "New Order" button → `/order`

## Account Settings

### Change Name
```
PATCH /profile
Validates: name (required, string, max:255)
Updates: users.name
Redirects back with success flash
```

### Change Password
```
PUT /profile/password
Validates:
  current_password (required, current_password rule)
  password (required, min:8, confirmed)
Updates: users.password (hashed)
Redirects back with success flash
```

### Delete Account
```
DELETE /profile
Validates: password (required, current_password rule)
Confirmation dialog: "Вы уверены? Это действие необратимо."
Logs out user → deletes user record → invalidates session
Redirects to home with success flash
```

## Flash Messages
Uses `session('success')` and `session('error')` displayed in `layouts/app.blade.php`.
