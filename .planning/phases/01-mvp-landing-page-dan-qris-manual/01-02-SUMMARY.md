---
phase: 01-mvp-landing-page-dan-qris-manual
plan: 02
subsystem: database, routes, testing
tags: [laravel-13, eloquent, migrations, form-requests, validation, tdd]
requires:
  - phase: 01-01
    provides: Laravel scaffold, products config contract, .env.example
provides:
  - Database schema: orders, payment_proofs, leads tables with constraints
  - Route contract: 15 named web routes for buyer, lead magnet, and admin flows
  - Validation contracts: 5 Form Request classes with locked rules
  - Controller shells: 9 controller classes ready for business logic
  - Admin seeder: env-only credential reader with safety skip
  - ScaffoldSmokeTest: 6 tests, 72 assertions validating all contracts
affects:
  - 01-04 (landing/lead), 01-05 (buyer order), 01-06 (admin)
tech-stack:
  added: []
  patterns:
    - "Eloquent models with explicit $fillable arrays and relationship methods"
    - "Token-based public buyer URLs ({invoice_token}, {download_token}) never numeric IDs"
    - "Form Request classes with authorize() returning true and rules() arrays"
    - "Controller shells accept $request and parameters matching route definitions"
    - "Migrations use database-agnostic column types (string, unsignedInteger, etc.)"
    - "Admin auth middleware redirects unauthenticated requests to admin.login"
key-files:
  created:
    - database/migrations/2024_01_01_000003_create_orders_table.php
    - database/migrations/2024_01_01_000004_create_payment_proofs_table.php
    - database/migrations/2024_01_01_000005_create_leads_table.php
    - database/migrations/2024_01_01_000006_add_is_admin_to_users_table.php
    - app/Models/Order.php
    - app/Models/PaymentProof.php
    - app/Models/Lead.php
    - routes/web.php
    - app/Http/Controllers/LandingPageController.php
    - app/Http/Controllers/OrderController.php
    - app/Http/Controllers/OrderPortalController.php
    - app/Http/Controllers/PaymentProofController.php
    - app/Http/Controllers/DownloadController.php
    - app/Http/Controllers/LeadMagnetController.php
    - app/Http/Controllers/Admin/AuthController.php
    - app/Http/Controllers/Admin/OrderController.php
    - app/Http/Controllers/Admin/OrderVerificationController.php
    - app/Http/Requests/StoreOrderRequest.php
    - app/Http/Requests/UploadPaymentProofRequest.php
    - app/Http/Requests/StoreLeadRequest.php
    - app/Http/Requests/AdminLoginRequest.php
    - app/Http/Requests/RejectPaymentRequest.php
    - database/seeders/AdminUserSeeder.php
    - tests/Feature/ScaffoldSmokeTest.php
  modified:
    - app/Models/User.php
    - database/seeders/DatabaseSeeder.php
    - bootstrap/app.php
key_decisions:
  - "AdminUserSeeder uses getenv() directly for runtime env reading (not env() helper which is for config files only)"
  - "auth middleware configured in bootstrap/app.php to redirect guests to admin.login and authenticated users to admin.orders.index"
  - "All public buyer routes use {invoice_token} or {download_token} parameters per D-01"
  - "UploadPaymentProofRequest enforces mimes:jpg,jpeg,png,webp with max:4096 per D-08"
  - "RejectPaymentRequest enforces reject_reason required string max:500 per D-07"
patterns-established:
  - "TDD per task: RED (failing test commit) → GREEN (implementation commit)"
  - "Controller shells expose method signatures matching route parameters for downstream plans"
  - "Migrations use database-agnostic types (string, unsignedInteger, boolean, foreignId, timestamp)"
  - "Model $fillable arrays list all mass-assignable fields explicitly"
requirements-completed: [REQ-001, REQ-002]
duration: 18min
completed: 2026-06-13
---

# Phase 01 Plan 02: Data Model, Route Contract, and Validation Shells

**Database schema with 3 new tables + users.is_admin, 15 named web routes, 9 controller shells, 5 Form Request classes, env-safe admin seeder, and 72-assertion ScaffoldSmokeTest validating all data and route contracts.**

## Performance

- **Duration:** 18 min
- **Started:** 2026-06-12T18:05:28Z
- **Completed:** 2026-06-12T18:23:30Z
- **Tasks:** 3 (all TDD, all autonomous)
- **Files modified:** 28

## Accomplishments

