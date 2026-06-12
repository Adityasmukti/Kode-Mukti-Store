---
phase: 01
slug: mvp-landing-page-dan-qris-manual
status: draft
nyquist_compliant: false
wave_0_complete: false
created: 2026-06-13
---

# Phase 01 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | PHPUnit via Laravel HTTP feature tests |
| **Config file** | `phpunit.xml` after Laravel scaffold |
| **Quick run command** | `php artisan test --filter=OrderFlowTest` |
| **Full suite command** | `php artisan test` |
| **Estimated runtime** | ~30-90 seconds after scaffold |

---

## Sampling Rate

- **After every task commit:** Run targeted `php artisan test --filter=...` for changed behavior.
- **After every plan wave:** Run `php artisan test`.
- **Before `/gsd:verify-work`:** Full suite must be green plus manual browser smoke for landing → order → upload proof → admin confirm → download.
- **Max feedback latency:** 90 seconds.

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Threat Ref | Secure Behavior | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|------------|-----------------|-----------|-------------------|-------------|--------|
| 01-01-01 | 01 | 1 | REQ-001 | T-01-01 | App scaffold boots and landing page route is reachable | feature | `php artisan test --filter=LandingPageTest` | ❌ W0 | ⬜ pending |
| 01-01-02 | 01 | 1 | REQ-001 | T-01-02 | Order creates unique non-guessable token and shows QRIS pending state | feature | `php artisan test --filter=OrderFlowTest` | ❌ W0 | ⬜ pending |
| 01-01-03 | 01 | 1 | REQ-001 | T-01-03 | Proof upload accepts JPG/PNG/WebP and rejects invalid file types | feature | `php artisan test --filter=PaymentProofUploadTest` | ❌ W0 | ⬜ pending |
| 01-01-04 | 01 | 2 | REQ-001 | T-01-04 | Admin routes require auth and confirm/reject changes buyer status safely | feature | `php artisan test --filter=AdminVerificationTest` | ❌ W0 | ⬜ pending |
| 01-01-05 | 01 | 2 | REQ-001 | T-01-05 | Verified buyer can download private ZIP; unverified buyer cannot | feature | `php artisan test --filter=DownloadAccessTest` | ❌ W0 | ⬜ pending |
| 01-01-06 | 01 | 2 | REQ-002 | T-01-06 | Lead email is upserted and redirects to instant lead magnet download page | feature | `php artisan test --filter=LeadMagnetTest` | ❌ W0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] Laravel scaffold exists with `composer.json`, `artisan`, `app/`, `routes/`, `resources/`, `database/`, and `tests/`.
- [ ] `phpunit.xml` exists from Laravel scaffold.
- [ ] Feature test stubs exist for `LandingPageTest`, `OrderFlowTest`, `PaymentProofUploadTest`, `AdminVerificationTest`, `DownloadAccessTest`, and `LeadMagnetTest`.
- [ ] Small fixture images exist for upload tests because PHP GD may be absent.
- [ ] Private placeholder files exist for ebook ZIP bundle and lead magnet download.

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Landing page visual quality on mobile/desktop | REQ-001, REQ-002 | Visual hierarchy and responsive polish need browser review | Open landing page in desktop and mobile widths; verify PAS flow, CTA visibility, lead magnet form, price anchoring, countdown, trust elements, and no layout overflow |
| QRIS production asset correctness | REQ-001 | Real QRIS image/merchant data is external to source code | Replace placeholder QRIS asset with production QRIS; create order and verify displayed amount/instructions match Rp 99.000 |
| Product ZIP content completeness | REQ-001 | Bundle content quality is a product/content check | Download verified ZIP and confirm it contains ebook/prompt bundle assets intended for sale |

---

## Validation Sign-Off

- [ ] All tasks have `<automated>` verify or Wave 0 dependencies.
- [ ] Sampling continuity: no 3 consecutive tasks without automated verify.
- [ ] Wave 0 covers all MISSING references.
- [ ] No watch-mode flags.
- [ ] Feedback latency < 90s.
- [ ] `nyquist_compliant: true` set in frontmatter after tests are in place.

**Approval:** pending
