# Order Files

## Overview
Admin uploads audio files and cover images for an order via Filament. Files appear in the user's order detail page.

## Database
Table: `order_files`
| Column | Type | Notes |
|---|---|---|
| id | bigint | PK |
| order_id | bigint | FK → orders (cascade delete) |
| type | string | `audio` or `cover` |
| path | string | Relative path in public disk |
| label | string | nullable, e.g. "Версия 1" |
| created_at / updated_at | timestamps | |

## Model: `App\Models\OrderFile`
```php
order()  // belongsTo Order
```

Relationships on Order:
```php
files()       // hasMany OrderFile (all types)
audioFiles()  // hasMany OrderFile where type = 'audio'
coverFiles()  // hasMany OrderFile where type = 'cover'
```

## Storage
- Disk: `public` (`storage/app/public`)
- Directory: `order-files/`
- Symlink: `public/storage` → `storage/app/public` (created via `artisan storage:link`)
- Access URL: `Storage::url($file->path)`

## Admin Upload (Filament)
Two places to upload:
1. **List page** — row action "Загрузить файл" on each order
2. **View page** — header action "Загрузить файл"

Upload modal fields:
- Type: select (audio / cover)
- Label: text input (optional, e.g. "Версия 2")
- File: file upload component → stored to `public` disk, `order-files/` directory

After upload, `OrderFile` record is created.

## User Display (`orders/show.blade.php`)

### Audio Files
Shown as HTML5 `<audio controls>` players:
```blade
<audio controls class="w-100">
    <source src="{{ Storage::url($file->path) }}" type="audio/mpeg">
</audio>
```
Label shown above each player (e.g. "Версия 1").

### Cover Images
Shown as thumbnail grid (`col-6 col-md-3`):
```blade
<img src="{{ Storage::url($file->path) }}" class="img-fluid rounded">
```

### Plan 3 Selection
For Plan 3 orders, each audio file and cover shows a "Выбрать эту версию" button.
Clicking submits a hidden form to `POST /orders/{order}/select` with `selected_audio_id` or `selected_cover_id`.
Selected state shown with green "✓ Выбрана" button.
Selection stored in `orders.selected_audio_id` and `orders.selected_cover_id`.

## Accepted File Types
- Audio: any format (no server-side restriction in current implementation — MIME validation can be added)
- Cover: JPG/PNG (validated in order form upload for user-submitted covers)

## User Cover Upload
When placing a Plan 3 order, the user can upload a ready-made cover (3000×3000 px):
- Field: `cover_image` in order form
- Validation: `image`, `mimes:jpeg,png`, `max:20480` (20 MB)
- Stored in: `covers/` directory (separate from admin-uploaded files)
- Path saved to: `orders.cover_image_path`
