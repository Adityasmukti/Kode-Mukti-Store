# Roadmap: Landing Page Kode Mukti

## Overview

Project ini dibangun sebagai MVP Laravel untuk menjual ebook digital dengan landing page persuasif, checkout QRIS manual, verifikasi admin, download ebook terproteksi, dan lead magnet instant download.

## Phases

- [ ] **Phase 1: MVP Landing Page dan QRIS Manual** - Landing page, checkout QRIS manual, upload bukti bayar, admin verifikasi, download ebook, dan lead magnet instant download.

## Phase Details

### Phase 1: MVP Landing Page dan QRIS Manual
**Goal**: Membuat MVP landing page Laravel yang bisa menjual ebook menggunakan QRIS manual dan memberikan lead magnet instant download.
**Depends on**: Nothing (first phase)
**Requirements**: [REQ-001, REQ-002]
**Success Criteria** (what must be TRUE):
  1. Pengunjung dapat membaca landing page ebook dengan CTA pembelian dan CTA lead magnet.
  2. Pengunjung dapat membuat order dan melihat instruksi pembayaran QRIS.
  3. Pengunjung dapat mengunggah bukti pembayaran untuk order mereka.
  4. Admin dapat memverifikasi pembayaran secara manual.
  5. Pembeli yang sudah diverifikasi dapat mengakses halaman download ebook.
  6. Pengunjung dapat mengisi email dan langsung mengunduh lead magnet.
**Plans**: 6 plans

Plans:
- [x] 01-01-PLAN.md — Laravel scaffold and product configuration (wave 1)
- [x] 01-02-PLAN.md — Data model, migrations, route contracts, controller/request shells, admin seeder, scaffold smoke tests (wave 2)
- [ ] 01-03-PLAN.md — Private asset fixtures, base Blade layout, CSS design tokens, test fixture files (wave 2)
- [ ] 01-04-PLAN.md — Public landing page and lead magnet instant download flow (wave 3)
- [ ] 01-05-PLAN.md — Buyer order, QRIS invoice states, and private proof upload/re-upload (wave 3)
- [ ] 01-06-PLAN.md — Admin login/dashboard, confirm/reject, proof preview route, and protected ebook ZIP download (wave 4)

## Progress

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 1. MVP Landing Page dan QRIS Manual | 2/6 | In progress | - |
