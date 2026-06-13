---
phase: 01-mvp-landing-page-dan-qris-manual
plan: 06
subsystem: admin-auth, admin-verification, file-download
tags: [laravel-13, blade, auth, file-storage, tdd]
requires:
  - phase: 01-02
    provides: Order model, PaymentProof model, AdminUserSeeder, route contracts, Form Request shells, Controller shells
  - phase: 01-03
    provides: Base Blade layout, CSS design tokens, test fixture JPG
  - phase: 01-05
    provides: Order creation flow, proof upload/re-upload, all 4 order states (pending, proof_submitted, rejected, verified), PaymentProof records
provides:
  - AdminAuthController with session-based login/logout and session regeneration
  - AdminOrderController with sorted dashboard, proof preview stream, is_admin gate
  - OrderVerificationController with confirm and reject actions, audit fields, proof status tracking
  - DownloadController with verified-only ebook ZIP streaming
  - Admin login, orders index (desktop table + mobile cards), and proof preview Blade views
  - AdminVerificationTest (2 methods, 53 assertions) and DownloadAccessTest (7 tests, 13 assertions)
affects:
  - REQ-001 acceptance criteria 5 (admin verify) and 6 (verified download) are now complete
tech-stack:
  added: []
  patterns:
    - "Admin auth: Auth::attempt with session regeneration, isAdmin() gate on dashboard and actions"
    - "Admin order listing: ORDER BY CASE status WHEN 'proof_submitted' THEN 0 ... END for SQLite/MySQL compatible priority sorting"
    - "Protected proof stream: Storage::disk('local')->path() + response()->file() with ownership check"
    - "Protected download: abort_unless($order->status === 'verified', 403) before Storage::download()"
    - "Admin Blade views: desktop table + mobile stacked cards with details/summary for Reject form"
    - "Session invalidation on logout: $request->session()->invalidate() + regenerateToken()"
key-files:
  created:
    - tests/Feature/AdminVerificationTest.php
    - tests/Feature/DownloadAccessTest.php
    - resources/views/admin/auth/login.blade.php
    - resources/views/admin/orders/index.blade.php
    - resources/views/admin/orders/proof.blade.php
  modified:
    - app/Http/Controllers/Admin/AuthController.php
    - app/Http/Controllers/Admin/OrderController.php
    - app/Http/Controllers/Admin/OrderVerificationController.php
    - app/Http/Controllers/DownloadController.php
    - routes/web.php
    - resources/css/app.css
key-decisions:
  - "admin.logout route middleware changed from guest to auth (Rule 1 — was a bug preventing logout)"
  - "Admin HTML details/summary element used for Reject form reveal instead of JS toggle"
  - "CASE WHEN in ORDER BY for status priority sorting (compatible with both SQLite and MySQL)"
  - "Storage::fake('local') for download tests with fake ZIP content on private disk"
requirements-completed: [REQ-001]
duration: 15min
completed: 2026-06-13
---

# Phase 01 Plan 06: Admin Authentication, Manual Payment Verification, Proof Preview, and Protected Download

**Admin session login, sorted order dashboard with proof preview, confirm/reject payment actions with audit fields, protected ebook ZIP download for verified orders only — all with full TDD test coverage.**

## Performance

- **Duration:** 15 min
- **Started:** 2026-06-13T07:30:00Z
- **Completed:** 2026-06-13T07:45:00Z
- **Tasks:** 3 (all TDD, all autonomous)
- **Tests:** 54 total, 258 assertions passing (9 new tests, 66 new assertions)

## Accomplishments

