---
phase: 01-mvp-landing-page-dan-qris-manual
plan: 05
subsystem: buyer-order, qris, upload
tags: [laravel-13, blade, storage, file-upload, tdd]
requires:
  - phase: 01-01
    provides: Laravel scaffold, products config (price, qris_image_path)
  - phase: 01-02
    provides: Order model, PaymentProof model, route contracts, Form Request shells
  - phase: 01-03
    provides: Base Blade layout, CSS design tokens, test fixture JPG, QRIS placeholder
provides:
  - OrderController@store with high-entropy invoice_token and redirect
  - OrderPortalController@show with token-based lookup and invoice Blade view
  - PaymentProofController@store with private file storage and status tracking
  - Invoice Blade rendering all 4 order states: pending, proof_submitted, rejected, verified
  - 2 feature test files: OrderFlowTest (10 tests), PaymentProofUploadTest (7 tests)
affects:
  - 01-06 (admin verify/reject will consume order states and proof records)
tech-stack:
  added: []
  patterns:
    - "Order creation with Str::random invoice_token, redirect to tokenized URL"
    - "Token lookup with firstOrFail to avoid numeric ID exposure"
    - "Private file storage on local disk under payment-proofs/{invoice_token}"
    - "PaymentProof records preserve history rows rather than overwriting metadata"
    - "Invoice Blade view uses @extends('layouts.app') and CSS token classes"
    - "File upload tests use fixture JPG from tests/Fixtures/proofs/ (no PHP GD)"
key-files:
  created:
    - tests/Feature/OrderFlowTest.php
    - tests/Feature/PaymentProofUploadTest.php
    - resources/views/orders/show.blade.php
  modified:
    - app/Http/Controllers/OrderController.php
    - app/Http/Controllers/OrderPortalController.php
    - app/Http/Controllers/PaymentProofController.php
    - resources/css/app.css
key-decisions:
  - "UploadedFile::fake()->image() requires GD extension; fixture file used instead (copy from tests/Fixtures/proofs/)"
  - "invoice_token uses Str::random(32) — high entropy, non-numeric, URL-safe"
  - "Proof files stored on local (private) disk; never public or symlinked"
  - "PaymentProof records created via hasMany relationship for history preservation"
  - "Re-upload after rejection creates new payment_proofs row, clears reject_reason"
  - "Blade view renders all 4 states with shared layout; state-specific copy from UI-SPEC"
requirements-completed: [REQ-001]
duration: 14min
completed: 2026-06-13
---

# Phase 01 Plan 05: Buyer Order Creation, QRIS Invoice Page, and Proof Upload Workflow

**Order creation with token-based invoice URL, QRIS pending page with all 4 buyer states, private proof upload/re-upload, and full feature tests — all without buyer accounts or payment gateway automation.**

## Performance

- **Duration:** 14 min
- **Started:** 2026-06-13T07:10:00Z
- **Completed:** 2026-06-13T07:24:00Z
- **Tasks:** 3 (all TDD, all autonomous)
- **Tests:** 17 tests, 62 assertions passing (OrderFlowTest + PaymentProofUploadTest)

## Accomplishments

- **Order creation** — `OrderController@store` accepts `StoreOrderRequest` (name, email, WA), generates 32-char `Str::random` invoice token, creates pending order at Rp 99.000, and redirects to tokenized URL. No buyer login required.
- **Tokenized invoice page** — `OrderPortalController@show` renders `orders/show.blade.php` with all 4 states from UI-SPEC: pending (QRIS, steps, upload form), proof_submitted (badge, admin message, secondary re-upload), rejected (destructive alert with reason, primary re-upload), verified (download ZIP button + support note).
- **Private proof upload** — `PaymentProofController@store` validates file via `UploadPaymentProofRequest` (JPG/PNG/WebP, max 4MB), stores on local private disk under `payment-proofs/{invoice_token}` with generated hashed names, creates payment_proofs history row, and transitions order to `proof_submitted`.
- **Re-upload support** — Rejected orders can re-upload proof; creates new payment_proofs row (preserving history per T-01-18), clears reject_reason, sets status to `proof_submitted`.
- **CSS invoice layout** — 80 lines of CSS for invoice card, QRIS image container (280px mobile / 360px desktop), payment steps, upload sections, and state-specific styling.

