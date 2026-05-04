# Billable

Billable is a multi-tenant SaaS invoicing platform for small teams. It lets a user create a workspace, manage clients, send invoices, collect Stripe payments, and track billing activity from a dedicated tenant subdomain.

Example domains:

- Central app: `https://billable.test`
- Tenant app: `https://acme-studio.billable.test`

The central domain is for the landing page, authentication, onboarding, subscriptions, SEO files, and the admin panel. Tenant domains are where the actual workspace routes live.

---

## Tech Stack

| Layer | Tech |
| --- | --- |
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Inertia.js 3, Vue 3, Tailwind CSS 4, Vite 8 |
| Tenancy | stancl/tenancy 3, domain-based tenant identification |
| Billing subscriptions | Laravel Cashier 16, Stripe Checkout, Stripe Billing Portal |
| Invoice payments | Stripe Payment Intents |
| Admin panel | Filament 5 |
| PDF generation | barryvdh/laravel-dompdf |
| Database | PostgreSQL central database plus PostgreSQL tenant databases |
| Code style | Laravel Pint with project rules in `pint.json` |
| Frontend packages | Bun lockfile is present; npm scripts also work |

---

## What The App Does

- Public landing page with SEO metadata, `robots.txt`, and `sitemap.xml`.
- User registration, login, and onboarding on the central domain.
- Workspace creation with a reserved-subdomain check.
- Tenant database creation and migration when a workspace is created.
- Free, Pro, and Business plans seeded into the central database.
- Stripe subscriptions through Cashier for paid plans.
- Tenant billing page at `/billing` for plan changes and the Stripe billing portal.
- Tenant dashboard with revenue, outstanding invoice, and overdue invoice summary data.
- Client CRUD inside tenant workspaces.
- Invoice CRUD with line items, discounts, tax, PDF generation, and send action.
- Public tenant invoice payment page using a payment token.
- Stripe invoice webhook that resolves the tenant before marking an invoice paid.
- Filament admin panel for platform administration.

---

## Domain Model

Billable separates central routes from tenant routes.

Central routes are registered in `routes/web.php` and run on `billable.test`:

- `/`
- `/register`
- `/login`
- `/onboarding`
- `/plans`
- `/billing/success`
- `/billing/portal`
- `/robots.txt`
- `/sitemap.xml`
- `/stripe/webhook`
- `/stripe/invoice-webhook`
- `/admin`

Tenant routes are registered in `routes/tenant.php` and run on a tenant domain such as `acme-studio.billable.test`:

- `/dashboard`
- `/billing`
- `/billing/portal`
- `/clients`
- `/invoices`
- `/pay/{token}`

Important: `https://billable.test/dashboard` should return 404 because `/dashboard` is a tenant route. Use the saved tenant domain, for example `https://acme-studio.billable.test/dashboard`.

---

## Local Setup

### Prerequisites

- PHP 8.3+
- Composer
- PostgreSQL
- Bun or Node.js/npm
- Laravel Valet is recommended for local subdomain tenancy
- Stripe CLI for local webhook forwarding

### 1. Install Dependencies

```bash
composer install
bun install
```

If you prefer npm:

```bash
npm install
```

### 2. Create Environment File

```bash
cp .env.example .env
php artisan key:generate
```

Set the important values in `.env`:

```env
APP_NAME=Billable
APP_URL=https://billable.test

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=billable
DB_USERNAME=your_postgres_user
DB_PASSWORD=your_postgres_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=usd
```

Do not leave the app on SQLite for normal local development. Several central models use the PostgreSQL connection explicitly, and tenant databases are also PostgreSQL in the current setup.

### 3. Configure Local Domains

With Valet:

```bash
valet link billable
valet secure billable
```

If you are not using Valet, add the central and tenant domains you need to `/etc/hosts`:

```txt
127.0.0.1 billable.test
127.0.0.1 acme-studio.billable.test
```

Add more tenant domains as you create more workspaces.

### 4. Run Migrations And Seeders

```bash
php artisan migrate
php artisan db:seed
```

The seeders create the default plans and the default super admin user.

