# Billable

Billable is a multi-tenant SaaS invoicing platform for small teams. A user signs up on the central app, creates a workspace, chooses a billing plan, and then works from a dedicated tenant subdomain to manage clients, invoices, team members, payments, and billing activity.

Example domains:

- Central app: `https://billable.test`
- Tenant app: `https://acme-studio.billable.test`

The central domain owns public marketing pages, authentication, onboarding, plan selection, subscription webhooks, SEO files, and the Filament admin panel. Tenant domains own workspace features such as dashboard, billing, clients, invoices, team, activity, and public invoice payment links.

Important: `https://billable.test/dashboard` should return `404`. `/dashboard` is a tenant route, so use the tenant domain, for example `https://acme-studio.billable.test/dashboard`.

---

## Tech Stack

| Layer | Tech |
| --- | --- |
| Backend | Laravel 13, PHP `^8.3` |
| Frontend | Inertia.js 3, Vue 3, Tailwind CSS 4, Vite 8 |
| Package/runtime tooling | Composer, Bun |
| Tenancy | `stancl/tenancy` 3, domain-based tenant identification |
| Database | PostgreSQL central database plus PostgreSQL tenant databases |
| Billing subscriptions | Laravel Cashier 16, Stripe Checkout, Stripe Billing Portal |
| Invoice payments | Stripe Payment Intents and a tenant-aware custom webhook |
| Roles and permissions | `spatie/laravel-permission` with central role/permission tables |
| Admin panel | Filament 5 |
| PDF generation | `barryvdh/laravel-dompdf` |
| Code style | Laravel Pint |
| Tests and CI | PHPUnit 12, GitHub Actions |

The CI workflow currently uses PHP 8.5. Local PHP only needs to satisfy Composer's `^8.3` constraint unless a dependency update raises that requirement.

---

## Current Feature Set

- Central landing page with SEO metadata, `robots.txt`, and `sitemap.xml`.
- Central registration, login, logout, and onboarding.
- Workspace creation with reserved-subdomain validation.
- Tenant database creation and tenant migration on workspace creation.
- Free, Pro, and Business plans seeded into the central database.
- Central and tenant subscription flows through Laravel Cashier and Stripe.
- Tenant subscription gate that allows `/billing` recovery while protecting app routes.
- Tenant dashboard with revenue, outstanding, overdue, draft, client, recent invoice, and recent activity data.
- Client CRUD with soft archive and invoice history.
- Invoice CRUD with line items, discounts, tax, totals, PDF download, send action, and reminder action.
- Public tenant invoice payment page at `/pay/{token}` with throttled Payment Intent creation.
- Stripe `payment_intent.succeeded` webhook logging, idempotency checks, tenant resolution, and payment receipt email.
- Workspace activity log for billing, clients, invoices, reminders, payments, and team changes.
- Team member management with owner/member roles.
- Permission-aware Inertia navigation and action visibility.
- Filament admin panel for workspaces, plans, subscription stats, MRR, and user counts.

---

## Architecture

This project follows a type-based Laravel structure. Do not create a `Domains` folder. Place classes by responsibility:

```txt
app/
├── Actions/
├── Concerns/
├── Enums/
├── Filament/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Mail/
├── Models/
├── Policies/
├── Providers/
├── Queries/
├── Services/
├── Support/
└── ViewModels/
```

Preferred request flow:

```txt
Controller
-> Form Request
-> Action
-> Model / Support class
-> Inertia page / redirect / response
```

Controllers should stay thin. Business workflows should live in `app/Actions`, reusable calculations in `app/Support` or `app/Services` when needed, reusable complex reads in `app/Queries`, and page preparation in `app/ViewModels` when controller data shaping gets too large.

Current examples:

- `app/Actions/Tenant/CreateWorkspace.php` creates the tenant, stores the domain, and assigns owner access.
- `app/Actions/Invoice/CreateInvoice.php` creates invoice headers, invoice items, totals, and activity.
- `app/Actions/Invoice/MarkInvoicePaid.php` marks tenant invoices paid after Stripe confirms payment.
- `app/Queries/Tenant/*` contains tenant listing reads for clients, invoices, activity, team members, and billing owners.
- `app/Services/InvoiceNumberService.php` issues tenant invoice numbers from a locked sequence row.
- `app/ViewModels/Tenant/*` prepares page payloads for dashboard and billing overview screens.
- `app/Support/AppUrl.php` builds central and tenant URLs from `APP_URL`.
- `app/Support/InvoiceTotals.php` centralizes invoice subtotal, discount, tax, and total calculations.

---

## Routing Model

Central routes are registered in `routes/web.php`:

```txt
/                         Landing page
/register                 Register
/login                    Login
/logout                   Logout
/onboarding               Workspace onboarding
/plans                    Central plan selection
/billing/success          Central subscription return
/billing/portal           Central billing portal
/robots.txt               Robots file
/sitemap.xml              Sitemap
/stripe/webhook           Cashier subscription webhook
/stripe/invoice-webhook   Custom invoice-payment webhook
/admin                    Filament admin panel
```

Tenant routes are registered in `routes/tenant.php` and loaded by `App\Providers\TenancyServiceProvider`:

```txt
/dashboard                         Workspace dashboard
/billing                           Tenant billing overview
/billing/portal                    Tenant billing portal
/billing/plans/{plan}/subscribe    Tenant plan checkout
/clients                           Client CRUD
/invoices                          Invoice CRUD
/invoices/{invoice}/send           Send invoice email
/invoices/{invoice}/remind         Send invoice reminder
/invoices/{invoice}/pdf            Download invoice PDF
/team                              Team management
/activity                          Activity log
/pay/{token}                       Public invoice payment page
/pay/{token}/intent                Public Payment Intent endpoint
```

Tenant routes use:

- `InitializeTenancyByDomain`
- `PreventAccessFromCentralDomains`
- `tenant.member`
- `subscribed` for protected product routes

The public invoice payment routes are tenant routes, but they do not require login.

---

## Roles And Permissions

Roles and permissions are central-database records powered by `spatie/laravel-permission`.

Current roles:

| Role | Access |
| --- | --- |
| `owner` | Receives every workspace permission. |
| `member` | Can view activity and billing, create/update/view clients and invoices, send invoices, and send reminders. Cannot manage team, manage billing, or delete clients/invoices. |

Current permission groups:

- `activity.view`
- `billing.view`
- `billing.manage`
- `clients.view`
- `clients.create`
- `clients.update`
- `clients.delete`
- `invoices.view`
- `invoices.create`
- `invoices.update`
- `invoices.delete`
- `invoices.send`
- `invoices.remind`
- `team.view`
- `team.manage`

Server-side enforcement currently uses:

- `ClientPolicy`
- `InvoicePolicy`
- explicit permission checks in billing, team, activity, and middleware

Frontend gating uses shared Inertia `permissions` props from `HandleInertiaRequests`. Treat those props as UI visibility only; policies and server checks are the real enforcement layer.

---

## Local Setup

### Prerequisites

- PHP `^8.3`
- Composer
- PostgreSQL
- Bun
- Laravel Herd for local `.test` domains
- Stripe CLI for local webhook forwarding

### 1. Install Dependencies

```bash
composer install
bun install
```

### 2. Create Environment File

```bash
cp .env.example .env
php artisan key:generate
```

Recommended local values:

```env
APP_NAME=Billable
APP_ENV=local
APP_URL=https://billable.test

DB_CONNECTION=central
DB_URL=

CENTRAL_DB_CONNECTION=pgsql
CENTRAL_DB_HOST=
CENTRAL_DB_PORT=
CENTRAL_DB_DATABASE=
CENTRAL_DB_USERNAME=
CENTRAL_DB_PASSWORD=

TENANCY_CENTRAL_CONNECTION=central

SESSION_DRIVER=database
SESSION_DOMAIN=.billable.test

CACHE_STORE=database
CACHE_CONNECTION=central
DB_CACHE_CONNECTION=central
PERMISSION_CACHE_STORE=array

QUEUE_CONNECTION=database

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=usd

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@billable.test
```

Adjust PostgreSQL username and password for your machine. Normal local development should use the named `central` connection; do not switch the app to default SQLite unless you intentionally rework tenancy and test configuration.

### 3. Create Databases

Create the central database before running migrations:

```bash
createdb billable
```

If you plan to run the current PHPUnit suite locally, also create the test database expected by `phpunit.xml`:

```bash
createdb billable_test
```

Tenant databases are created automatically by Stancl Tenancy when a workspace is created.

### 4. Configure Local Domains

With Laravel Herd:

1. Open Herd.
2. Make sure `/Users/syahmirazak/Sites` is parked.
3. Make sure the `billable` site is available as `billable.test`.
4. Secure the site if you want to use `https://billable.test`.

If tenant subdomains do not resolve automatically, add the central and tenant domains you need to `/etc/hosts`:

```txt
127.0.0.1 billable.test
127.0.0.1 acme-studio.billable.test
```

Add more tenant domains as you create more workspaces.

### 5. Run Migrations And Seeders

```bash
php artisan migrate
php artisan db:seed
```

Seeders create:

- default plans
- default roles and permissions
- default super admin user

When a new workspace is created through onboarding, Stancl Tenancy creates that tenant database and runs tenant migrations from `database/migrations/tenant`.

### 6. Run Frontend Assets

For active frontend development:

```bash
bun run dev
```

For compiled production assets:

```bash
bun run build
```

Herd serves Laravel while Vite serves frontend assets during development.

### 7. Forward Stripe Webhooks

The app has two Stripe webhook endpoints:

```txt
https://billable.test/stripe/webhook
https://billable.test/stripe/invoice-webhook
```

Both handlers currently read the same Cashier webhook secret from `STRIPE_WEBHOOK_SECRET`, so make sure the endpoint you are testing uses the matching Stripe CLI signing secret.

---

## Default Credentials

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | `admin@billable.test` | `password` |

Open the admin panel at:

```txt
https://billable.test/admin
```

Only users with `is_admin = true` can access Filament.

---

## Useful Commands

Use these manually when needed:

```bash
# Run queue, logs, and Vite together
composer run dev

# Run Vite only
bun run dev

# Build frontend assets
bun run build

# Format PHP files
./vendor/bin/pint

# Check PHP formatting without changing files
./vendor/bin/pint --test

# Run the scheduled reminder command manually
php artisan invoices:send-reminders

# List all app routes
php artisan route:list --except-vendor

# List central billing routes
php artisan route:list --path=plans --except-vendor

# List tenant billing routes
php artisan route:list --path=billing --except-vendor

# Run the current test suite when you have a PostgreSQL test database ready
php artisan test
```

---

## Testing And CI Notes

The current `phpunit.xml` uses:

```env
DB_CONNECTION=central
CENTRAL_DB_CONNECTION=pgsql
CENTRAL_DB_DATABASE=billable_test
```

So local tests need a PostgreSQL `billable_test` database unless the test configuration is changed.

GitHub Actions is configured in `.github/workflows/ci.yml` with a PostgreSQL 17 service that creates `billable_test`, then runs:

```bash
./vendor/bin/pint --test
php artisan test
bun run build
```

---

## Project Structure

