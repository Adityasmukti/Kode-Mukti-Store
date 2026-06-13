---
phase: 01-mvp-landing-page-dan-qris-manual
plan: 03
subsystem: ui, assets, testing
tags: [laravel-13, blade, css, design-tokens, fixtures, private-storage]
requires:
  - phase: 01-01
    provides: Laravel scaffold, products config contract
  - phase: 01-02
    provides: ScaffoldSmokeTest base, route contracts, file structure
provides:
  - Private product ZIP bundle (46MB) under storage/app/private/products/
  - Private lead magnet text file (50 prompts) under storage/app/private/lead-magnets/
  - Development QRIS placeholder PNG at public/images/qris.png
  - Base Blade layout with viewport, CSRF, title, flash messages
  - Plain CSS design token system (447 lines) matching 01-UI-SPEC.md
  - Test fixture JPG for upload tests without PHP GD
  - 7 ScaffoldSmokeTest tests, 88 assertions validating UI tokens and private assets
affects:
  - 01-04 (landing/lead views will extend app layout)
  - 01-05 (buyer order views will extend app layout)
  - 01-06 (admin views will extend app layout)
tech-stack:
  added: []
  patterns:
    - "Blade layouts with @yield('content'), viewport meta, CSRF, title section"
    - "Plain CSS design tokens via :root variables (no Tailwind, no PostCSS)"
    - "Status badge classes: .badge-pending, .badge-proof-submitted, .badge-verified, .badge-rejected"
    - "44px minimum control height for touch accessibility"
    - "Private asset fixtures stored under tests/Fixtures/ for upload tests"
    - "Zip CLI for building product bundle from docs/ content"
    - "Python Pillow for creating placeholder images and test fixtures"
key-files:
  created:
    - storage/app/private/products/Ultimate-ChatGPT-Mastery-Bundle.zip
    - storage/app/private/lead-magnets/50-prompt-pemasaran-gratis.txt
    - public/images/qris.png
    - resources/views/layouts/app.blade.php
    - tests/Fixtures/proofs/sample-proof.jpg
  modified:
    - resources/css/app.css (replaced Tailwind import with plain CSS design tokens)
    - tests/Feature/ScaffoldSmokeTest.php (added private assets and UI tokens test)
key_decisions:
  - "Plain CSS over Tailwind: Tailwind removed from app.css per UI-SPEC contract which requires no component frameworks"
  - "Python Pillow used instead of PHP GD for fixture/placeholder image creation since PHP GD is unavailable"
  - "Private asset files force-added to git despite storage/app/private/.gitignore pattern for development fixture reproducibility"
  - "Layout assertions check file content directly (not rendered response) since landing page doesn't extend layout yet"
duration: 10min
completed: 2026-06-13
---

# Phase 01 Plan 03: Private Asset Fixtures, Base UI Layout, CSS Design Tokens, and Test Fixtures

**Product ZIP bundle, lead magnet file, QRIS placeholder, Blade layout, plain CSS token system, test fixture JPG, and updated ScaffoldSmokeTest — all autonomous tasks completed with 7 tests/88 assertions passing.**

## Overview

Plan 03 created all the shared asset infrastructure and UI shell that downstream plans (01-04, 01-05, 01-06) consume for rendering, downloading, and testing. Three autonomous tasks built: (1) private downloadable assets (product ZIP from docs, lead magnet with 50 prompts, QRIS placeholder), (2) base Blade layout with plain CSS design token system matching 01-UI-SPEC.md, and (3) test fixture JPG with updated ScaffoldSmokeTest coverage.

## Task Results

### Task 1: Create private product ZIP, lead magnet, and QRIS placeholder assets ✓

- **Commit:** `6e83856`
- **Files:**
  - `storage/app/private/products/Ultimate-ChatGPT-Mastery-Bundle.zip` — 46MB ZIP bundling all docs/ content (3x .md, 2x .pdf, 3x .xlsx)
  - `storage/app/private/lead-magnets/50-prompt-pemasaran-gratis.txt` — 50 marketing prompt entries (one per line, derived from docs content)
  - `public/images/qris.png` — 300x300 orange placeholder with "QRIS DEVELOPMENT" text, created via Python Pillow
- **Approach:** `zip -j` CLI for product bundle, manual extraction of marketing prompts from docs, Python Pillow for QRIS image
- **Verification:** All three files exist; none under `storage/app/public/` or `public/storage/`

### Task 2: Create base Blade layout and CSS design token system ✓

- **Commit:** `ec41f79`
- **Files:**
  - `resources/views/layouts/app.blade.php` — 43 lines with viewport meta, CSRF token, `@yield('title')` with fallback, `@yield('content')`, flash messages for success/error/validation errors, `@vite('resources/css/app.css')` link
  - `resources/css/app.css` — 447 lines replacing Tailwind with plain CSS design tokens
