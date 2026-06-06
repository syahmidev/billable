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
| Queues | Laravel database queue, stored on the central connection |
| Roles and permissions | `spatie/laravel-permission` with central role/permission tables |
| Admin panel | Filament 5 |
| PDF generation | `barryvdh/laravel-dompdf` |
| Code style | Laravel Pint |
| Tests and CI | PHPUnit 12, GitHub Actions |

The CI workflow currently uses PHP 8.5. Local PHP only needs to satisfy Composer's `^8.3` constraint unless a dependency update raises that requirement.

---

## Current Feature Set

- Central landing page with SEO metadata, `robots.txt`, and `sitemap.xml`.
- Central registration, login, logout, and onboarding with rate limiting on all auth routes.
- Email verification with resend support — tenant and billing routes are gated behind the `verified` middleware.
- Password reset flow with signed reset link and Inertia pages.
- Workspace creation with reserved-subdomain validation.
- Tenant database creation and tenant migration on workspace creation.
- Free, Pro, and Business plans seeded into the central database.
- Plan limits enforced in code — Free plan is capped at 3 clients and 5 invoices per month via `PlanLimitsService`.
- Central and tenant subscription flows through Laravel Cashier and Stripe.
- Tenant subscription gate that allows `/billing` recovery while protecting app routes.
- Subscription cancellation webhook — `customer.subscription.deleted` resets the workspace owner's plan and clears subscription caches.
- Tenant dashboard with revenue, outstanding, overdue, draft, client, recent invoice, and recent activity data.
- Client CRUD with soft archive and invoice history.
- Invoice CRUD with line items, discounts, tax, totals, PDF download (rate limited), queued send action, and queued reminder action.
- Invoice CSV export at `/invoices/export` with optional `status`, `client_id`, `from`, and `to` query filters.
- Auto-overdue job that transitions sent invoices past their due date to `overdue` daily at `00:01`.
- Public tenant invoice payment page at `/pay/{token}` with throttled Payment Intent creation — draft, cancelled, and deleted invoices return 404.
- Stripe `payment_intent.succeeded` webhook logging, idempotency checks, tenant resolution, and queued payment receipt email.
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
├── Exceptions/
├── Filament/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
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
- `app/Actions/Invoice/CreateInvoice.php` creates invoice headers, invoice items, totals, and activity — enforces plan limits before creation.
- `app/Actions/Invoice/MarkInvoicePaid.php` marks tenant invoices paid after Stripe confirms payment.
- `app/Actions/Invoice/MarkOverdueInvoices.php` loops all tenants and transitions due sent invoices to overdue.
- `app/Jobs/Invoice/*` sends invoice, reminder, and payment receipt emails from the queue.
- `app/Queries/Tenant/*` contains tenant listing reads for clients, invoices, activity, team members, and billing owners.
- `app/Services/InvoiceNumberService.php` issues tenant invoice numbers from a locked sequence row.
- `app/Services/PlanLimitsService.php` reads the workspace owner's plan and enforces per-plan client and invoice limits.
- `app/Exceptions/PlanLimitExceededException.php` is thrown when a plan limit is hit and handled globally to redirect back with an error message.
- `app/ViewModels/Tenant/*` prepares page payloads for dashboard and billing overview screens.
- `app/Support/AppUrl.php` builds central and tenant URLs from `APP_URL`.
- `app/Support/InvoiceTotals.php` centralizes invoice subtotal, discount, tax, and total calculations.

---

## Routing Model

Central routes are registered in `routes/web.php`:

```txt
/                         Landing page
/register                 Register (throttle: 10/min)
/login                    Login (throttle: 5/min)
/logout                   Logout
/forgot-password          Password reset request (throttle: 5/min)
/reset-password/{token}   Password reset form + submit (throttle: 5/min)
/email/verify             Email verification notice
/email/verify/{id}/{hash} Email verification link (signed)
/onboarding               Workspace onboarding
/plans                    Central plan selection
/billing/success          Central subscription return
/billing/portal           Central billing portal
/robots.txt               Robots file
/sitemap.xml              Sitemap
/stripe/webhook           Cashier subscription webhook (CashierWebhookController)
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
/invoices/export                   CSV export (throttle: 10/min)
/invoices/{invoice}/send           Send invoice email
/invoices/{invoice}/remind         Send invoice reminder
/invoices/{invoice}/pdf            Download invoice PDF (throttle: 20/min)
/team                              Team management
/activity                          Activity log
/pay/{token}                       Public invoice payment page (throttle: 60/min)
/pay/{token}/intent                Public Payment Intent endpoint (throttle: 10/min)
```

Tenant routes use:

- `InitializeTenancyByDomain`
- `PreventAccessFromCentralDomains`
- `tenant.member`
- `subscribed` for protected product routes

The public invoice payment routes are tenant routes, but they do not require login.

---

## Plan Limits

Plan limits are enforced at creation time by `PlanLimitsService`, called from `CreateClient` and `CreateInvoice`.

| Plan | Client limit | Invoice limit |
| --- | --- | --- |
| Free | 3 total | 5 per month |
| Pro | Unlimited | Unlimited |
| Business | Unlimited | Unlimited |

When a limit is exceeded, `PlanLimitExceededException` is thrown and the global exception handler redirects back with a user-facing error message. Existing data is never deleted or hidden on downgrade — limits only gate new creation.

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
- Gates for billing, team, and activity workspace abilities
- shared Inertia permission props for UI visibility

Frontend gating uses shared Inertia `permissions` props from `HandleInertiaRequests`. Treat those props as UI visibility only; policies and server checks are the real enforcement layer.

