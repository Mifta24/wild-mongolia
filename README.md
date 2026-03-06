# Thai Travel

A web application for Thailand travel services (car transfers + tours) with an admin panel, membership, points/coupons, Stripe payment, and real-time support chat.

## Stack

- PHP `^8.3`
- Laravel `^12`
- MySQL/PostgreSQL/SQLite (default local: SQLite)
- Tailwind CSS + Vite
- Laravel Reverb (real-time chat)
- Stripe (`stripe/stripe-php`)
- Social login Google (`laravel/socialite`)
- Role & permission (`spatie/laravel-permission`)

## Implemented Features

- Public pages: Home, Cars, Tours, Membership, Contact, FAQ, Terms, Privacy
- Contact form sends email (`/contact` POST)
- Authentication + Google OAuth
- Role-based dashboard (admin/user)
- Booking flow: create, payment, success, invoice/voucher download
- Stripe payment + webhook endpoint
- Membership paid tier (Gold/Platinum) + renewal flow via Stripe
- Points & coupons system
- User dashboard: bookings, points, coupons, profile, notifications
- User review submission
- Admin panel: products, bookings, customers, coupons, points, inventory, vendor, dispatch
- Real-time support chat (user/admin) via Reverb
- Scheduled commands: point expiry, coupon deactivation, deactivated user purge

## Not Yet Implemented / TODO

- CMS for content pages (based on `TASK.md`)
- Full multilingual support (EN/JP/TH/CN)
- Vendor portal dedicated (self-service partner)

## Key Routes

- Public:
	- `GET /` (`home`)
	- `GET /contact`, `POST /contact`
	- `GET /faq`, `GET /terms`, `GET /privacy`
	- `GET /cars`, `GET /tours`, `GET /membership`
- Payment:
	- `POST /webhooks/stripe`
	- User payment routes are under auth middleware (`booking.payment`, `booking.process`, `booking.success`)
- Membership:
	- `POST /user/membership/subscribe`
	- `GET /user/membership/success`
- Support chat:
	- `GET /support/chat`
	- `POST /support/chat/message`

## Local Installation

1. Install dependencies:

```bash
composer install
npm install
```

2. Create env file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

3. Set up database, then run migration:

```bash
php artisan migrate
```

4. Run the app (single command):

```bash
composer run dev
```

This command runs Laravel server, queue listener, Reverb, and Vite at the same time.

## Minimal Environment Configuration

Example important variables in `.env`:

```dotenv
APP_URL=http://localhost:8000

# Mail (for contact form and notifications)
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=465
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your-email
MAIL_FROM_NAME="Thai Travel"
SUPPORT_EMAIL=your-support-email

# Stripe
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT=${APP_URL}/auth/google/callback

# Reverb
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

## Stripe Webhook (Local)

1. Run listener:

```bash
stripe listen --forward-to http://127.0.0.1:8000/webhooks/stripe
```

2. Copy the signing secret from Stripe CLI output into `STRIPE_WEBHOOK_SECRET`.

## Scheduler / Cron

Set server cron:

```bash
* * * * * cd /path/to/thai-travel && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks:

- `points:expire` (daily)
- `coupons:deactivate-expired` (daily)
- `users:purge-deactivated` (daily)

## Testing

Run all tests:

```bash
composer test
```

Or run a specific test:

```bash
php artisan test tests/Feature/ContactFormTest.php
```

## Notes

- This `README` is aligned with the current state of the project.
- Product requirement reference remains in `TASK.md`.
