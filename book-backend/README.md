# Book Store Backend (Laravel)

Backend application for a bookstore management system:
- Auth (login/register)
- Dashboard stats
- Books, Customers, Orders
- Payments (including Razorpay integration flow)

## Requirements

- PHP 8.2+
- Composer 2+
- MySQL 8+
- Node.js 18+ (only needed for optional tunnel tools)

## Quick Setup

1. Install dependencies

```bash
composer install
```

2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

3. Set DB in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_store
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. Run migrations and seeders

```bash
php artisan migrate --seed
```

5. Clear caches and start server

```bash
php artisan optimize:clear
php artisan serve
```

App URL: `http://127.0.0.1:8000`

## Default Login Credentials

Try these (depends on seeders used):

- `test@example.com` / `password`
- `admin@bookstore.com` / `admin123`
- `manager@bookstore.com` / `manager123`

## Demo Flow (Client Presentation)

1. Login
2. Open Dashboard
3. Create Customer
4. Create Book
5. Create Order (`/orders/create`)
6. Process Payment (`/payments/order/{id}/create`)
7. Show order/payment status update and dashboard refresh

## Client Deployment (Recommended: Railway)

This repository includes `railway.json` and `Procfile` for deployment.

1. Push code to GitHub.
2. Create a new Railway project from your GitHub repo.
3. Add a MySQL database service in Railway.
4. Set these Railway variables:

```env
APP_NAME=book_store
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...               # generate locally using php artisan key:generate --show
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=<railway_mysql_host>
DB_PORT=<railway_mysql_port>
DB_DATABASE=<railway_mysql_db>
DB_USERNAME=<railway_mysql_user>
DB_PASSWORD=<railway_mysql_password>

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

CACHE_STORE=file
QUEUE_CONNECTION=database
```

5. After first deploy, run migrations:

```bash
php artisan migrate --force
php artisan db:seed --force
```

6. Open the Railway public URL and login.

## Client Deployment (Alternative: Render Blueprint)

This repository now includes `render.yaml` for quick setup.

1. Push latest code to GitHub.
2. In Render, choose `New` -> `Blueprint`.
3. Select your repo.
4. Render will read `render.yaml` and create the web service.
5. Fill required secret env values in Render:
   - `APP_KEY`
   - `APP_URL`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`
6. Run:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan optimize:clear
```

## Quick Public Demo (Fallback)

If you need temporary sharing only, use a tunnel. This is less stable than Railway and may be blocked by corporate firewalls.

## Important Security Notes

- Debug/setup routes are intentionally disabled in `routes/web.php` for shared demo safety.
- Do not share `.env` file with clients.
- Use test payment keys only.

## Troubleshooting

### 1) `Unable to find observer: App\Observers\OrderObserver`

Ensure `app/Observers/OrderObserver.php` exists and app cache is cleared:

```bash
php artisan optimize:clear
```

### 2) `This cache store does not support tagging`

`CACHE_STORE=file` does not support tags. Use normal `Cache::forget(...)` keys (already handled in order flow).

### 3) `Unexpected token '<' ... is not valid JSON`

Usually backend returned HTML error page instead of JSON. Check server logs and ensure payment controller dependencies/imports are correct.

### 4) `419 Page Expired` after login on public URL

Check:
- HTTPS URL is used
- `SESSION_SECURE_COOKIE=true` for HTTPS deployments
- proxy trust enabled in `bootstrap/app.php`
- clear browser cookies for the tunnel domain or use incognito
- run `php artisan optimize:clear`

## Useful Commands

```bash
php artisan route:list
php artisan optimize:clear
php artisan migrate:status
php artisan tinker
```
