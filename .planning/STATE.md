---
gsd_state_version: 1.0
milestone: v1.0
milestone_name: milestone
status: active
last_updated: "2026-06-13T07:24:00Z"
progress:
  total_phases: 1
  completed_phases: 0
  total_plans: 6
  completed_plans: 5
  percent: 0
---

# State

## Current Status

Phase 01 Plan 05 complete — buyer order creation, QRIS invoice page, and proof upload/re-upload workflow implemented.

## Last Session

- Stopped at: Phase 01 Plan 05 complete (buyer order, QRIS invoice, proof upload)
- Resume file: `.planning/phases/01-mvp-landing-page-dan-qris-manual/01-06-PLAN.md`
- Updated: 2026-06-13

## Accumulated Context

### Roadmap Evolution

- Phase 1 added: MVP Landing Page dan QRIS Manual.

### Decisions

- Laravel is the application stack.
- Hosting target is own VPS through Cloudflared tunnel.
- Payment gateway is deferred; MVP uses QRIS manual verification.
- Lead magnet uses instant download after email capture.
- config/products.php is the single source of truth for all product pricing and asset paths.
- PROMO_DEADLINE reads from environment with safe non-expired fallback 2026-07-31.
- .env.example contains non-secret config names only — no secrets committed.
- Products config uses env() with sensible defaults, not hardcoded values.
- AdminUserSeeder uses getenv() directly for runtime env reading (not env() helper).
- Auth middleware redirects guests to admin.login and authenticated users to admin.orders.index.
- Public buyer routes use {invoice_token} or {download_token} per D-01, never numeric IDs.
- UploadPaymentProofRequest enforces mimes:jpg,jpeg,png,webp with max:4096 per D-08.
- RejectPaymentRequest enforces reject_reason required string max:500 per D-07.
- Plain CSS design tokens replace Tailwind import per UI-SPEC contract (no component frameworks).
- Python Pillow used instead of PHP GD for fixture/placeholder image creation.
- Private asset files force-added to git despite storage/.gitignore for development fixture reproducibility.
- Named rate limiters defined in AppServiceProvider@boot for orders, uploads, leads, login (10-5 per minute per IP).
- Lead token uses 48-char Str::random high-entropy token, uniqueness verified before save.
- Original price Rp 499.000 hardcoded in LandingPageController for strikethrough anchoring effect.
- Vite manifest stub in public/build/ for test environment (no npm build required).
- Fixture-based image upload tests: sample-proof.jpg copied to temp files with correct MIME types (no PHP GD required).
- Str::random(32) for invoice_token: high-entropy, URL-safe, non-numeric.
- Proof files stored on local (private) disk; never public or symlinked.
- PaymentProof records created via hasMany relationship for history preservation (T-01-18).
- Blade view renders all 4 states in single template with @if/@elseif branches.