- **Admin session auth** — `Admin\AuthController` implements `create` (login form view), `store` (Auth::attempt + session regeneration + intended redirect), and `destroy` (logout + session invalidation + CSRF token regeneration). Validation via `AdminLoginRequest` email/password rules.
- **Admin order dashboard** — `Admin\OrderController@index` lists all orders with status-priority sorting (`proof_submitted` → `pending` → `rejected` → `verified`), showing buyer name/email/WhatsApp/amount/status/upload time/proof preview link/confirm and reject actions. Desktop responsive table + mobile stacked cards. Empty state when no orders exist (UI-SPEC copy). Non-admin users receive 403.
- **Private proof preview** — `Admin\OrderController@proof` streams uploaded proof images from `Storage::disk('local')` behind `isAdmin` gate with ownership verification (proof must belong to order). Returns appropriate Content-Type header and 404 if proof doesn't belong to requested order.
- **Confirm/Reject payment actions** — `OrderVerificationController@confirm` sets `status=verified`, records `verified_at`/`verified_by`, clears `reject_reason`, marks latest proof `accepted`. `@reject` validates `reject_reason` via `RejectPaymentRequest`, sets `status=rejected`, stores reason, marks latest proof `rejected`. Both gated by `isAdmin` check with CSRF-protected POST routes. Reject uses HTML `<details>` element for form reveal — no JavaScript.
- **Protected permanent download** — `DownloadController@ebook` finds order by `invoice_token` (404 if missing), abort 403 unless `status=verified`, streams ZIP via `Storage::disk('local')->download()`. No expiry, no temporary URLs, no buyer login.
- **Logout route fix** — Changed `admin.logout` middleware from `guest` to `auth` (Rule 1 — authenticated users were previously unable to logout because `guest` middleware redirects them away).

## Task Commits

Each task followed TDD (RED → GREEN) pattern:

1. **Task 1: Admin login, dashboard, proof preview**
   - `5e864d6` (test) — RED: failing AdminVerificationTest::test_admin_login_and_dashboard (6 assertions fail on shell controllers)
   - `1b1615d` (feat) — GREEN: AuthController, OrderController, admin views, admin CSS, logout route fix

2. **Task 2: Confirm and reject payment actions**
   - `196a312` (test) — RED: expanded test_confirm_and_reject_actions with 22 assertions (fails on missing isAdmin gate)
   - `0eff66a` (feat) — GREEN: OrderVerificationController with confirm/reject logic

3. **Task 3: Protected permanent ebook download**
   - `2930ae5` (test) — RED: DownloadAccessTest with 7 tests (6 fail on shell controller)
   - `8dd29b8` (feat) — GREEN: DownloadController@ebook with status check and private disk stream

## Files Created/Modified

**Created (5 files):**
- `tests/Feature/AdminVerificationTest.php` — 2 test methods, 53 assertions covering login, dashboard, proof preview, confirm, reject, non-admin blocking, and logout
- `tests/Feature/DownloadAccessTest.php` — 7 tests, 13 assertions covering 403 for unverified states, 200 for verified, 404 for invalid token, repeated downloads, and storage path non-exposure
- `resources/views/admin/auth/login.blade.php` — Login form extending app layout, email/password fields with validation error display
- `resources/views/admin/orders/index.blade.php` — Dashboard with desktop table + mobile stacked cards, status badges, proof preview links, confirm button, reject with details/summary reveal form
- `resources/views/admin/orders/proof.blade.php` — Proof preview page with order metadata and image display, back-to-dashboard link

**Modified (6 files):**
- `app/Http/Controllers/Admin/AuthController.php` — Full implementation of create/store/destroy with session auth
- `app/Http/Controllers/Admin/OrderController.php` — index() with sorted listings and isAdmin gate; proof() with image stream and ownership check
- `app/Http/Controllers/Admin/OrderVerificationController.php` — confirm() and reject() with audit fields and proof status tracking
- `app/Http/Controllers/DownloadController.php` — ebook() with 403 for unverified, 404 for missing, verified-only download stream
- `routes/web.php` — Fixed logout middleware from guest to auth (Rule 1)
- `resources/css/app.css` — Added 180 lines of admin dashboard/login/proof styles

## Decisions Made