When a new workspace is created through onboarding, Stancl Tenancy creates the tenant database and runs tenant migrations automatically.

### 5. Build Frontend Assets

```bash
bun run build
```

Or with npm:

```bash
npm run build
```

For active frontend work:

```bash
bun run dev
```

Valet should serve the Laravel app while Vite serves frontend assets in development.

### 6. Forward Stripe Webhooks

Use separate terminals if you want to listen to both endpoints:

```bash
stripe listen --forward-to https://billable.test/stripe/webhook
stripe listen --forward-to https://billable.test/stripe/invoice-webhook
```

Copy the generated webhook secret into `STRIPE_WEBHOOK_SECRET`.

---

## Default Credentials

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | `admin@billable.test` | `password` |

---

## Common Development Commands

```bash
# Format PHP files
./vendor/bin/pint

# Check PHP formatting without changing files
./vendor/bin/pint --test

# Build frontend assets
bun run build

# Run Vite dev server
bun run dev

# Run the focused invoice totals unit tests
php artisan test --filter=InvoiceTotalsTest

# List central billing routes
php artisan route:list --path=plans --except-vendor

# List tenant billing routes
php artisan route:list --path=billing --except-vendor
```

Full test-suite note: `php artisan test` currently depends on PostgreSQL being available because the landing page loads plans from the `pgsql` connection. If PostgreSQL is not reachable in the test environment, the default feature test for `/` can fail with a database connection error.

---

## Project Structure

```txt
app/
├── Actions/
│   ├── Auth/
│   ├── Billing/
│   ├── Client/
│   ├── Invoice/
│   └── Tenant/
├── Concerns/
├── Enums/
├── Filament/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Billing/
│   │   ├── Payment/
│   │   ├── Seo/
│   │   ├── Stripe/
│   │   └── Tenant/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Providers/
└── Support/

database/
├── migrations/
├── migrations/tenant/
└── seeders/

resources/js/
├── Components/
├── Layouts/
└── Pages/
    ├── Auth/
    ├── Billing/
    ├── Onboarding/
    ├── Payment/
    └── Tenant/

routes/
├── web.php
├── tenant.php
└── console.php
```

---

## Important Implementation Notes

- `config/tenancy.php` defines `billable.test`, `localhost`, and `127.0.0.1` as central domains.
- Tenant identification uses `InitializeTenancyByDomain` and `PreventAccessFromCentralDomains`.
- `routes/tenant.php` is loaded by `App\Providers\TenancyServiceProvider`.
- `Stancl\Tenancy\Features\ViteBundler::class` is enabled so tenant pages use normal Vite build assets instead of broken tenant asset URLs.
- `App\Support\AppUrl` builds central URLs and tenant domains from `APP_URL`.
- `App\Actions\Tenant\CreateWorkspace` stores the full tenant domain in the `domains` table.
- `App\Concerns\HasTenantAccess` is used by users to resolve tenant access and tenant URLs.
- `App\Support\InvoiceTotals` centralizes invoice subtotal, discount, tax, and total calculations.
- SEO defaults live in `config/seo.php` and are shared with Inertia through `HandleInertiaRequests`.
- Public invoice payment routes are tenant routes, but they do not require login.
- Stripe subscription webhooks are handled by Cashier at `/stripe/webhook`.
- Stripe invoice payment webhooks are handled by `InvoiceWebhookController` at `/stripe/invoice-webhook`.

---

## Troubleshooting

### `billable.test/dashboard` returns 404

That is expected. `/dashboard` is not a central route. Open the tenant domain instead:

```txt
https://acme-studio.billable.test/dashboard
```

### Tenant page is blank and assets load from `/tenancy/assets/...`

Check that `Stancl\Tenancy\Features\ViteBundler::class` is enabled in `config/tenancy.php`, then rebuild assets:

```bash
bun run build
```

Tenant pages should load assets from `/build/assets/...`.

### Plans or landing page fail during tests

The `Plan` model uses the `pgsql` connection. Make sure PostgreSQL is reachable for feature tests that hit the landing page or billing pages.

---

Built by [syahmidev](https://github.com/syahmidev)