```txt
app/
├── Actions/
│   ├── Activity/
│   ├── Auth/
│   ├── Billing/
│   ├── Client/
│   ├── Invoice/
│   ├── Team/
│   └── Tenant/
├── Concerns/
├── Enums/
├── Filament/
│   ├── Resources/
│   └── Widgets/
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
├── Mail/
├── Models/
├── Policies/
├── Providers/
├── Queries/
│   └── Tenant/
├── Services/
├── Support/
└── ViewModels/
    └── Tenant/

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
- `routes/tenant.php` is loaded by `App\Providers\TenancyServiceProvider`.
- Tenant identification uses `InitializeTenancyByDomain` and `PreventAccessFromCentralDomains`.
- `tenant.active` blocks all tenant routes when a workspace is suspended.
- `tenants.name` and `tenants.is_suspended` are real central database columns, not only values in Stancl's `data` JSON.
- `Stancl\Tenancy\Features\ViteBundler::class` is enabled so tenant pages use normal Vite build assets.
- `asset_helper_tenancy` is still enabled for tenant-aware asset calls, but Vite assets should resolve through the Vite integration.
- `App\Support\AppUrl` builds central URLs, tenant domains, and tenant URLs from `APP_URL`.
- `App\Actions\Tenant\CreateWorkspace` stores the full tenant domain in the `domains` table.
- `App\Concerns\HasTenantAccess` resolves tenant membership, owner checks, permissions, and tenant URLs.
- `CreateInvoice` and `UpdateInvoice` wrap multi-table invoice writes in database transactions.
- Tenant invoice numbers are issued from `invoice_number_sequences` with `lockForUpdate()` instead of `count() + 1`.
- `App\Support\InvoiceTotals` centralizes invoice total math.
- SEO defaults live in `config/seo.php` and are shared with Inertia through `HandleInertiaRequests`.
- `routes/console.php` registers `invoices:send-reminders` and schedules it daily at `09:00`.
- Invoice emails, reminder emails, and receipt emails are currently sent synchronously.

---

## Remaining Improvement Backlog And Concerns

These are the main things worth improving next:

1. Queue email and external side effects.
   Invoice sends, reminders, and receipts currently happen synchronously. Jobs would make retries, failures, and slower mail providers easier to handle.

2. Keep authorization style consistent.
   Client and invoice authorization use policies, while billing, team, and activity mostly use explicit permission checks. This is functional, but Gates or dedicated policies would make the authorization map easier to audit.

3. Review Stripe webhook secret handling for local development.
   Both webhook handlers read the same Cashier webhook secret. If separate Stripe CLI listeners are used, each listener may generate its own signing secret, so one endpoint can fail signature validation unless the secrets are aligned.

4. Move production Stripe price IDs out of seed assumptions.
   Plan records can be managed in Filament, but seeded Stripe Price IDs should be treated as environment-specific test data and verified before production use.

5. Clean default project metadata when ready.
   `composer.json` still has Laravel skeleton metadata. Update package name, description, and keywords when the project identity is finalized.

---

## Troubleshooting

### `billable.test/dashboard` returns 404

That is expected. `/dashboard` is not a central route. Open the tenant domain instead:

```txt
https://acme-studio.billable.test/dashboard
```

### Tenant page is blank and assets load from `/tenancy/assets/...`

Check that `Stancl\Tenancy\Features\ViteBundler::class` is enabled in `config/tenancy.php`, then rebuild assets when needed:

```bash
bun run build
```

Tenant pages should load Vite assets from `/build/assets/...`.

### Permission changes do not appear

Roles and permissions are cached by Spatie. This project uses `PERMISSION_CACHE_STORE=array` in `.env.example` for local friendliness. If a non-array cache store is used, clear permission/cache state after changing roles or permissions.

### Tests cannot connect to the database

Create the expected test database or update `phpunit.xml` intentionally:

```bash
createdb billable_test
```

### Tenant subdomain does not resolve

Add the tenant domain to `/etc/hosts` or configure your local DNS/Herd setup:

```txt
127.0.0.1 acme-studio.billable.test
```

---

Built by [syahmidev](https://github.com/syahmidev)