- **CSS features:**
  - Color tokens: background `#FFF7ED`, card `#FFFFFF`, accent `#F97316`, destructive `#DC2626`
  - Status colors: pending `#D97706`, verified `#16A34A`
  - Typography: system UI font stack, body 16px/400/1.5, label 14px/600/1.4, heading 24px/600/1.2, display 40px/600/1.1
  - Controls: minimum 44px height
  - Focus ring: `#F97316` 2px solid outline
  - Status badges: `.badge-pending`, `.badge-proof-submitted`, `.badge-verified`, `.badge-rejected`
  - Validation error styles: `.error`, `.error-text`, `.error-list` with `#DC2626`
  - Form elements, buttons (primary/secondary/destructive), alerts, cards, responsive helpers
- **Accepted criteria:** All UI-SPEC tokens present, no Tailwind/postCSS/icon packages added, no npm packages installed

### Task 3: Create test fixture file and update scaffold smoke test ✓

- **Commit:** `a05e234`
- **Files:**
  - `tests/Fixtures/proofs/sample-proof.jpg` — 631 bytes, valid 10x10 JPG created via Python Pillow, no PHP GD required
  - `tests/Feature/ScaffoldSmokeTest.php` — added `test_private_assets_and_ui_tokens()` with 16 assertions
- **New test coverage:**
  - `test_private_assets_and_ui_tokens` (16 assertions):
    1. Product ZIP exists on private disk
    2. Lead magnet file exists on private disk
    3. QRIS image exists in public/images
    4. Product ZIP NOT under public/storage
    5. Lead magnet NOT under public/storage
    6. Layout file contains viewport meta tag
    7. Layout contains CSRF meta tag
    8. Layout contains `<title>` region
    9. Layout contains `@yield('content')`
    10-15. CSS contains all 6 UI tokens (#FFF7ED, #FFFFFF, #F97316, #DC2626, 44px, ui-sans-serif)

## Deviations from Plan

None — plan executed exactly as written.

## Known Stubs

- `public/images/qris.png` — This is a development placeholder. Must be replaced with actual merchant QRIS image before deployment (documented in plan `user_setup` frontmatter).
- `resources/views/landing.blade.php` — Currently standalone, does not extend app layout. Will be updated in Plan 01-04.
- All controller methods remain as empty shells from Plan 01-02 — business logic will be added in plans 01-04, 01-05, and 01-06.

## Asset Path Diagram

```
storage/app/private/products/Ultimate-ChatGPT-Mastery-Bundle.zip
  → config('products.ebook_zip_path')
  → Used by: DownloadController (plan 01-05)

storage/app/private/lead-magnets/50-prompt-pemasaran-gratis.txt
  → config('products.lead_magnet_path')
  → Used by: LeadMagnetController (plan 01-04)

public/images/qris.png
  → config('products.qris_image_path')
  → Used by: OrderPortalController blade view (plan 01-05)

resources/views/layouts/app.blade.php
  → @extends('layouts.app')
  → Used by: all downstream plan views (01-04, 01-05, 01-06)

resources/css/app.css
  → @vite('resources/css/app.css')
  → Used by: all views extending app layout

tests/Fixtures/proofs/sample-proof.jpg
  → UploadPaymentProofRequest tests (plan 01-05)
```

## Threat Model Compliance

| Threat ID | Category | Component | Status | Verification |
|-----------|----------|-----------|--------|--------------|
| T-01-06 | Tampering | ZIP bundle creation | mitigated | Bundle created only from existing docs assets; no executable content included |
| T-01-07 | Information Disclosure | private file paths | mitigated | ZIP and lead magnet stored on local private disk only; never ran storage:link |
| T-01-08 | Tampering | fixture upload file | mitigated | Fixture is a valid 631-byte JPG for testing only; not used in production |

## Commit Log

| Hash | Type | Message |
|------|------|---------|
| `6e83856` | feat | create private product ZIP, lead magnet, and QRIS placeholder assets |
| `ec41f79` | feat | create base Blade layout and CSS design token system |
| `a05e234` | feat | create test fixture JPG and update ScaffoldSmokeTest |

## Self-Check: PASSED

- [x] `storage/app/private/products/Ultimate-ChatGPT-Mastery-Bundle.zip` exists
- [x] `storage/app/private/lead-magnets/50-prompt-pemasaran-gratis.txt` exists with 50 lines
- [x] `public/images/qris.png` exists (7.1KB valid PNG)
- [x] `resources/views/layouts/app.blade.php` exists with viewport, CSRF, title, @yield, flash messages
- [x] `resources/css/app.css` has all UI tokens (#FFF7ED, #FFFFFF, #F97316, #DC2626, 44px, system font)
- [x] `tests/Fixtures/proofs/sample-proof.jpg` exists (631 bytes, valid JPG)
- [x] All 7 ScaffoldSmokeTest tests pass (88 assertions)
- [x] No assets under `storage/app/public/` or `public/storage/`
- [x] No Tailwind, no icon packages, no npm packages added
- [x] 3 atomic commits with formatted messages

---

*Phase: 01-mvp-landing-page-dan-qris-manual*
*Completed: 2026-06-13*
