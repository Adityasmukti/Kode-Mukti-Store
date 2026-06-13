---
gsd_state_version: 1.0
milestone: v1.0
milestone_name: milestone
status: active
last_updated: "2026-06-13T07:10:00Z"
progress:
  total_phases: 1
  completed_phases: 0
  total_plans: 6
  completed_plans: 3
  percent: 50
---

# State

## Current Status

Project planning initialized for MVP landing page.

## Last Session

- Stopped at: Phase 01 Plan 03 complete (asset fixtures, layout, CSS tokens, test fixtures)
- Resume file: `.planning/phases/01-mvp-landing-page-dan-qris-manual/01-04-PLAN.md`
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
