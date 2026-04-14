# PostPilot — Laravel Edition

AI-powered Instagram scheduling for Indian local businesses.
**Stack:** Laravel 11, MySQL, Blade, Laravel Queues, Socialite (Google OAuth), Razorpay, Guzzle.

---

## Folder Structure

```
postpilot-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── SocialiteController.php     # Google OAuth login/logout
│   │   │   ├── AccountController.php           # Instagram connect/disconnect
│   │   │   ├── AdminController.php             # Admin dashboard data
│   │   │   ├── DashboardController.php         # Page controllers + calendar API
│   │   │   ├── GenerateController.php          # AI caption + festivals
│   │   │   ├── PaymentController.php           # Razorpay order + verify
│   │   │   └── PostController.php             # Post CRUD + reschedule
│   │   └── Middleware/
│   │       └── AdminAuth.php                   # Admin key protection
│   ├── Jobs/
│   │   └── PublishPost.php                     # Queue job — publishes to Instagram
│   ├── Mail/
│   │   ├── WelcomeMail.php
│   │   ├── PaymentReceiptMail.php
│   │   ├── PostFailedMail.php
│   │   └── AdminAlertMail.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── ConnectedAccount.php
│   │   ├── Payment.php
│   │   └── Festival.php
│   ├── Policies/
│   │   └── PostPolicy.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/
│       ├── AnthropicService.php                # Claude AI via Guzzle
│       ├── MetaService.php                     # Instagram Graph API
│       └── RazorpayService.php                 # Razorpay orders + verification
├── bootstrap/
│   └── app.php                                 # Laravel 11 app setup
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── postpilot.php                           # Plan config + Meta config
│   ├── queue.php
│   └── services.php                            # Google, Razorpay, Anthropic keys
├── database/
│   ├── migrations/                             # 4 migration files
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── FestivalSeeder.php                  # 23 Indian festivals
├── public/
│   └── index.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php                   # Master layout with header + JS helpers
│       ├── auth/
│       │   └── login.blade.php                 # Google sign-in page
│       ├── dashboard/
│       │   ├── calendar.blade.php              # FullCalendar + post composer
│       │   ├── queue.blade.php                 # Filtered post list
│       │   ├── accounts.blade.php              # Instagram connection management
│       │   └── pricing.blade.php               # Plans + Razorpay checkout
│       ├── admin/
│       │   ├── login.blade.php
│       │   └── dashboard.blade.php             # Stats, payments, users, failed posts
│       └── emails/
│           ├── welcome.blade.php
│           ├── payment-receipt.blade.php
│           ├── post-failed.blade.php
│           └── admin-alert.blade.php
├── routes/
│   ├── web.php                                 # All application routes
│   └── console.php                             # Artisan commands + scheduler
├── artisan
├── composer.json
├── .env.example
└── README.md
```

---

## Prerequisites

| Tool    | Version    | Check                    |
|---------|------------|--------------------------|
| PHP     | 8.2+       | `php --version`          |
| Composer| 2.x        | `composer --version`     |
| MySQL   | 8.0+       | `mysql --version`        |
| Node.js | 18+        | `node --version` (for assets if needed) |

---

## Step 1 — Get All API Keys

| # | Key | Where |
|---|-----|-------|
| 1 | Google OAuth Client ID + Secret | console.cloud.google.com → APIs → Credentials → OAuth 2.0 |
| 2 | Meta App ID + Secret | developers.facebook.com → Create App |
| 3 | Razorpay Key ID + Secret | dashboard.razorpay.com → Settings → API Keys (test keys first) |
| 4 | Anthropic API Key | console.anthropic.com |
| 5 | Gmail App Password | myaccount.google.com → Security → App Passwords (needs 2FA) |

---

## Step 2 — Google OAuth Setup

1. Go to console.cloud.google.com
2. Create a project → APIs & Services → Credentials
3. Create OAuth 2.0 Client ID (Web application)
4. Add Authorized redirect URI: `http://localhost:8000/auth/google/callback`
5. Copy Client ID and Client Secret into `.env`

---

