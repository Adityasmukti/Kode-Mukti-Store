# Phase 01: MVP Landing Page dan QRIS Manual - Research

**Researched:** 2026-06-13  
**Domain:** Laravel monolith, manual QRIS checkout, protected digital downloads  
**Confidence:** HIGH

<user_constraints>
## User Constraints (from CONTEXT.md)

Copied from `.planning/phases/01-mvp-landing-page-dan-qris-manual/01-CONTEXT.md`. [VERIFIED: workspace]

### Locked Decisions

#### Akses Download
- **D-01:** Pembeli mengakses halaman order/download memakai link invoice unik tanpa login.
- **D-02:** Sebelum pembayaran verified, link unik menampilkan status pending, instruksi QRIS, dan form upload atau re-upload bukti pembayaran.
- **D-03:** Setelah admin confirm pembayaran, link download tetap aktif tanpa expiry.
- **D-04:** Produk utama diberikan sebagai satu file ZIP bundle.

#### Admin Verifikasi
- **D-05:** Admin panel wajib dilindungi login admin email/password.
- **D-06:** Aksi admin MVP adalah `Confirm` dan `Reject` pembayaran.
- **D-07:** Reject perlu alasan singkat agar pembeli bisa memperbaiki dan upload ulang bukti.
- **D-08:** Bukti pembayaran hanya menerima gambar: JPG, PNG, atau WebP.
- **D-09:** Admin tidak perlu notifikasi otomatis di MVP; admin cukup cek dashboard manual.

#### Landing Page Copy
- **D-10:** Positioning utama adalah hemat waktu dan produktivitas untuk pemilik bisnis, kreator konten, dan profesional.
- **D-11:** CTA utama hero section adalah "Dapatkan Sekarang" dengan harga Rp 99.000 ditampilkan dekat tombol.
- **D-12:** Urgency menggunakan countdown promo yang configurable, bukan klaim kuota palsu.
- **D-13:** Jangan memakai testimoni palsu. Karena belum ada testimoni asli, gunakan benefit bullets, trust badge, guarantee, dan contoh isi produk sebagai social proof awal.

#### Lead Magnet
- **D-14:** Form lead magnet hanya meminta email untuk menjaga opt-in rate tetap tinggi.
- **D-15:** Setelah email tersimpan, user diarahkan ke halaman download lead magnet.
- **D-16:** Halaman download lead magnet boleh menampilkan soft CTA untuk membeli ebook.
- **D-17:** Email duplikat tetap boleh submit dan download ulang; sistem cukup update timestamp opt-in terakhir.
- **D-18:** Export CSV leads ditunda dari MVP.

### the agent's Discretion

- Saat user memilih "You decide", keputusan yang diambil: status pending dengan upload/re-upload bukti, CTA "Dapatkan Sekarang", countdown promo configurable, email-only lead magnet.

### Deferred Ideas (OUT OF SCOPE)

- Payment gateway automation through Midtrans/Xendit is deferred until registration friction is acceptable.
- Email marketing integration is deferred; lead capture is database-only in MVP.
- CSV export for leads is deferred.
- Buyer account/login system is deferred.
</user_constraints>

## Summary

