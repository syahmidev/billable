# billable

**Multi-tenant SaaS invoicing platform** built as Portfolio Project #1.

Create a workspace, manage clients, generate PDF invoices, collect Stripe payments online — all from a dedicated subdomain.

---

## Tech Stack

| Layer | Tech |
|---|---|
| Backend | Laravel 13 · PHP 8.4 |
| Multi-tenancy | stancl/tenancy v3 (subdomain-based, separate DBs) |
| Frontend | Inertia.js v3 · Vue 3 · Tailwind CSS v4 |
| Subscriptions | Stripe via Laravel Cashier v16 |
| Invoice payments | Stripe Payment Intents (direct SDK) |
| Admin panel | Filament v5 |
| PDF generation | barryvdh/laravel-dompdf |
| Database | PostgreSQL (central + per-tenant) |
| Package manager | Bun |

---

## Features

- **Multi-tenant workspaces** — each workspace gets a subdomain (`acme.billable.test`)
- **Stripe subscriptions** — Free, Pro, Business tiers via Cashier
- **Client management** — full CRUD with invoice history and balance tracking
- **Invoice management** — draft → sent → paid lifecycle with PDF generation
- **Invoice payments** — clients pay via a secure public payment link (Stripe Payment Element)
- **Dashboard & analytics** — MRR, outstanding, overdue alerts, 6-month revenue chart
- **Super admin panel** — Filament v5 at `/admin` to manage tenants, plans, and subscriptions
- **Responsive UI** — dark sidebar layout with mobile drawer

---

## Local Setup

### Prerequisites
- PHP 8.4+
- PostgreSQL
- Bun
- Stripe CLI (for webhook forwarding)

### 1. Clone & Install

```bash
git clone <repo-url> billable
cd billable
composer install
bun install
cp .env.example .env
php artisan key:generate
```

### 2. Configure `.env`

```env
APP_URL=http://billable.test

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=billable
DB_USERNAME=your_user
DB_PASSWORD=your_password

SESSION_DRIVER=database
SESSION_CONNECTION=pgsql
CACHE_STORE=database
CACHE_CONNECTION=pgsql

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=usd
```

### 3. Hosts file (subdomain tenancy)

Add to `/etc/hosts`:
```
127.0.0.1 billable.test
127.0.0.1 acme.billable.test   # repeat for each workspace
```

Or use Valet: `valet link billable && valet secure billable`

### 4. Database & Seed

```bash
php artisan migrate
php artisan db:seed           # creates admin user + default plans
```

### 5. Build & Run

```bash
bun run build
php artisan serve
```

For Stripe webhooks in development:
```bash
stripe listen --forward-to http://billable.test/stripe/webhook
stripe listen --forward-to http://billable.test/stripe/invoice-webhook
```

---

## Default Credentials

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@billable.test | password |

---

## Project Structure

```
app/
├── Actions/           # Business logic (thin controllers)
│   ├── Client/
│   ├── Invoice/
│   └── Auth/
├── Filament/          # Super admin panel
│   ├── Resources/
│   └── Widgets/
├── Http/
│   ├── Controllers/Tenant/    # Tenant-side routes
│   ├── Controllers/Payment/   # Public invoice payment
│   └── Controllers/Stripe/    # Webhook handlers
├── Models/
│   ├── User.php       # Central DB, FilamentUser
│   ├── Tenant.php     # TenantWithDatabase + custom data column
│   ├── Client.php
│   ├── Invoice.php
│   └── InvoiceItem.php
└── Providers/
    └── Filament/AdminPanelProvider.php

database/
├── migrations/        # Central DB migrations
└── migrations/tenant/ # Per-tenant DB migrations

resources/js/
├── Layouts/
│   ├── AppLayout.vue      # Dark sidebar with mobile drawer
│   ├── AuthLayout.vue
│   └── PaymentLayout.vue  # Minimal public layout
└── Pages/
    ├── Landing.vue
    ├── Auth/
    ├── Onboarding/
    ├── Billing/
    ├── Payment/
    └── Tenant/
        ├── Dashboard.vue
        ├── Clients/
        └── Invoices/
```

---

## Architecture Notes

- **Tenancy**: `InitializeTenancyByDomain` — the full domain (e.g. `acme.billable.test`) is stored in the `domains` table. Each tenant gets its own PostgreSQL database.
- **Cross-domain redirects**: Uses `Inertia::location()` not `redirect()->away()` — required for Inertia to do a full page reload across domains.
- **User model**: Pinned to `protected $connection = 'pgsql'` so it always reads from the central DB even within tenant context.
- **Stripe invoice payments**: Direct PaymentIntent API (not Cashier) — the `payment_token` UUID on each invoice is the public identifier for the payment page.
- **Webhook tenant resolution**: `tenant_id` is stored in PaymentIntent metadata, allowing the webhook handler to `Tenancy::initialize()` the correct tenant before marking the invoice paid.

---

Built by [syahmidev](https://github.com/syahmidev)