## Task Commits

Each task followed TDD (RED → GREEN) pattern:

1. **Task 1: Order creation and pending invoice state**
   - `096a73e` (test) — RED: failing OrderFlowTest (5 tests, 4 fail as expected)
   - `37c4c1f` (feat) — GREEN: OrderController, OrderPortalController, Blade view, invoice CSS

2. **Task 2: Private proof upload and re-upload workflow**
   - `fbd9a55` (test) — RED: failing PaymentProofUploadTest (7 tests, 3 fail as expected)
   - `635247a` (feat) — GREEN: PaymentProofController with private storage and status updates

3. **Task 3: Complete invoice states**
   - `525268d` (test) — Added state-specific tests for proof_submitted, rejected, verified (all pass — view already renders all states from Task 1)

## Files Created/Modified

**Created (3 files):**
- `tests/Feature/OrderFlowTest.php` — 10 tests, 42 assertions covering order creation, validation, pending state, all 4 invoice states, and token lookup
- `tests/Feature/PaymentProofUploadTest.php` — 7 tests, 20 assertions covering valid upload, file type/size rejection, unknown token 404, re-upload after rejection, and private storage verification
- `resources/views/orders/show.blade.php` — 120-line Blade view extending app layout, rendering all 4 order states with QRIS, payment steps, upload form, state badges, alerts, and download button

**Modified (4 files):**
- `app/Http/Controllers/OrderController.php` — Implemented store() with order creation and redirect
- `app/Http/Controllers/OrderPortalController.php` — Implemented show() with token lookup and view
- `app/Http/Controllers/PaymentProofController.php` — Implemented store() with private file storage and status transition
- `resources/css/app.css` — Added 80 lines of invoice/QRIS page styling

## Decisions Made

- **Fixture-based image upload tests**: `UploadedFile::fake()->image()` requires PHP GD extension, which is unavailable. Tests copy the existing `sample-proof.jpg` fixture to temp files with correct MIME types instead.
- **Str::random(32) for invoice_token**: High-entropy, alphanumeric, URL-safe. Matches D-01 (token-based URLs, no numeric IDs).
- **Local private disk for proofs**: `Storage::disk('local')` stores under `storage/app/private/` — never symlinked to `public/`. Matches T-01-16.
- **PaymentProof history rows**: `$order->paymentProofs()->create()` creates new records on each upload. On re-upload, old records are preserved with their timestamps (T-01-18 mitigation).
- **All states in single Blade view**: Single `orders/show.blade.php` uses `@if`/`@elseif` branches for all 4 states, avoiding template duplication.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 2 — Missing Critical] Added invoice CSS to app.css**
- **Found during:** Task 1 (order creation + pending state)
- **Issue:** Blade view uses custom CSS classes (`.invoice-page`, `.qris-image`, `.payment-steps`, etc.) but no corresponding CSS was in app.css
- **Fix:** Added 80 lines of invoice/QRIS page styles: card layout, QRIS image sizing, payment steps, upload section, verified section, support note
- **Files modified:** `resources/css/app.css`
- **Verification:** OrderFlowTest all 5 tests pass (view renders correctly)
- **Committed in:** `37c4c1f` (Task 1 GREEN commit)

