# Phase 01: MVP Landing Page dan QRIS Manual - Pattern Map

**Mapped:** 2026-06-13  
**Files analyzed:** 42 planned files / file groups  
**Analogs found:** 0 / 42  
**Codebase status:** Greenfield Laravel scaffold. No `composer.json`, `artisan`, `app/`, `routes/`, `resources/`, `database/`, or `tests/` application files exist yet.

## File Classification

| New/Modified File | Role | Data Flow | Closest Analog | Match Quality |
|---|---|---:|---|---|
| `composer.json`, `composer.lock`, `artisan`, `bootstrap/app.php` | config | request-response | none | greenfield scaffold |
| `config/products.php` | config | request-response | none | greenfield |
| `config/filesystems.php` | config | file-I/O | none | greenfield scaffold |
| `config/auth.php` | config | request-response | none | greenfield scaffold |
| `routes/web.php` | route | request-response | none | greenfield |
| `app/Http/Controllers/LandingPageController.php` | controller | request-response | none | greenfield |
| `app/Http/Controllers/OrderController.php` | controller | CRUD | none | greenfield |
| `app/Http/Controllers/OrderPortalController.php` | controller | request-response | none | greenfield |
| `app/Http/Controllers/PaymentProofController.php` | controller | file-I/O | none | greenfield |
| `app/Http/Controllers/DownloadController.php` | controller | file-I/O | none | greenfield |
| `app/Http/Controllers/LeadMagnetController.php` | controller | CRUD + file-I/O | none | greenfield |
| `app/Http/Controllers/Admin/AuthController.php` | controller | request-response | none | greenfield |
| `app/Http/Controllers/Admin/OrderController.php` | controller | CRUD | none | greenfield |
| `app/Http/Controllers/Admin/OrderVerificationController.php` | controller | CRUD | none | greenfield |
| `app/Http/Requests/StoreOrderRequest.php` | utility | request-response | none | greenfield |
| `app/Http/Requests/UploadPaymentProofRequest.php` | utility | file-I/O | none | greenfield |
| `app/Http/Requests/StoreLeadRequest.php` | utility | request-response | none | greenfield |
| `app/Http/Requests/AdminLoginRequest.php` | utility | request-response | none | greenfield |
| `app/Http/Requests/RejectPaymentRequest.php` | utility | request-response | none | greenfield |
| `app/Models/Order.php` | model | CRUD | none | greenfield |
| `app/Models/PaymentProof.php` | model | CRUD + file-I/O metadata | none | greenfield |
| `app/Models/Lead.php` | model | CRUD | none | greenfield |
| `app/Models/User.php` | model | CRUD/auth | none | greenfield scaffold |
| `database/migrations/*_create_orders_table.php` | migration | CRUD | none | greenfield |
| `database/migrations/*_create_payment_proofs_table.php` | migration | CRUD | none | greenfield |
| `database/migrations/*_create_leads_table.php` | migration | CRUD | none | greenfield |
| `database/migrations/*_add_is_admin_to_users_table.php` | migration | CRUD/auth | none | greenfield |
| `database/seeders/AdminUserSeeder.php` | utility | batch | none | greenfield |
| `resources/views/layouts/app.blade.php` | component | request-response | none | greenfield |
| `resources/views/landing.blade.php` | component | request-response | none | greenfield |
| `resources/views/orders/show.blade.php` | component | request-response + file-I/O form | none | greenfield |
| `resources/views/lead-magnet/show.blade.php` | component | request-response + file download CTA | none | greenfield |
| `resources/views/admin/auth/login.blade.php` | component | request-response | none | greenfield |
| `resources/views/admin/orders/index.blade.php` | component | request-response + CRUD actions | none | greenfield |
| `resources/css/app.css` | utility | transform | none | greenfield scaffold |
| `resources/js/app.js` | utility | event-driven | none | greenfield scaffold |
| `public/images/qris-placeholder.*` or configured QRIS asset | asset/config | file-I/O | none | greenfield |
| `storage/app/private/products/Ultimate-ChatGPT-Mastery-Bundle.zip` | asset | file-I/O | none | greenfield |
| `storage/app/private/lead-magnets/*` | asset | file-I/O | none | greenfield |
| `tests/Feature/LandingPageTest.php` | test | request-response | none | greenfield scaffold |
| `tests/Feature/OrderFlowTest.php` | test | CRUD | none | greenfield scaffold |
| `tests/Feature/PaymentProofUploadTest.php` | test | file-I/O | none | greenfield scaffold |
| `tests/Feature/AdminVerificationTest.php` | test | CRUD/auth | none | greenfield scaffold |
| `tests/Feature/LeadMagnetTest.php` | test | CRUD + file-I/O | none | greenfield scaffold |

