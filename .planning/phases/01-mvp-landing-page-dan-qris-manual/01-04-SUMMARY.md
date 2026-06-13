---
phase: 01-mvp-landing-page-dan-qris-manual
plan: 04
subsystem: landing-page, lead-magnet, testing
tags: [laravel-13, blade, pas-copy, pricing, countdown, email-capture, tdd]
requires:
  - phase: 01-01
    provides: product config contract (price, paths)
  - phase: 01-02
    provides: route contracts, controller shells, StoreLeadRequest, Lead model, Lead migration
  - phase: 01-03
    provides: Blade layout, CSS design tokens, lead magnet fixture file
provides:
  - PAS landing page at / with hero, problem/agitation, solution, preview, pricing, countdown, lead magnet, FAQ
  - Email-only lead capture with upsert and tokenized download redirect
  - Lead magnet download page with soft CTA upsell
  - Private lead magnet file streaming via Storage::local
affects:
  - 01-05 (checkout forms on landing page post to orders.store)
  - 01-06 (admin view uses same layout)
tech-stack:
  added: []
  patterns:
    - "Invokable controllers pass config values to views"
    - "Lead upsert: updateOrCreate with unique email key, separate first_opted_at / last_opted_at"
    - "High-entropy 48-char random download token for public URLs"
    - "Named rate limiters defined via RateLimiter::for() in AppServiceProvider"
    - "Vite manifest stub for test environment (no npm build needed)"
key-files:
  created:
    - resources/views/landing.blade.php
    - resources/views/lead-magnet/show.blade.php
    - tests/Feature/LandingPageTest.php
    - tests/Feature/LeadMagnetTest.php
    - database/factories/LeadFactory.php
    - public/build/manifest.json
    - public/build/assets/app.css
  modified:
    - app/Http/Controllers/LandingPageController.php
    - app/Http/Controllers/LeadMagnetController.php
    - app/Models/Lead.php (added HasFactory trait)
    - resources/views/layouts/app.blade.php (added @stack directives)
    - app/Providers/AppServiceProvider.php (added named rate limiters)
key_decisions:
  - "Named rate limiters (orders, uploads, leads, login) defined in AppServiceProvider@boot — pre-existing gap from Plan 01-02"
  - "Lead token generation uses do/while loop with Str::random(48) for high entropy per T-01-09"
  - "Landing page uses PAS copy in exact UI-SPEC order, no fake testimonials or scarcity claims"
  - "Vite manifest stub created for test environment (public/build/) to avoid npm build requirement"
  - "Original price Rp 499.000 hardcoded in controller for strikethrough anchoring effect"
duration: 12min
completed: 2026-06-13
---

# Phase 01 Plan 04: Public Landing Page and Lead Magnet Instant Download Flow

**PAS landing page with pricing anchor, configurable countdown, email-only lead capture with upsert, tokenized download page, and 19 feature tests (40 assertions) — all TDD with RED/GREEN commits.**

## Performance

- **Duration:** 12 min
- **Tasks:** 3 (all TDD, all autonomous)
- **Tests:** 19 tests, 40 assertions (LandingPageTest + LeadMagnetTest)
- **Files created:** 7
- **Files modified:** 4

## Task Results

### Task 1: Build landing page with PAS copy, pricing, countdown, checkout and lead CTAs ✓

- **RED commit:** `8eac7d2` — LandingPageTest with 9 tests (content, CTA, forms, no fake content, countdown)
- **GREEN commit:** `9e3e29b` — LandingPageController + full landing.blade.php
- **Key implementation:**
  - `LandingPageController::__invoke` passes `price` (Rp 99.000), `promoDeadline` (configurable), `originalPrice` (Rp 499.000 strikethrough)
  - Landing page follows UI-SPEC section order: hero with PAS headline, problem/agitation, solution/benefits, product preview, pricing anchor card, countdown promo, lead magnet block, FAQ
  - Checkout form posts to `orders.store` with name, email, whatsapp
  - Lead form posts to `lead-magnet.store` with email only
  - Client-side countdown timer using `setInterval`, fallback text explaining promo deadline
  - Allowed trust badges only; no fake testimonials, no fake scarcity claims, no buyer account requirement
  - Updated layout with `@stack('styles')` and `@stack('scripts')` for page-specific assets

### Task 2: Implement email-only lead capture with duplicate upsert and token redirect ✓

- **RED commit:** `ba820ce` — store/duplicate/validation tests
- **GREEN commit:** `e0a78c7` — LeadMagnetController@store implementation
- **Key implementation:**
  - `updateOrCreate` by email: creates if new, updates `last_opted_at` if duplicate
  - High-entropy 48-char `Str::random()` token, uniqueness verified before save
  - `first_opted_at` set on creation, `last_opted_at` updated on every opt-in (including duplicates)
  - StoreLeadRequest already validated email-only (from Plan 01-02)
  - **Deviation Rule 2:** Added missing named rate limiters (`orders`, `uploads`, `leads`, `login`) in `AppServiceProvider` — these were referenced in routes but never defined (pre-existing gap from Plan 01-02)

### Task 3: Implement lead magnet download page and private file stream ✓