Team members do not inherit the owner's billing plan. Their `plan` column is always `null`. `EnsureSubscribed` checks the workspace owner's subscription when a member's own plan is null — this is the correct and intended path.

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
DB_QUEUE_CONNECTION=central

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_INVOICE_WEBHOOK_SECRET=whsec_...
STRIPE_PRICE_PRO=price_...
STRIPE_PRICE_BUSINESS=price_...
CASHIER_CURRENCY=usd

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@billable.test
```

Adjust PostgreSQL username and password for your machine. Normal local development should use the named `central` connection; do not switch the app to default SQLite unless you intentionally rework tenancy and test configuration.

In production, set `SESSION_SECURE_COOKIE=true` and `APP_DEBUG=false`.

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

Paid plan Stripe Price IDs are read from `STRIPE_PRICE_PRO` and `STRIPE_PRICE_BUSINESS`. If those values are blank, seeders will not overwrite existing paid plan Price IDs; production values can also be managed in Filament.

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

Use `STRIPE_WEBHOOK_SECRET` for the Cashier subscription endpoint. Use `STRIPE_INVOICE_WEBHOOK_SECRET` for the custom invoice-payment endpoint. If `STRIPE_INVOICE_WEBHOOK_SECRET` is blank, the invoice webhook falls back to `STRIPE_WEBHOOK_SECRET` for simpler one-secret local setups.

Both endpoints fail closed when the required signing secret is missing, so do not leave `STRIPE_WEBHOOK_SECRET` blank while testing Stripe callbacks.

For local development with two Stripe CLI listeners, run one listener per endpoint and copy each printed `whsec_...` value into the matching environment variable:

```bash
stripe listen --forward-to https://billable.test/stripe/webhook
stripe listen --events payment_intent.succeeded --forward-to https://billable.test/stripe/invoice-webhook
```

After changing webhook secrets in `.env`, clear cached config if needed:

```bash
php artisan config:clear
```

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

# Run the full test suite (requires billable_test PostgreSQL database)
php artisan test

# Mark all sent invoices past their due date as overdue (runs automatically at 00:01)
php artisan invoices:mark-overdue

# Send invoice reminders for due and overdue invoices (runs automatically at 09:00)
php artisan invoices:send-reminders

# Backfill missing payment tokens on invoices created before the payment_token column was added
# Run once in production if any invoice has a NULL payment_token
php artisan invoices:backfill-tokens

# List all app routes
php artisan route:list --except-vendor

# List central billing routes
php artisan route:list --path=plans --except-vendor

# List tenant billing routes
php artisan route:list --path=billing --except-vendor
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

The test suite currently has 47 tests across unit and feature layers covering:

- Invoice totals and status logic
- Permission and subscription status enums
- Landing page, SEO routes, and routing boundary assertions
- Stripe invoice webhook idempotency and signature verification
- Register plan intent preservation
- Tenant isolation — cross-tenant client, invoice, and payment token access
- Plan limit enforcement for Free plan client and invoice caps
- Invoice CSV export including content verification and status filtering
- Cashier subscription cancellation webhook plan reset and cache clearing

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
├── Exceptions/
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
├── Jobs/
│   └── Invoice/
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
- Workspace billing, team, and activity authorization is mapped through Gates in `App\Providers\AppServiceProvider`.
- `CreateInvoice` and `UpdateInvoice` wrap multi-table invoice writes in database transactions.
- `CreateClient` and `CreateInvoice` both call `PlanLimitsService` before writing — Free plan tenants are hard-stopped at 3 clients and 5 invoices/month. Pro and Business have no limits.
- Tenant invoice numbers are issued from `invoice_number_sequences` with `lockForUpdate()` instead of `count() + 1`.
- `App\Support\InvoiceTotals` centralizes invoice total math.
- `App\Http\Controllers\Stripe\CashierWebhookController` extends Cashier's built-in `WebhookController`. It adds a `handleCustomerSubscriptionDeleted` override that resets `user.plan` to `null` and clears the `tenant_owner_plan_*` and `tenant_owner_*` cache keys when a subscription is cancelled via the Stripe billing portal.
- `EnsureSubscribed` caches the workspace owner lookup per tenant for 5 minutes. Clear `tenant_owner_{id}` from cache if subscription state must propagate immediately.
- `PlanLimitsService` caches the owner plan lookup per tenant for 5 minutes under `tenant_owner_plan_{id}`.
- Paid plan Stripe Price IDs are configured through `config/billing.php` from `STRIPE_PRICE_PRO` and `STRIPE_PRICE_BUSINESS`.
- The custom invoice Stripe webhook reads `STRIPE_INVOICE_WEBHOOK_SECRET`, falling back to `STRIPE_WEBHOOK_SECRET` when a separate invoice secret is not configured.
- The Cashier subscription webhook is guarded by `EnsureCashierWebhookSecretIsConfigured` so a missing `STRIPE_WEBHOOK_SECRET` does not leave it unsigned.
- SEO defaults live in `config/seo.php` and are shared with Inertia through `HandleInertiaRequests`.
- `routes/console.php` registers and schedules `invoices:mark-overdue` (daily at `00:01`) and `invoices:send-reminders` (daily at `09:00`), both with `withoutOverlapping()`. A one-time `invoices:backfill-tokens` command is also registered for production data repair.
- Invoice emails, reminder emails, and receipt emails are queued through `app/Jobs/Invoice` with `$tries = 3` and exponential backoff of `[30, 60, 120]` seconds.
- Database queue jobs use `DB_QUEUE_CONNECTION`, which should stay on the central connection for tenant apps.
- Auth routes (login, register, forgot/reset password) are rate limited. See `routes/web.php` for per-route throttle values.
- Team members do not inherit the workspace owner's billing plan. The `plan` column on team member rows is always `null`. `EnsureSubscribed` falls through to the owner lookup when a member's own plan is null.

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

### A workspace owner cancels their subscription but team members still have access

This should not happen with the current implementation. `customer.subscription.deleted` resets the owner's plan and clears subscription caches. If the issue persists, manually clear the `tenant_owner_{id}` and `tenant_owner_plan_{id}` cache keys and verify the Cashier webhook secret is correctly configured.

### An invoice shows no payment URL

The invoice was likely created before the `payment_token` column was added. Run the backfill command once to repair all affected rows:

```bash
php artisan invoices:backfill-tokens
```

---

Built by [syahmidev](https://github.com/syahmidev)