## Pattern Assignments

### Greenfield scaffold files

**Applies to:** `composer.json`, `artisan`, `bootstrap/app.php`, base `config/*`, default `app/Models/User.php`, default test bootstrap.  
**Analog:** none in repository.  
**Planner instruction:** create official Laravel skeleton in a temporary directory, then merge into this non-empty workspace while preserving `.planning/` and `docs/`.

**Research source:** `01-RESEARCH.md` lines 85-98.

```text
Framework: Laravel application skeleton `laravel/laravel`
Views: Blade + Vite assets
Auth: Laravel built-in browser/session auth
Storage: Laravel `local` private disk
Tests: PHPUnit feature tests via `php artisan test`
Installation shape: create Laravel into a temporary directory, copy generated app files into root, preserve `.planning/` and `docs/`.
```

---

### `routes/web.php` (route, request-response)

**Analog:** none in repository.  
**Pattern to establish:** all public, buyer-token, lead magnet, and admin browser routes live in `routes/web.php` with CSRF/session support.

**Research source:** `01-RESEARCH.md` lines 168-183 and 243-261.

```php
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

---

### Public buyer controllers

**Applies to:** `LandingPageController`, `OrderController`, `OrderPortalController`.  
**Analog:** none in repository.  
**Pattern to establish:** thin controllers; validate with Form Requests; persist through Eloquent; redirect to tokenized invoice URL; never create buyer accounts.

**Decision source:** `01-CONTEXT.md` lines 18-23 and 31-42.  
**Research source:** `01-RESEARCH.md` lines 111-119 and 172-177.

```php
// OrderController@store shape
$order = Order::create([
    'invoice_token' => Str::random(48),
    'name' => $request->validated('name'),
    'email' => $request->validated('email'),
    'whatsapp' => $request->validated('whatsapp'),
    'amount' => config('products.price'),
    'status' => 'pending',
]);

return redirect()->route('orders.show', $order->invoice_token);
```

---

### Payment proof and protected download controllers

**Applies to:** `PaymentProofController`, `DownloadController`, proof preview route if added.  
**Analog:** none in repository.  
**Pattern to establish:** private `local` disk only; status/token checks before any file response; never expose paid ZIP/proofs via public URLs.

**Research source:** `01-RESEARCH.md` lines 185-191 and 203-209.

```php
// PaymentProofController@store shape
$path = $request->file('proof')->store(
    'payment-proofs/'.$order->invoice_token,
    'local'
);

$order->paymentProofs()->create([
    'disk' => 'local',
    'path' => $path,
    'mime' => $request->file('proof')->getMimeType(),
    'size' => $request->file('proof')->getSize(),
    'status' => 'submitted',
]);

$order->update(['status' => 'proof_submitted', 'reject_reason' => null]);
```

**Protected download pattern** (`01-RESEARCH.md` lines 264-270):

```php
abort_unless($order->status === 'verified', 403);

return Storage::disk('local')->download(
    config('products.ebook_zip_path'),
    'Ultimate-ChatGPT-Mastery-Bundle.zip'
);
```

---

### Lead magnet controller and model

**Applies to:** `LeadMagnetController`, `Lead`, `leads` migration, lead views/tests.  
**Analog:** none in repository.  
**Pattern to establish:** email-only upsert; duplicate emails update `last_opted_at`; redirect to tokenized download page; stream private lead magnet only through controller.

**Decision source:** `01-CONTEXT.md` lines 37-42.  
**Research source:** `01-RESEARCH.md` lines 154-161 and 177-179.

```php
$lead = Lead::updateOrCreate(
    ['email' => $request->validated('email')],
    [
        'download_token' => DB::raw('COALESCE(download_token, ?)'),
        'last_opted_at' => now(),
    ]
);

return redirect()->route('lead-magnet.show', $lead->download_token);
```

**Note:** implement token generation without relying on `DB::raw()` placeholder if it reduces clarity; ensure duplicate submits keep or regenerate a valid unique token deterministically.

---

### Admin auth and verification controllers

**Applies to:** `Admin/AuthController`, `Admin/OrderController`, `Admin/OrderVerificationController`, admin views.  
**Analog:** none in repository.  
**Pattern to establish:** Laravel session auth, `auth` middleware on admin routes, session regeneration on login, no custom cookies/tokens, confirm/reject only.

**Decision source:** `01-CONTEXT.md` lines 24-29.  
**Research source:** `01-RESEARCH.md` lines 193-201 and 225-233.

```php
// Admin\AuthController@store shape
if (Auth::attempt($request->validated())) {
    $request->session()->regenerate();
    return redirect()->intended(route('admin.orders.index'));
}