- **Database schema** with `orders` (invoice_token unique, status, reject_reason, verified audit), `payment_proofs` (order_id FK, disk/path/mime/size, status), `leads` (email unique, download_token unique, opt-in timestamps), and `is_admin` boolean on users table
- **Eloquent models** with explicit `$fillable`, `Order hasMany PaymentProofs`, `PaymentProof belongsTo Order`, `Order::isVerified()`, `User::isAdmin()`
- **15 named web routes** covering landing, buyer order/status/proof/download (all token-based), lead magnet store/show/download, and admin login/orders/confirm/reject/proof
- **5 Form Request classes** with validation rules matching D-01, D-07, D-08, D-14 locked decisions
- **9 controller shells** with method signatures matching route definitions — no business logic
- **AdminUserSeeder** reads `ADMIN_EMAIL` and `ADMIN_PASSWORD` from environment via `getenv()` with safety skip if empty; no hard-coded credentials
- **ScaffoldSmokeTest** with 6 test methods and 72 assertions validating schema columns, route names, token-based params, validation rules, seeder behavior, and proof preview auth gate

## Task Commits

Each task was committed atomically following TDD (RED → GREEN) pattern:

1. **Task 1: Migrations and Eloquent models**
   - `6528790` (test) — RED: failing test for migration schema columns
   - `aeb6991` (feat) — GREEN: migrations and models pass all assertions

2. **Task 2: Route contracts, controller shells, Form Requests**
   - `06a8e3a` (test) — RED: failing test for route contracts and validation
   - `bccbd09` (feat) — GREEN: routes, controllers, requests pass all assertions

3. **Task 3: Admin seeder, ScaffoldSmokeTest, proof preview auth**
   - `12d506b` (test) — RED: failing tests for admin seeder and proof preview auth
   - `c4a70b8` (feat) — GREEN: seeder and final test suite passes 72 assertions

## Files Created/Modified

**Migrations & Models (8 files):**
- `database/migrations/2024_01_01_000003_create_orders_table.php` — orders with invoice_token unique, buyer fields, amount, status, reject_reason, verified_at, verified_by FK
- `database/migrations/2024_01_01_000004_create_payment_proofs_table.php` — payment_proofs with order_id FK, disk, path, mime, size, status
- `database/migrations/2024_01_01_000005_create_leads_table.php` — leads with email unique, download_token unique, first_opted_at, last_opted_at
- `database/migrations/2024_01_01_000006_add_is_admin_to_users_table.php` — is_admin boolean default false on users
- `app/Models/Order.php` — $fillable, hasMany PaymentProofs, isVerified()
- `app/Models/PaymentProof.php` — $fillable, belongsTo Order
- `app/Models/Lead.php` — $fillable, date casts
- `app/Models/User.php` — added is_admin to fillable, is_admin boolean cast, isAdmin()

**Routes & Controllers (10 files):**
- `routes/web.php` — 15 named routes with throttle/auth/guest middleware
- `app/Http/Controllers/LandingPageController.php` — __invoke shell returning view('landing')
- `app/Http/Controllers/OrderController.php` — store(StoreOrderRequest) shell
- `app/Http/Controllers/OrderPortalController.php` — show(invoiceToken) shell
- `app/Http/Controllers/PaymentProofController.php` — store(UploadPaymentProofRequest, invoiceToken) shell
- `app/Http/Controllers/DownloadController.php` — ebook(invoiceToken) shell
- `app/Http/Controllers/LeadMagnetController.php` — store/downloadPage/download shells
- `app/Http/Controllers/Admin/AuthController.php` — create/store/destroy shells
- `app/Http/Controllers/Admin/OrderController.php` — index/proof shells
- `app/Http/Controllers/Admin/OrderVerificationController.php` — confirm/reject shells

**Form Requests (5 files):**
- `app/Http/Requests/StoreOrderRequest.php` — name required string max:120, email required email max:255, whatsapp required string max:32
- `app/Http/Requests/UploadPaymentProofRequest.php` — proof required file image mimes:jpg,jpeg,png,webp max:4096
- `app/Http/Requests/StoreLeadRequest.php` — email only (per D-14)
- `app/Http/Requests/AdminLoginRequest.php` — email required email, password required string
- `app/Http/Requests/RejectPaymentRequest.php` — reject_reason required string max:500 (per D-07)

**Seeders & Config (3 files):**
- `database/seeders/AdminUserSeeder.php` — reads ADMIN_EMAIL/ADMIN_PASSWORD via getenv(), skips with warning if empty
- `database/seeders/DatabaseSeeder.php` — calls AdminUserSeeder
- `bootstrap/app.php` — configured auth redirect guests to admin.login, redirect users to admin.orders.index

**Tests (1 file):**
- `tests/Feature/ScaffoldSmokeTest.php` — 6 tests, 72 assertions covering schema, routes, validation, seeder, auth

## Decisions Made

