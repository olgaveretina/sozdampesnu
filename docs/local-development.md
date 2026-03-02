# Local Development — Start, Stop, Run

The project runs via **Laravel Sail** (Docker Compose wrapper).

---

## Prerequisites

- Docker Desktop (or Docker Engine) must be running before any Sail commands.

---

## Start the site

```bash
cd /home/overetina/projects/songmaster
./vendor/bin/sail up -d
```

`-d` runs containers in the background (detached mode).

The site will be available at **http://localhost**
Admin panel: **http://localhost/admin**

---

## Stop the site

```bash
./vendor/bin/sail down
```

This stops and removes the containers (data in the MySQL volume is preserved).

---

## Check status

```bash
./vendor/bin/sail ps
```

---

## Common commands while running

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Open tinker REPL
./vendor/bin/sail artisan tinker

# Install a Composer package
./vendor/bin/sail composer require vendor/package

# Clear caches
./vendor/bin/sail artisan optimize:clear

# View application logs
./vendor/bin/sail artisan pail
# or directly:
tail -f storage/logs/laravel.log
```

---

## Storage symlink (first-time setup)

Required for serving uploaded files:

```bash
./vendor/bin/sail artisan storage:link
```

---

## Notes

- All artisan and composer commands must be run via `./vendor/bin/sail`, not bare `php artisan`.
- The MySQL host inside Docker is `mysql` (not `127.0.0.1`).
- Environment variables are in `.env` — copy `.env.example` if setting up fresh.