**2. [Rule 3 — Blocking] Test fixture files require PHP GD but GD is not installed**
- **Found during:** Task 2 (PaymentProofUploadTest)
- **Issue:** `UploadedFile::fake()->image()` requires `ext-gd` which is not available. Tests fail with "GD extension is not installed."
- **Fix:** Replaced all `fake()->image()` calls with fixture-based approach: copy `tests/Fixtures/proofs/sample-proof.jpg` to temp file with correct MIME type
- **Files modified:** `tests/Feature/PaymentProofUploadTest.php`
- **Verification:** All 7 PaymentProofUploadTest tests pass (no PHP GD required)
- **Committed in:** `fbd9a55` (Task 2 RED commit — test was rewritten before GREEN)

---

**Total deviations:** 2 auto-fixed (1 missing critical, 1 blocking)
**Impact on plan:** Both auto-fixes were necessary for correct operation. No scope creep.

## Issues Encountered

- **PHP GD extension unavailable**: Same as Plan 01-02 and 01-03. Resolved by using fixture file approach instead of `UploadedFile::fake()->image()`. The fixture JPG from Plan 01-03 (created via Python Pillow) is copied to temp files for upload tests.

## TDD Gate Compliance

| Task | RED Commit | GREEN Commit | Compliant |
|------|-----------|-------------|-----------|
| 1 — Order creation | `096a73e` test | `37c4c1f` feat | ✅ |
| 2 — Proof upload | `fbd9a55` test | `635247a` feat | ✅ |
| 3 — Invoice states | `525268d` test | (view built in Task 1) | ✅ (tests validate existing impl) |

## Threat Model Compliance

| Threat ID | Category | Component | Status | Verification |
|-----------|----------|-----------|--------|--------------|
| T-01-14 | Spoofing | invoice_token | mitigated | Str::random(32) high-entropy token; lookup by token only; never expose order ID |
| T-01-15 | Tampering | proof upload | mitigated | FormRequest image/mime/size validation; local private disk; generated filenames |
| T-01-16 | Info Disclosure | payment proofs | mitigated | Stored on local (private) disk; no public symlink; no public URL exposure |
| T-01-17 | Denial of Service | order/proof routes | mitigated | throttle:orders and throttle:uploads middleware on POST routes (from Plan 01-02) |
| T-01-18 | Repudiation | proof history | mitigated | PaymentProof records created via hasMany; each upload is a new row with timestamps |

## Known Stubs

- `orders.download` route exists but `DownloadController@ebook` is an empty shell — will be implemented in Plan 01-06. The verified state Blade view links to it and the button renders correctly.
- `public/images/qris.png` is a development placeholder (from Plan 01-03). Must be replaced with actual merchant QRIS image before deployment.

## Next Phase Readiness

- **Plan 01-06 ready**: Admin verify/reject workflow can consume `Order` statuses (`pending`, `proof_submitted`, `verified`, `rejected`), `PaymentProof` records, and `reject_reason` field. The `admin.orders.index`, `admin.orders.confirm`, `admin.orders.reject`, `admin.orders.proof` routes, `AdminLoginRequest`, `RejectPaymentRequest`, and `Admin/AuthController` shells are all in place from Plan 01-02.
- **Download controller**: `DownloadController@ebook` needs implementation for the verified state download button to work.

## Self-Check: PASSED

- [x] `php artisan test --filter=OrderFlowTest` — 10 tests, 42 assertions passing
- [x] `php artisan test --filter=PaymentProofUploadTest` — 7 tests, 20 assertions passing
- [x] `php artisan test tests/Feature/ScaffoldSmokeTest.php` — 7 tests, 88 assertions passing (regression)
- [x] Order creation redirects to `/orders/{invoice_token}`, numeric `/orders/1` returns 404
- [x] Pending page shows QRIS, steps, upload form, "Menunggu Pembayaran" badge
- [x] Proof upload stores file on local private disk, creates payment_proofs record
- [x] Invalid file types rejected with validation errors
- [x] Re-upload after rejection works, clears reject_reason
- [x] Verified state shows download button linking to `orders.download` route
- [x] 4 TDD gate commits present: 2 RED (test) → 2 GREEN (feat)

---

*Phase: 01-mvp-landing-page-dan-qris-manual*
*Completed: 2026-06-13*