- **RED commit:** `7a92f65` — added download page/file tests, LeadFactory, HasFactory trait
- **GREEN commit:** `6d6052d` — lead-magnet/show.blade.php
- **Key implementation:**
  - `LeadMagnetController@downloadPage` — looks up lead by token, renders download page
  - `LeadMagnetController@download` — streams private file via `Storage::disk('local')->download()` after token lookup
  - Invalid tokens return 404 via `firstOrFail()` — never reveals storage paths
  - Download page matches UI-SPEC: heading "Download 50 Prompt Pemasaran Gratis", primary button "Download Lead Magnet", soft CTA card "Butuh sistem prompt yang lebih lengkap?" with button "Lihat Ebook Rp 99.000"
  - `LeadFactory` created for test data generation, `HasFactory` trait added to Lead model

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 2 - Missing Infrastructure] Added named rate limiters in AppServiceProvider**
- **Found during:** Task 2
- **Issue:** Routes `lead-magnet.store`, `orders.store`, `orders.proof.store`, and `admin.login` use `throttle:leads`, `throttle:orders`, `throttle:uploads`, `throttle:login` middleware, but none of these named rate limiters were defined. Calling any POST route would throw "Rate limiter [name] is not defined."
- **Fix:** Added `RateLimiter::for('orders')`, `RateLimiter::for('uploads')`, `RateLimiter::for('leads')`, and `RateLimiter::for('login')` in `AppServiceProvider@boot` with per-minute IP-based limits.
- **Files modified:** `app/Providers/AppServiceProvider.php`
- **Commit:** `e0a78c7`

**2. [Rule 2 - Missing Functionality] Vite manifest not found in test environment**
- **Found during:** Task 1
- **Issue:** The Blade layout uses `@vite('resources/css/app.css')`, but no Vite manifest exists in the test environment (npm build never run). All view-rendering tests would crash with `ViteManifestNotFoundException`.
- **Fix:** Created minimal `public/build/manifest.json` and copied `app.css` to `public/build/assets/app.css` for test environment.
- **Files modified:** `public/build/manifest.json`, `public/build/assets/app.css`
- **Commit:** `9e3e29b`

**3. [Rule 2 - Missing Functionality] Missing HasFactory trait on Lead model**
- **Found during:** Task 3
- **Issue:** Tests tried to call `Lead::factory()->create()` but the model lacked the `HasFactory` trait.
- **Fix:** Added `use HasFactory;` to Lead model and created `LeadFactory`.
- **Files modified:** `app/Models/Lead.php`, `database/factories/LeadFactory.php`
- **Commit:** `7a92f65`

## Threat Model Compliance

| Threat ID | Category | Component | Status | Verification |
|-----------|----------|-----------|--------|--------------|
| T-01-09 | Spoofing | lead download_token | mitigated | 48-char random token via Str::random(); uniqueness checked; invalid token returns 404 |
| T-01-10 | Info Disclosure | lead magnet download | mitigated | Streamed via Storage::local after token lookup; no public Storage::url; no path exposure |
| T-01-11 | Tampering | lead capture form | mitigated | StoreLeadRequest validates email only; named rate limiter (10/min per IP) |
| T-01-12 | Repudiation | duplicate lead opt-ins | mitigated | first_opted_at preserved on create; last_opted_at updated on every duplicate submission |
| T-01-13 | Info Disclosure | landing page copy | mitigated | No fake testimonials, logos, or customer claims; only approved trust badges |

## Commit Log

| Hash | Type | Message |
|------|------|---------|
| `8eac7d2` | test | add failing test for landing page content |
| `9e3e29b` | feat | implement PAS landing page with pricing, countdown, and lead magnet CTA |
| `ba820ce` | test | add failing test for email-only lead magnet capture |
| `e0a78c7` | feat | implement email-only lead capture with upsert and token redirect |
| `7a92f65` | test | add download page and file stream tests for lead magnet |
| `6d6052d` | feat | implement lead magnet download page and private file stream |

## TDD Gate Compliance

- [x] Task 1: RED commit `8eac7d2` exists (test: landing page content) → GREEN commit `9e3e29b` exists (feat: PAS landing page)
- [x] Task 2: RED commit `ba820ce` exists (test: lead magnet capture) → GREEN commit `e0a78c7` exists (feat: upsert and redirect)
- [x] Task 3: RED commit `7a92f65` exists (test: download page/file stream) → GREEN commit `6d6052d` exists (feat: download page)

All six RED→GREEN gate sequences are satisfied.

## Self-Check: PASSED

- [x] `php artisan route:list` — landing, lead-magnet.store, lead-magnet.show, lead-magnet.download all registered
- [x] `LandingPageTest` — 9 tests, 21 assertions: PASS
- [x] `LeadMagnetTest` — 10 tests, 19 assertions: PASS
- [x] Combined suite — 19 tests, 40 assertions: PASS
- [x] Lead upsert: duplicate email creates 1 row, updates last_opted_at, preserves download_token
- [x] Private file download: streams from Storage::local after token lookup
- [x] Invalid download token returns 404 without revealing storage paths
- [x] Landing page has no fake testimonials or fake scarcity claims
- [x] LEAD_MAGNET_PATH file exists on private disk: `storage/app/private/lead-magnets/50-prompt-pemasaran-gratis.txt`
- [x] 6 atomic commits with proper TDD RED/GREEN format

## Success Criteria Verification

- [x] **REQ-001 landing page:** Landing page is live at / with PAS copy, "Dapatkan Sekarang" CTA, Rp 99.000 price near CTA, strikethrough Rp 499.000 anchoring, checkout form with name/email/whatsapp posting to orders.store, lead magnet form posting to lead-magnet.store, configurable countdown, FAQ, no fake testimonials/scarcity/buyer account
- [x] **REQ-002 lead magnet:** Email-only form stores leads, duplicate emails update last_opted_at and still redirect to valid download page, instant download via token URL, no email marketing integration or CSV export
- [x] **D-10 through D-18:** All implemented without deferred features leaking in

---

*Phase: 01-mvp-landing-page-dan-qris-manual*
*Plan: 04*
*Completed: 2026-06-13*