## Step 3 — MySQL Database Setup

```sql
CREATE DATABASE postpilot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'postpilot'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON postpilot.* TO 'postpilot'@'localhost';
FLUSH PRIVILEGES;
```

---

## Step 4 — Install and Configure

```bash
cd postpilot-laravel

# Install PHP dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Fill all values in .env (see .env.example for instructions)
nano .env
```

Key values to fill in `.env`:
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`
- `META_APP_ID`, `META_APP_SECRET`
- `RAZORPAY_KEY_ID`, `RAZORPAY_KEY_SECRET`
- `ANTHROPIC_API_KEY`
- `MAIL_USERNAME`, `MAIL_PASSWORD` (Gmail App Password)
- `ADMIN_SECRET_KEY` (make up any long random string)

---

## Step 5 — Database Setup

```bash
# Run all migrations (creates all tables)
php artisan migrate

# Seed Indian festivals (23 festivals for 2026-2027)
php artisan db:seed

# Create storage symlink (for uploaded images)
php artisan storage:link
```

---

## Step 6 — Run the Project

You need **2 terminals:**

### Terminal 1 — Web Server
```bash
php artisan serve
# Runs at http://localhost:8000
```

### Terminal 2 — Queue Worker (publishes scheduled posts)
```bash
php artisan queue:work --queue=default
```

Open `http://localhost:8000` in your browser.

---

## Step 7 — Meta Developer App Setup

1. Go to developers.facebook.com → Create App → Business type
2. Add product: **Instagram Graph API**
3. Settings → Basic: copy App ID and App Secret into `.env`
4. Instagram Graph API → Settings → Add OAuth Redirect URI: `http://localhost:8000/auth/instagram/callback`
5. Add your own account as a test user (Roles → Test Users)
6. Add your Instagram as a test Instagram account

---

## Step 8 — Test the Full Flow

1. Open `http://localhost:8000`
2. Click "Sign in with Google"
3. Go to Accounts → Connect Instagram
4. Go to Calendar → click a date → Post Composer opens
5. Fill in AI fields → click Generate → caption appears
6. Upload an image → set date and time → Schedule Post
7. Check Queue tab — post appears as "scheduled"
8. Watch Terminal 2 — queue worker publishes at scheduled time
9. Go to Pricing → click "Get Growth" → Razorpay modal
   - Test card: `4111 1111 1111 1111`, any future date, any CVV
   - UPI test: `success@razorpay`

---

## Admin Dashboard

Go to `http://localhost:8000/admin/login`
Enter your `ADMIN_SECRET_KEY` from `.env`

Shows: total users, MRR, plan breakdown, revenue by month, recent payments, failed posts, recent users.

---

## Production Deployment

### Using Shared Hosting (cPanel)
```bash
# Point document root to /public
# Set APP_ENV=production, APP_DEBUG=false in .env
# Run migrations on production DB
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Queue Worker on Production (Supervisor)
Create `/etc/supervisor/conf.d/postpilot-worker.conf`:
```ini
[program:postpilot-worker]
command=php /var/www/postpilot/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/postpilot
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/postpilot/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start postpilot-worker:*
```

### Cron (for Laravel Scheduler)
```bash
crontab -e
# Add this line:
* * * * * cd /var/www/postpilot && php artisan schedule:run >> /dev/null 2>&1
```

---

## Common Errors and Fixes

| Error | Fix |
|-------|-----|
| `APP_KEY` not set | Run `php artisan key:generate` |
| DB connection refused | Check DB credentials in `.env`, ensure MySQL is running |
| Google OAuth error | Check redirect URI matches exactly in Google Console and `.env` |
| Instagram "No Business account" | Your IG must be Business/Creator, linked to a Facebook Page |
| Mail not sending | Use Gmail App Password (not your regular password). Enable 2FA first. |
| Queue not processing | Make sure Terminal 2 is running `php artisan queue:work` |
| Storage images not showing | Run `php artisan storage:link` |
| Razorpay signature mismatch | Check `RAZORPAY_KEY_SECRET` matches exactly (no spaces) |