Build this as one Laravel 13 full-stack monolith with Blade views, web routes, Eloquent models, Form Requests, Laravel session auth for admin, and Laravel private filesystem downloads. [CITED: https://laravel.com/docs/13.x/installation] [CITED: https://laravel.com/docs/13.x/routing] [CITED: https://laravel.com/docs/13.x/authentication] [CITED: https://laravel.com/docs/13.x/filesystem]

Use stored random order tokens for buyer invoice/download links; do not create buyer accounts. [VERIFIED: 01-CONTEXT.md] Keep proof images and ZIP files on the `local` private disk, and serve them only through controllers that check order status or lead token before calling `Storage::download()`. [CITED: https://laravel.com/docs/13.x/filesystem]

**Primary recommendation:** Scaffold Laravel in the workspace root via a temporary `laravel/laravel` project merge, then implement a minimal Blade + controller MVP with `orders`, `payment_proofs`, and `leads` tables. [VERIFIED: composer show] [VERIFIED: glob composer.json absent]

## Project Constraints (from AGENTS.md)

- No workspace `AGENTS.md` exists, so only global OpenCode rules apply. [VERIFIED: glob]
- Keep changes minimal, do not install extra packages without need, do not edit `.env`, and do not expose secrets. [VERIFIED: global OpenCode rules]

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| REQ-001 | Landing page Laravel dengan QRIS manual: order, QRIS, proof upload, admin confirm, active download after confirm. [VERIFIED: REQUIREMENTS.md] | Laravel web routes, private Storage, manual Auth admin, `orders` + `payment_proofs` model. [CITED: Laravel docs] |
| REQ-002 | Lead magnet instant download after email capture, database-only MVP. [VERIFIED: REQUIREMENTS.md] | `leads` model with duplicate email update and private lead magnet download controller. [VERIFIED: 01-CONTEXT.md] |
</phase_requirements>

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|--------------|----------------|-----------|
| Landing page + forms | Frontend Server / Blade | Browser | Laravel web routes render Blade and receive CSRF-protected forms. [CITED: https://laravel.com/docs/13.x/routing] |
| Order + QRIS manual flow | API / Backend | Database | Controller validates order input, persists status, and renders invoice state. [CITED: https://laravel.com/docs/13.x/validation] |
| Proof upload | API / Backend | Private Storage | Controller validates image upload and stores it on a configured disk. [CITED: https://laravel.com/docs/13.x/filesystem] |
| Admin verification | API / Backend | Frontend Server | Admin routes use Laravel session auth middleware and update order state. [CITED: https://laravel.com/docs/13.x/authentication] |
| Protected ZIP delivery | API / Backend | Private Storage | Controller checks token + `verified` status before `Storage::download()`. [CITED: https://laravel.com/docs/13.x/filesystem] |
| Lead magnet download | API / Backend | Private Storage | Email is stored first, then a tokenized download route streams the file. [VERIFIED: REQUIREMENTS.md] |

## Standard Stack

| Component | Recommendation | Version / Status | Why |
|-----------|----------------|------------------|-----|
| Framework | Laravel application skeleton `laravel/laravel` | latest v13.8.0 available; requires PHP `^8.3`. [VERIFIED: composer show] | Official Laravel skeleton for full-stack apps. [CITED: https://laravel.com/docs/13.x/installation] |
| Core framework | `laravel/framework` | latest v13.15.0 available; requires PHP `^8.3`. [VERIFIED: composer show] | Provides routing, validation, auth, storage, Eloquent, testing. [VERIFIED: composer show] |
| Views | Blade + Vite assets | bundled in Laravel skeleton. [CITED: https://laravel.com/docs/13.x/installation] | Lowest-complexity MVP; no SPA needed. [ASSUMED] |
| Auth | Laravel built-in browser/session auth | built into framework. [CITED: https://laravel.com/docs/13.x/authentication] | Admin-only login does not need Sanctum/Passport/API tokens. [CITED: https://laravel.com/docs/13.x/authentication] |
| Storage | Laravel `local` private disk | default root `storage/app/private`. [CITED: https://laravel.com/docs/13.x/filesystem] | Keeps proofs and ZIP outside public web root. [CITED: https://laravel.com/docs/13.x/filesystem] |
| Database | SQLite for local MVP; MySQL/PostgreSQL optional on VPS | Laravel default `.env` uses SQLite after new app creation. [CITED: https://laravel.com/docs/13.x/installation] | Fast MVP and easy migration later. [ASSUMED] |
| Tests | PHPUnit feature tests via `php artisan test` | skeleton requires `phpunit/phpunit ^12.5.12`. [VERIFIED: composer show] | Covers flows without browser automation. [CITED: https://laravel.com/docs/13.x/http-tests] |

**Installation shape:** create Laravel into a temporary directory, copy generated app files into the non-empty workspace root, preserve `.planning/` and `docs/`, then remove temp. [VERIFIED: workspace has no composer.json; workspace is non-empty]

## Package Legitimacy Audit

| Package | Registry | Evidence | slopcheck | Disposition |
|---------|----------|----------|-----------|-------------|
| `laravel/laravel` | Packagist/Composer | Official docs name Laravel installer/skeleton; Composer source `github.com/laravel/laravel`, v13.8.0. [CITED: https://laravel.com/docs/13.x/installation] [VERIFIED: composer show] | SLOP on PyPI only; false ecosystem check. [VERIFIED: slopcheck output] | Approved for Composer; do not install from PyPI/npm. |
| `laravel/framework` | Packagist/Composer | Composer source `github.com/laravel/framework`, v13.15.0, MIT. [VERIFIED: composer show] | SLOP on PyPI only; false ecosystem check. [VERIFIED: slopcheck output] | Approved for Composer; do not install from PyPI/npm. |

**Packages removed due to true slopcheck SLOP verdict:** none; slopcheck checked PyPI, not Packagist, so its verdict is not applicable to Composer packages. [VERIFIED: slopcheck output]  
**Packages flagged as suspicious:** none for Composer after official docs + Composer verification. [VERIFIED: composer show]

## Recommended MVP Architecture

```text
Visitor -> LandingPageController
  -> OrderController@store -> orders row + unique invoice_token -> invoice page
  -> LeadMagnetController@store -> leads upsert + download_token -> lead download

Buyer invoice token -> OrderPortalController@show
  pending/rejected -> QRIS instructions + proof upload form
  verified -> ZIP download button -> DownloadController@ebook

Admin login -> Admin\OrderController@index/show
  -> confirm/reject -> updates order + proof status
```

- Keep all public pages in `routes/web.php` because Laravel web routes provide sessions and CSRF protection. [CITED: https://laravel.com/docs/13.x/routing]
- Keep business mutations in controllers/services, not Blade templates. [ASSUMED]
- Store product ZIP and lead magnet files under `storage/app/private/products/` and `storage/app/private/lead-magnets/`. [CITED: https://laravel.com/docs/13.x/filesystem]
- Do not run `php artisan storage:link` for protected proof/product files; that command is for public disk exposure. [CITED: https://laravel.com/docs/13.x/filesystem]

## Data Model

### `orders`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | Primary key. [ASSUMED] |
| `invoice_token` | string unique | Public buyer link token; generate with high entropy and index unique. [ASSUMED] |
| `name`, `email`, `whatsapp` | string | Required checkout fields. [VERIFIED: REQUIREMENTS.md] |
| `amount` | unsigned integer | Store `99000` in rupiah cents-free integer. [VERIFIED: 01-CONTEXT.md] |
| `status` | string enum-like | `pending`, `proof_submitted`, `verified`, `rejected`. [VERIFIED: 01-CONTEXT.md] |
| `reject_reason` | text nullable | Required when rejecting. [VERIFIED: 01-CONTEXT.md] |
| `verified_at`, `verified_by` | timestamp/user nullable | Admin audit trail. [ASSUMED] |
| `created_at`, `updated_at` | timestamps | Laravel standard timestamps. [ASSUMED] |

### `payment_proofs`

| Column | Type | Notes |
|--------|------|-------|
| `order_id` | foreignId | Each upload belongs to an order. [ASSUMED] |
| `path`, `disk` | string | Store private path and disk name. [CITED: https://laravel.com/docs/13.x/filesystem] |
| `mime`, `size` | string/integer | Keep metadata for admin review. [CITED: https://laravel.com/docs/13.x/filesystem] |
| `status` | string | `submitted`, `accepted`, `rejected`, so re-uploads keep history. [VERIFIED: 01-CONTEXT.md] |
| `created_at`, `updated_at` | timestamps | Laravel standard timestamps. [ASSUMED] |

### `leads`

| Column | Type | Notes |
|--------|------|-------|
| `email` | string unique | Duplicate submit updates timestamp. [VERIFIED: 01-CONTEXT.md] |
| `download_token` | string unique | Tokenized lead magnet download route avoids exposing raw file path. [ASSUMED] |
| `first_opted_at`, `last_opted_at` | timestamps | Supports duplicate re-submit tracking. [VERIFIED: 01-CONTEXT.md] |
| `created_at`, `updated_at` | timestamps | Laravel standard timestamps. [ASSUMED] |

### `users`

- Use Laravel's default `users` model/table for admin login, plus an `is_admin` boolean or a single seeded admin account. [CITED: https://laravel.com/docs/13.x/authentication] [ASSUMED]
- Seed admin password via safe deployment secret input; do not hard-code credentials in code or commit `.env`. [VERIFIED: global OpenCode rules]

## Route / Controller Boundaries

| Route | Controller | Purpose | Middleware |
|-------|------------|---------|------------|
| `GET /` | `LandingPageController` | Landing page with sales CTA and lead magnet form. [VERIFIED: ROADMAP.md] | `web` |
| `POST /orders` | `OrderController@store` | Validate name/email/WhatsApp, create order, redirect to invoice token. [VERIFIED: REQUIREMENTS.md] | `web`, `throttle` |
| `GET /orders/{invoice_token}` | `OrderPortalController@show` | Show pending/rejected/verified state. [VERIFIED: 01-CONTEXT.md] | `web` |
| `POST /orders/{invoice_token}/proof` | `PaymentProofController@store` | Validate and store JPG/PNG/WebP proof. [VERIFIED: 01-CONTEXT.md] | `web`, `throttle` |
| `GET /orders/{invoice_token}/download` | `DownloadController@ebook` | Download ZIP only when order is verified. [VERIFIED: 01-CONTEXT.md] | `web` |
| `POST /lead-magnet` | `LeadMagnetController@store` | Upsert email and redirect to lead download page. [VERIFIED: 01-CONTEXT.md] | `web`, `throttle` |
| `GET /lead-magnet/{download_token}` | `LeadMagnetController@downloadPage` | Instant download page + soft CTA. [VERIFIED: 01-CONTEXT.md] | `web` |
| `GET /lead-magnet/{download_token}/download` | `LeadMagnetController@download` | Stream private lead magnet file. [VERIFIED: REQUIREMENTS.md] | `web` |
| `GET/POST /admin/login` | `Admin\AuthController` | Manual admin login/logout using `Auth::attempt()`. [CITED: https://laravel.com/docs/13.x/authentication] | `guest` / `throttle` |
| `GET /admin/orders` | `Admin\OrderController@index` | Pending/recent order dashboard. [VERIFIED: 01-CONTEXT.md] | `auth` |
| `POST /admin/orders/{order}/confirm` | `Admin\OrderVerificationController@confirm` | Mark verified and proof accepted. [VERIFIED: 01-CONTEXT.md] | `auth` |
| `POST /admin/orders/{order}/reject` | `Admin\OrderVerificationController@reject` | Require reason, mark rejected. [VERIFIED: 01-CONTEXT.md] | `auth` |

## File Storage and Protected Download Approach

- Use `Storage::disk('local')` for proofs, product ZIP, and lead magnet because Laravel's local disk defaults to private storage under `storage/app/private`. [CITED: https://laravel.com/docs/13.x/filesystem]
- Use `$uploadedFile->store('payment-proofs/'.$order->invoice_token, 'local')` so Laravel generates a safe unique name and returns a storable path. [CITED: https://laravel.com/docs/13.x/filesystem]
- Use `Storage::download($path, $downloadName)` from a controller after authorization checks. [CITED: https://laravel.com/docs/13.x/filesystem]
- Do not use public URLs or `Storage::url()` for paid product/proof files because public disk files are designed to be web-accessible. [CITED: https://laravel.com/docs/13.x/filesystem]
- Keep the paid link permanent by storing `invoice_token` and checking `status === verified`; do not use expiring temporary URLs for the ebook. [VERIFIED: 01-CONTEXT.md]

## Admin Auth and Verification Flow

1. Admin visits `/admin/login`, submits email/password, controller validates and calls `Auth::attempt()`. [CITED: https://laravel.com/docs/13.x/authentication]
2. On success, regenerate session to prevent session fixation. [CITED: https://laravel.com/docs/13.x/authentication]
3. Admin routes use `auth` middleware. [CITED: https://laravel.com/docs/13.x/authentication]
4. Dashboard lists `proof_submitted` first, then rejected/pending/verified. [ASSUMED]
5. Confirm sets order `verified`, `verified_at`, `verified_by`, and current proof `accepted`. [VERIFIED: 01-CONTEXT.md]
6. Reject requires `reject_reason`, sets order `rejected`, marks current proof `rejected`, and shows reason on buyer invoice page. [VERIFIED: 01-CONTEXT.md]
7. No email/WhatsApp notification is required for MVP. [VERIFIED: 01-CONTEXT.md]

## Upload Validation / Security

- Use Form Request classes for `StoreOrderRequest`, `UploadPaymentProofRequest`, `StoreLeadRequest`, `AdminLoginRequest`, and `RejectPaymentRequest`. [CITED: https://laravel.com/docs/13.x/validation]
- Proof rules: `required`, `file`, `image`, `mimes:jpg,jpeg,png,webp`, `max:4096`; exact max size is a planner decision, but 4 MB is enough for phone screenshots in MVP. [CITED: https://laravel.com/docs/13.x/validation] [ASSUMED]
- Never trust `getClientOriginalName()` / `getClientOriginalExtension()` for storage decisions; Laravel docs call these unsafe and recommend `hashName()` / `extension()`. [CITED: https://laravel.com/docs/13.x/filesystem]
- Store proof files privately and show admin previews through an authenticated controller route, not through `public/storage`. [CITED: https://laravel.com/docs/13.x/filesystem]
- Add throttle limits to order creation, proof upload, lead capture, and admin login; Laravel route rate limiting returns HTTP 429 when exceeded. [CITED: https://laravel.com/docs/13.x/routing]
- Validate WhatsApp as normalized string, not integer, because phone numbers can contain leading zeroes and `+62`. [ASSUMED]
- Add model `$fillable` only for validated fields; Laravel 13 Form Requests can reject unknown fields with `FailOnUnknownFields`. [CITED: https://laravel.com/docs/13.x/validation]

## UI / Page List

| Page | Required Sections / States |
|------|----------------------------|
| Landing `/` | PAS copy, benefits, product contents, trust/guarantee, price Rp 99.000 near CTA, configurable countdown, no fake testimonials, lead magnet form. [VERIFIED: 01-CONTEXT.md] |
| Order created / invoice `/orders/{token}` | QRIS image/instructions, amount Rp 99.000, status, proof upload/re-upload. [VERIFIED: 01-CONTEXT.md] |
| Verified order | Download ZIP button and support note. [VERIFIED: REQUIREMENTS.md] |
| Rejected order | Reject reason + re-upload form. [VERIFIED: 01-CONTEXT.md] |
| Lead magnet download | Instant download button + soft CTA to buy ebook. [VERIFIED: 01-CONTEXT.md] |
| Admin login | Email/password form. [VERIFIED: 01-CONTEXT.md] |
| Admin orders | Pending proof list, order detail, confirm/reject actions. [VERIFIED: 01-CONTEXT.md] |

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Admin sessions | Custom cookies/token auth | Laravel Auth + session guard. [CITED: https://laravel.com/docs/13.x/authentication] | Built-in auth handles credential checks and sessions. [CITED: docs] |
| Input validation | Manual `if` chains | Form Requests / validator. [CITED: https://laravel.com/docs/13.x/validation] | Centralized rules and error redirects. [CITED: docs] |
| File serving | Direct public links | `Storage::download()` after status/token checks. [CITED: https://laravel.com/docs/13.x/filesystem] | Prevents public exposure. [CITED: docs] |
| File names | Client original names | Laravel generated/hash names. [CITED: https://laravel.com/docs/13.x/filesystem] | Client file names/extensions are unsafe. [CITED: docs] |
| Payment automation | QRIS gateway abstraction | Manual status workflow only. [VERIFIED: 01-CONTEXT.md] | Gateways are deferred. [VERIFIED: 01-CONTEXT.md] |

## Common Pitfalls

- **Public storage leak:** `storage:link` makes `storage/app/public` web-accessible, so paid ZIP/proofs must not be placed there. [CITED: https://laravel.com/docs/13.x/filesystem]
- **Token guessed or enumerated:** use high-entropy unique tokens and query by token; do not expose incremental order IDs in buyer URLs. [ASSUMED]
- **Reject flow dead-end:** rejected orders must keep the same invoice link and allow re-upload with reason visible. [VERIFIED: 01-CONTEXT.md]
- **Admin auth overbuilt:** Sanctum/Passport/API auth is unnecessary for a browser-only monolith admin panel. [CITED: https://laravel.com/docs/13.x/authentication]
- **Tests depending on GD:** Laravel's `UploadedFile::fake()->image()` requires PHP GD, which is missing locally; use fixture files or install `php-gd`. [CITED: https://laravel.com/docs/13.x/filesystem] [VERIFIED: php -m]

## Code Examples

```php
// routes/web.php [CITED: https://laravel.com/docs/13.x/routing]
Route::get('/', LandingPageController::class)->name('landing');
Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:orders');
Route::get('/orders/{token}', [OrderPortalController::class, 'show'])->name('orders.show');
Route::post('/orders/{token}/proof', [PaymentProofController::class, 'store'])->middleware('throttle:uploads');
Route::get('/orders/{token}/download', [DownloadController::class, 'ebook'])->name('orders.download');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'create'])->middleware('guest')->name('login');
    Route::post('/login', [Admin\AuthController::class, 'store'])->middleware('throttle:login');
    Route::middleware('auth')->group(function () {
        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/confirm', [Admin\OrderVerificationController::class, 'confirm']);
        Route::post('/orders/{order}/reject', [Admin\OrderVerificationController::class, 'reject']);
    });
});
```

```php
// Protected download pattern [CITED: https://laravel.com/docs/13.x/filesystem]
abort_unless($order->status === 'verified', 403);
return Storage::disk('local')->download(
    config('products.ebook_zip_path'),
    'Ultimate-ChatGPT-Mastery-Bundle.zip'
);
```

## Validation Architecture

### Test Framework

| Property | Value |
|----------|-------|
| Framework | PHPUnit via Laravel HTTP feature tests. [VERIFIED: composer show] [CITED: https://laravel.com/docs/13.x/http-tests] |
| Config file | `phpunit.xml` after scaffold; currently absent. [VERIFIED: glob] |
| Quick run command | `php artisan test --filter=OrderFlowTest` [ASSUMED] |
| Full suite command | `php artisan test` [CITED: https://laravel.com/docs/13.x/http-tests] |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|--------------|
| REQ-001 | Landing page loads with price/CTA/lead form. [VERIFIED: REQUIREMENTS.md] | Feature | `php artisan test --filter=LandingPageTest` | ❌ Wave 0 |
| REQ-001 | Visitor creates order and sees QRIS/invoice pending state. [VERIFIED: REQUIREMENTS.md] | Feature | `php artisan test --filter=OrderFlowTest` | ❌ Wave 0 |
| REQ-001 | Buyer uploads JPG/PNG/WebP proof; invalid file rejected. [VERIFIED: 01-CONTEXT.md] | Feature + storage fake/fixture | `php artisan test --filter=PaymentProofUploadTest` | ❌ Wave 0 |
| REQ-001 | Admin login required; confirm activates ZIP download; reject shows reason/re-upload. [VERIFIED: 01-CONTEXT.md] | Feature | `php artisan test --filter=AdminVerificationTest` | ❌ Wave 0 |
| REQ-002 | Lead email is saved/upserted and redirects to instant download page. [VERIFIED: REQUIREMENTS.md] | Feature | `php artisan test --filter=LeadMagnetTest` | ❌ Wave 0 |

### Sampling Rate

- **Per task commit:** targeted `php artisan test --filter=...`. [ASSUMED]
- **Per wave merge:** `php artisan test`. [CITED: https://laravel.com/docs/13.x/http-tests]
- **Phase gate:** Full suite green plus manual browser smoke for landing → order → upload → admin confirm → download. [ASSUMED]

### Wave 0 Gaps

- [ ] Laravel scaffold (`composer.json`, `artisan`, app structure). [VERIFIED: glob absent]
- [ ] `phpunit.xml` and test base from scaffold. [VERIFIED: glob absent]
- [ ] Fixture image files for upload tests or install PHP GD; local PHP has no `gd` module. [VERIFIED: php -m] [CITED: https://laravel.com/docs/13.x/filesystem]
- [ ] Private product ZIP and lead magnet files placed under storage or generated from `docs/` assets. [VERIFIED: 01-CONTEXT.md]

## Environment Availability

| Dependency | Required By | Available | Version | Fallback |
|------------|-------------|-----------|---------|----------|
| PHP | Laravel 13 | ✓ | 8.3.30 [VERIFIED: php --version] | — |
| Composer | Laravel scaffold/install | ✓ | 2.8.12 [VERIFIED: composer --version] | — |
| Node | Vite asset build | ✓ | v24.16.0 [VERIFIED: node --version] | Use plain Blade/CSS if Vite blocked. [ASSUMED] |
| npm | Vite asset build | ✓ | 11.13.0 [VERIFIED: npm --version] | Use plain Blade/CSS if Vite blocked. [ASSUMED] |
| SQLite CLI | Local MVP DB | ✓ | 3.45.1 [VERIFIED: sqlite3 --version] | MySQL on VPS. [ASSUMED] |
| PHP `zip` extension | ZIP file handling | ✓ | module loaded [VERIFIED: php -m] | Use prebuilt ZIP artifact. [ASSUMED] |
| ZIP CLI | Building bundle from docs | ✓ | Info-ZIP 3.0 [VERIFIED: zip -v] | Prebuild bundle manually. [ASSUMED] |
| PHP GD | `UploadedFile::fake()->image()` tests | ✗ | absent [VERIFIED: php -m] | Use committed small fixture images or install `php-gd`. [CITED: https://laravel.com/docs/13.x/filesystem] |

**Missing dependencies with no fallback:** none identified. [VERIFIED: environment probes]  
**Missing dependencies with fallback:** PHP GD for image factory tests; use fixture images. [VERIFIED: php -m]

## Security Domain

| ASVS Category | Applies | Standard Control |
|---------------|---------|------------------|
| V2 Authentication | Yes | Laravel session auth for admin. [CITED: https://laravel.com/docs/13.x/authentication] |
| V3 Session Management | Yes | Regenerate session on login; invalidate/regenerate CSRF on logout. [CITED: https://laravel.com/docs/13.x/authentication] |
| V4 Access Control | Yes | `auth` middleware on admin routes and token/status checks on buyer downloads. [CITED: docs] [ASSUMED] |
| V5 Input Validation | Yes | Form Requests + file validation. [CITED: https://laravel.com/docs/13.x/validation] |
| V6 Cryptography | Yes | Use Laravel password hashing via Auth; do not custom-hash or store raw passwords. [CITED: https://laravel.com/docs/13.x/authentication] |
| V12 Files | Yes | Private disk, safe generated names, MIME/extension validation. [CITED: https://laravel.com/docs/13.x/filesystem] |

## Planning Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Non-empty workspace blocks `composer create-project .` [VERIFIED: workspace files exist] | Scaffold task fails. | Create temp Laravel app, copy files into root preserving `.planning/` and `docs/`. [ASSUMED] |
| Product ZIP not prepared [VERIFIED: context lists docs as content assets] | Verified buyers cannot download. | Plan explicit task to build/place `Ultimate-ChatGPT-Mastery-Bundle.zip` under private storage and test exists. [ASSUMED] |
| Proof files accidentally public [CITED: filesystem docs] | Payment proof/privacy leak. | Use `local` disk only; no public URLs for proofs. [CITED: docs] |
| Admin seed credentials mishandled [VERIFIED: global security rules] | Security incident. | Seed via prompt/secret, never commit credentials or `.env`. [VERIFIED: global rules] |
| QRIS amount/manual process mismatch [VERIFIED: REQUIREMENTS.md] | Buyer confusion/admin workload. | Show fixed Rp 99.000 and clear upload instructions on invoice page. [VERIFIED: 01-CONTEXT.md] |
| Lead duplicate handling overcomplicated [VERIFIED: 01-CONTEXT.md] | Scope creep. | Unique email upsert + `last_opted_at`; no CSV/email automation. [VERIFIED: 01-CONTEXT.md] |

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | Blade + Vite is the lowest-complexity UI for this MVP. | Standard Stack | If user wants Tailwind/Livewire/Inertia, UI tasks change. |
| A2 | SQLite is acceptable for local MVP planning. | Standard Stack | If VPS must use MySQL immediately, migrations/config tasks change. |
| A3 | 4 MB is enough for proof screenshots. | Upload Validation | Some real screenshots may fail; planner can set 8 MB. |
| A4 | Admin uses default `users` table plus `is_admin` or seeded single admin. | Data Model | If separate admin guard/table is required, auth config tasks grow. |
| A5 | Product ZIP can be prebuilt from docs assets or manually placed. | Planning Risks | If app must generate ZIP dynamically, implementation expands. |

## Open Questions (RESOLVED)

1. **RESOLVED: Where is the production QRIS image stored?**  
    - What we know: QRIS manual is required. [VERIFIED: REQUIREMENTS.md]  
    - Decision: MVP stores the QRIS display image at `public/images/qris.png` because it must be visible to buyers on the invoice page. [RESOLVED]
    - Implementation note: scaffold should include a placeholder `public/images/qris.png` or documented placeholder asset, and deployment must replace it with the real merchant QRIS image before launch. [RESOLVED]
2. **RESOLVED: Should production DB be SQLite or MySQL on VPS?**  
    - What we know: own VPS + Cloudflared tunnel is target. [VERIFIED: STATE.md]  
    - Decision: use SQLite for MVP to reduce deployment and maintenance complexity. [RESOLVED]
    - Implementation note: keep Laravel migrations database-driver agnostic so MySQL can be introduced later if traffic/concurrency grows. [RESOLVED]

## Sources

### Primary (HIGH confidence)
- `.planning/phases/01-mvp-landing-page-dan-qris-manual/01-CONTEXT.md` — locked MVP decisions. [VERIFIED: workspace]
- `.planning/REQUIREMENTS.md` — REQ-001/REQ-002 acceptance criteria. [VERIFIED: workspace]
- `.planning/ROADMAP.md` and `.planning/STATE.md` — phase scope and project state. [VERIFIED: workspace]
- Laravel 13 Installation — https://laravel.com/docs/13.x/installation [CITED]
- Laravel 13 Routing — https://laravel.com/docs/13.x/routing [CITED]
- Laravel 13 Validation — https://laravel.com/docs/13.x/validation [CITED]
- Laravel 13 Filesystem — https://laravel.com/docs/13.x/filesystem [CITED]
- Laravel 13 Authentication — https://laravel.com/docs/13.x/authentication [CITED]
- Laravel 13 HTTP Tests — https://laravel.com/docs/13.x/http-tests [CITED]
- Composer package metadata for `laravel/laravel` and `laravel/framework`. [VERIFIED: composer show]

### Secondary (MEDIUM confidence)
- Local environment probes for PHP/Composer/Node/npm/SQLite/PHP modules/ZIP CLI. [VERIFIED: bash probes]

### Tertiary (LOW confidence)
- Assumptions listed in the Assumptions Log. [ASSUMED]

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — Laravel and framework versions verified with Composer and official docs. [VERIFIED: composer show] [CITED: Laravel docs]
- Architecture: HIGH — direct mapping from locked decisions to Laravel built-ins. [VERIFIED: 01-CONTEXT.md] [CITED: Laravel docs]
- Pitfalls/security: MEDIUM — official docs cover storage/auth/validation; token entropy and ops details need implementation review. [CITED: Laravel docs] [ASSUMED]

**Research date:** 2026-06-13  
**Valid until:** 2026-07-13 for Laravel 13 package versions; re-run `composer show laravel/laravel laravel/framework --all` before implementation. [ASSUMED]