return back()->withErrors([
    'email' => 'Email atau password tidak valid.',
])->onlyInput('email');
```

```php
// Admin\OrderVerificationController shape
$order->update([
    'status' => 'verified',
    'verified_at' => now(),
    'verified_by' => $request->user()->id,
    'reject_reason' => null,
]);

$order->paymentProofs()->latest()->first()?->update(['status' => 'accepted']);
```

```php
// reject shape
$order->update([
    'status' => 'rejected',
    'reject_reason' => $request->validated('reject_reason'),
]);

$order->paymentProofs()->latest()->first()?->update(['status' => 'rejected']);
```

---

### Form Requests

**Applies to:** `StoreOrderRequest`, `UploadPaymentProofRequest`, `StoreLeadRequest`, `AdminLoginRequest`, `RejectPaymentRequest`.  
**Analog:** none in repository.  
**Pattern to establish:** one Form Request per mutation; controllers consume only `$request->validated()`; proof upload allows JPG/PNG/WebP only.

**Research source:** `01-RESEARCH.md` lines 203-211.

```php
// UploadPaymentProofRequest::rules()
return [
    'proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
];
```

```php
// StoreOrderRequest::rules()
return [
    'name' => ['required', 'string', 'max:120'],
    'email' => ['required', 'email', 'max:255'],
    'whatsapp' => ['required', 'string', 'max:32'],
];
```

---

### Models and migrations

**Applies to:** `Order`, `PaymentProof`, `Lead`, `User` admin flag, migrations.  
**Analog:** none in repository.  
**Pattern to establish:** Eloquent models with explicit `$fillable`, relationships, unique high-entropy public tokens, enum-like string statuses.

**Research source:** `01-RESEARCH.md` lines 129-166.

```php
// Order relationships/status shape
public function paymentProofs(): HasMany
{
    return $this->hasMany(PaymentProof::class);
}