- **admin.logout middleware fix**: Changed from `guest` to `auth` (Rule 1 — authenticated users could not log out with `guest` middleware which redirects authenticated users away).
- **HTML details/summary for Reject form**: No JavaScript needed for the reveal/expand pattern. CSS-only `details` element with absolute-positioned form panel. Accessible via keyboard and touch.
- **CASE WHEN in ORDER BY for priority sorting**: `ORDER BY CASE WHEN status='proof_submitted' THEN 0 ...` works in both SQLite (tests) and MySQL (production).
- **Storage::fake('local') for download tests**: Fake disk creates a fake ZIP content, controllers stream via `Storage::disk('local')->download()`. The fake adapter correctly generates download responses.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 1 — Bug] Fixed admin.logout route middleware**
- **Found during:** Task 1 (admin auth implementation)
- **Issue:** The `admin.logout` POST route had `guest` middleware, which prevents authenticated users from accessing it (Laravel's `guest` middleware redirects authenticated users away). This meant no one could actually log out.
- **Fix:** Changed `->middleware('guest')` to `->middleware('auth')` so only authenticated users can POST to logout.
- **Files modified:** `routes/web.php`
- **Verification:** AdminVerificationTest asserts logout succeeds and session is invalidated.
- **Committed in:** `1b1615d` (Task 1 GREEN commit)

---

**Total deviations:** 1 auto-fixed (critical bug)
**Impact on plan:** Fix was necessary for correct operation. Without it, admin users could never log out.

## TDD Gate Compliance

| Task | RED Commit | GREEN Commit | Compliant |
|------|-----------|-------------|-----------|
| 1 — Admin login + dashboard | `5e864d6` test | `1b1615d` feat | ✅ |
| 2 — Confirm/reject actions | `196a312` test | `0eff66a` feat | ✅ |
| 3 — Protected download | `2930ae5` test | `8dd29b8` feat | ✅ |

## Threat Model Compliance

| Threat ID | Category | Component | Status | Verification |
|-----------|----------|-----------|--------|--------------|
| T-01-19 | Spoofing | admin login | mitigated | Auth::attempt with hashed passwords, session regeneration, validated AdminLoginRequest |
| T-01-20 | Elevation of Privilege | admin routes | mitigated | auth middleware + isAdmin() check on index, proof, confirm, reject |
| T-01-21 | Tampering | confirm/reject | mitigated | CSRF-protected POST routes, isAdmin gate, validated reject_reason via RejectPaymentRequest, exact state transitions |
| T-01-22 | Info Disclosure | paid ZIP download | mitigated | 403 for unverified orders; Storage::disk('local')->download() with no public URL; missing token returns 404 |
| T-01-23 | Info Disclosure | proof preview | mitigated | Proof streams from local private disk behind auth + isAdmin gate; ownership check (proof must belong to order) |
| T-01-24 | Repudiation | manual verification | mitigated | verified_at and verified_by stored on confirm; payment_proofs.status tracks accepted/rejected |

## REQ-001 Status Update

| Acceptance Criteria | Status | Plan |
|--------------------|--------|------|
| Admin dapat melihat order pending dan mengonfirmasi pembayaran secara manual | ✅ Complete | Plan 01-06 |
| Setelah pembayaran dikonfirmasi, halaman download ebook aktif untuk pembeli tersebut | ✅ Complete | Plan 01-06 |

---

**Total deviations:** 1 auto-fixed (Rule 1 - bug fix)
**Impact on plan:** All deviations were necessary corrections. No scope creep.

## Self-Check: PASSED

- [x] `php artisan test --filter=AdminVerificationTest::test_admin_login_and_dashboard` — 31 assertions passing
- [x] `php artisan test --filter=AdminVerificationTest::test_confirm_and_reject_actions` — 22 assertions passing
- [x] `php artisan test --filter=DownloadAccessTest` — 7 tests, 13 assertions passing
- [x] `php artisan test` — 54 tests, 258 assertions passing (all tests)
- [x] Guest visiting `/admin/orders` is redirected to `/admin/login`
- [x] Admin login with valid credentials creates authenticated session
- [x] Non-admin user receives 403 on admin dashboard
- [x] Dashboard orders sorted: proof_submitted → pending → rejected → verified
- [x] Proof preview streams image via authenticated route only
- [x] Confirm sets verified status, audit fields, marks proof accepted
- [x] Reject requires reason, stores it, marks proof rejected
- [x] Reject without reason returns validation error
- [x] Pending/proof_submitted/rejected orders get 403 on download
- [x] Verified orders get 200 with Content-Disposition header on download
- [x] Invalid token returns 404 on download
- [x] Repeated downloads work (no expiry)
- [x] Storage path not exposed in error responses
- [x] Logout invalidates session, redirects to login
- [x] 6 TDD gates present: 3 RED (test) → 3 GREEN (feat)

---

*Phase: 01-mvp-landing-page-dan-qris-manual*
*Completed: 2026-06-13*