- **AdminUserSeeder uses `getenv()` not `env()`**: `env()` helper is for config files only and is affected by config caching. `getenv()` works reliably at seeder runtime for deployment credentials.
- **Auth middleware configuration in `bootstrap/app.php`**: Laravel 13 uses `redirectGuestsTo` and `redirectUsersTo` closures in the middleware configuration, not a `redirectTo` method on a controller.
- **SQLite driver required PHP extension**: PHP `sqlite3` and `pdo_sqlite` extensions must be loaded for testing with `:memory:` database. The `php -d extension=...` approach works as a workaround when the extensions can't be installed system-wide.
- **Form Request `authorize()` returns `true`**: All Form Requests use a simple `return true` authorize method because authorization is handled at the route/controller level, not in validation.

## Deviations from Plan

None — plan executed exactly as written.

## Issues Encountered

1. **PHP SQLite extension not installed** — Local PHP 8.3.30 lacks `pdo_sqlite` and `sqlite3` extensions. Resolved by downloading the `php8.3-sqlite3` .deb package and loading the `.so` files via `php -d extension=...` flags. All test commands use this workaround.
2. **`Artisan::call('db:seed')` resets environment** — The `putenv()`/`getenv()` approach failed when running seeder via `Artisan::call()` because the command environment resets. Fixed by instantiating the seeder directly (`new AdminUserSeeder->run()`) in tests.
3. **Doctrine Schema Manager unavailable** — Unique index check via `getDoctrineSchemaManager()` fails because `doctrine/dbal` is not installed by default in Laravel 13. Replaced with raw `PRAGMA index_list` / `PRAGMA index_info` SQLite introspection queries.

## Known Stubs

- `public/images/qris.png` — does not exist yet (from Plan 01-01). Deployment must replace with actual merchant QRIS image before launch.
- `storage/app/private/products/Ultimate-ChatGPT-Mastery-Bundle.zip` — does not exist yet. Will be created in Plan 01-03.
- `storage/app/private/lead-magnets/50-prompt-pemasaran-gratis.txt` — does not exist yet. Will be created in Plan 01-03.
- All controller methods are empty shells — business logic will be implemented in Plans 01-04, 01-05, and 01-06.

## Threat Model Compliance

| Threat ID | Category | Component | Status | Verification |
|-----------|----------|-----------|--------|--------------|
| T-01-01 | Spoofing | admin seed/login | mitigated | AdminUserSeeder consumes env-only credentials; auth middleware on admin group |
| T-01-02 | Tampering | migrations/status | mitigated | Order status is string-based; restricted statuses enforced in downstream plans |
| T-01-03 | Info Disclosure | file paths | mitigated | Route contract uses {invoice_token}/{download_token} for buyer URLs, never numeric IDs |
| T-01-04 | Tampering | proof upload | mitigated | Form Request enforces required file image mimes:jpg,jpeg,png,webp max:4096 |
| T-01-05 | Info Disclosure | tokens | mitigated | Schema uses unique high-entropy string tokens as primary public identifiers |

## Next Phase Readiness

- **Plan 01-03 ready**: Private asset fixtures, base Blade layout, CSS design tokens, and test fixture files can be created against established schema and route contracts.
- **Plan 01-04 ready**: Landing page and lead magnet controllers accept `StoreLeadRequest` and `LeadMagnetController` methods with correct signatures.
- **Plan 01-05 ready**: Order controllers, `StoreOrderRequest`, `UploadPaymentProofRequest`, `DownloadController` accept correct parameters for buyer flow.
- **Plan 01-06 ready**: Admin controllers, `AdminLoginRequest`, `RejectPaymentRequest`, auth middleware stack, and `admin.orders.proof` route all wired for admin workflow.

**No blockers.** All contracts are in place for downstream plans.

## Self-Check: PASSED

- [x] `php artisan migrate:fresh --env=testing` — all 7 migrations run cleanly
- [x] `php artisan route:list --except-vendor` — 15 named routes registered
- [x] All 6 `ScaffoldSmokeTest` tests pass (72 assertions)
- [x] `orders.invoice_token` has unique index confirmed via PRAGMA introspection
- [x] All 5 Form Request classes exist with correct rules
- [x] All 9 controller shells exist with method signatures matching route definitions
- [x] `AdminUserSeeder` uses `getenv()` only, no hard-coded credentials
- [x] 6 TDD commits in git log: 3 RED (test) → 3 GREEN (feat)
- [x] `bootstrap/app.php` configured for auth redirects
- [x] `.planning/` artifacts preserved

---

*Phase: 01-mvp-landing-page-dan-qris-manual*
*Completed: 2026-06-13*