public function isVerified(): bool
{
    return $this->status === 'verified';
}
```

Required tables:

- `orders`: `invoice_token` unique, buyer contact fields, `amount`, `status`, `reject_reason`, `verified_at`, `verified_by`, timestamps.
- `payment_proofs`: `order_id`, `path`, `disk`, `mime`, `size`, `status`, timestamps.
- `leads`: `email` unique, `download_token` unique, `first_opted_at`, `last_opted_at`, timestamps.
- `users`: Laravel default table plus `is_admin` boolean or equivalent admin check.

---

### Blade views and UI assets

**Applies to:** `resources/views/*`, `resources/css/app.css`, minimal `resources/js/app.js` if needed.  
**Analog:** none in repository.  
**Pattern to establish:** Blade + plain CSS/Vite; no React, Vue, Inertia, Livewire, Tailwind, shadcn, icon package, or fake testimonials.

**UI source:** `01-UI-SPEC.md` lines 16-27, 63-77, 105-166, 184-191.

```text
Design tokens:
- Background: #FFF7ED
- Card/form surface: #FFFFFF
- Accent CTA/focus/price/countdown: #F97316
- Destructive reject state only: #DC2626
- Body font: system UI stack
- Body 16px/400, Label 14px/600, Heading 24px/600, Display 40px/600
- Buttons: minimum 44px high, full-width on mobile where useful
```

Page contracts:

- Landing `/`: PAS hero, CTA “Dapatkan Sekarang”, price `Rp 99.000`, lead magnet CTA, benefits, product preview, countdown promo, FAQ/support note.
- Invoice `/orders/{token}`: pending/proof-submitted/rejected/verified states; QRIS instructions; upload/re-upload; verified ZIP download.
- Admin orders: authenticated page, desktop table + mobile cards, no delete action.
- Lead magnet page: instant download button + soft CTA to ebook.

---

### Feature tests

**Applies to:** `tests/Feature/LandingPageTest.php`, `OrderFlowTest.php`, `PaymentProofUploadTest.php`, `AdminVerificationTest.php`, `LeadMagnetTest.php`.  
**Analog:** none in repository.  
**Pattern to establish:** Laravel HTTP feature tests after scaffold; use file fixtures for upload tests because local PHP GD is unavailable.

**Research source:** `01-RESEARCH.md` lines 273-304 and 307-321.

```text
Targeted commands:
- php artisan test --filter=LandingPageTest
- php artisan test --filter=OrderFlowTest
- php artisan test --filter=PaymentProofUploadTest
- php artisan test --filter=AdminVerificationTest
- php artisan test --filter=LeadMagnetTest

Full suite:
- php artisan test
```

## Shared Patterns

### Authentication

**Source:** Research only, no code analog. `01-RESEARCH.md` lines 193-201.  
**Apply to:** all `Admin/*` controllers and admin routes.

```text
Use Laravel Auth + session guard.
On successful login: regenerate the session.
Admin routes: `auth` middleware.
No custom cookies, no Sanctum/Passport, no buyer accounts.
```

### Validation

**Source:** Research only, no code analog. `01-RESEARCH.md` lines 203-211.  
**Apply to:** all POST routes.

```text
Use Form Request classes:
- StoreOrderRequest
- UploadPaymentProofRequest
- StoreLeadRequest
- AdminLoginRequest
- RejectPaymentRequest

Proof upload rule: required file image mimes:jpg,jpeg,png,webp max:4096.
Controllers must use validated data only.
```

### Private file handling

**Source:** Research only, no code analog. `01-RESEARCH.md` lines 185-191 and 237-240.  
**Apply to:** proof uploads, ebook ZIP download, lead magnet download.

```text
Use Storage::disk('local') for private files.
Do not run/use public storage links for paid/proof files.
Do not expose Storage::url() for protected files.
Serve downloads only from controllers after token/status checks.
```

### Buyer access tokens

**Source:** `01-CONTEXT.md` lines 18-23; `01-RESEARCH.md` lines 56-58 and 235-239.  
**Apply to:** orders and leads.

```text
Use stored random tokens:
- order invoice link: `/orders/{invoice_token}`
- lead magnet link: `/lead-magnet/{download_token}`

Do not expose incremental order IDs in public buyer URLs.
Paid ebook link remains active after `verified` status; no expiry required.
```

### UI conventions

**Source:** `01-UI-SPEC.md` lines 16-27 and 184-191.  
**Apply to:** all Blade views.

```text
Blade + plain CSS/Vite only.
Every input needs visible label and validation error area.
Focus ring uses #F97316 with at least 2px outline/box-shadow.
Submit disabled/loading copy: “Memproses...”.
Status badges include text labels, not color-only indicators.
```

## No Analog Found

All planned application files have no existing codebase analog because this workspace currently contains only planning artifacts and product content assets.

| File / Group | Role | Data Flow | Reason |
|---|---|---|---|
| Laravel scaffold | config | request-response | No existing Laravel app or `composer.json`. |
| Public/order controllers | controller | request-response / CRUD | No `app/Http/Controllers`. |
| Admin controllers | controller | auth + CRUD | No auth/admin code exists. |
| Form Requests | utility | request-response / file-I/O | No `app/Http/Requests`. |
| Models/migrations | model/migration | CRUD | No `app/Models` or `database/migrations`. |
| Blade views/assets | component/utility | request-response | No `resources/views` or `resources/css`. |
| Feature tests | test | request-response / CRUD / file-I/O | No `tests` directory. |
| Private downloadable assets | asset | file-I/O | Product source docs exist, but no generated private ZIP/lead magnet artifact. |

## Greenfield Laravel Conventions Planner Should Establish

1. Scaffold official Laravel app first; do not hand-create framework internals.
2. Keep buyer flow accountless and token-based.
3. Keep admin flow session-authenticated with Laravel built-in `auth` middleware.
4. Use Form Requests for every mutation.
5. Use private `local` disk for proof images, ebook ZIP, and lead magnet downloads.
6. Keep business mutations in controllers/models, never in Blade templates.
7. Use Blade + plain CSS/Vite with UI tokens from `01-UI-SPEC.md`.
8. Use feature tests as the verification backbone; use fixture upload files instead of GD-generated fake images.

## Metadata

**Analog search scope:** workspace root excluding dependency/build/cache folders; checked `composer.json`, `artisan`, `app/**/*.php`, `routes/**/*.php`, `resources/**/*.blade.php`, `database/**/*.php`, `tests/**/*.php`.  
**Files scanned:** planning artifacts and docs only; 0 application source files found.  
**Project instructions:** no workspace `AGENTS.md`; no project `.claude/skills` or `.agents/skills` found. Global OpenCode rules apply.  
**Pattern extraction date:** 2026-06-13
