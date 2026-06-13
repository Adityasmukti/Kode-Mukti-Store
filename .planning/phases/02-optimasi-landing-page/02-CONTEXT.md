# Phase 02: Optimasi Landing Page - Context

**Gathered:** 2026-06-13
**Status:** Ready for planning

<domain>
## Phase Boundary

Phase ini memasang Umami Analytics, conversion tracking, dan optimasi performa landing page. Scope mencakup docker-compose Umami, tracking script di Blade, custom events untuk konversi order/lead, dan perbaikan Lighthouse score.

Tidak termasuk payment gateway upgrade, email marketing integration, atau deployment konfigurasi production.
</domain>

<decisions>
## Implementation Decisions

### Analytics
- **D-01:** Gunakan Umami Analytics self-hosted via Docker Compose.
- **D-02:** Umami + PostgreSQL berjalan di container terpisah dari Laravel.
- **D-03:** Tracking script disisipkan di Blade layout via env variable agar bisa switch environment.
- **D-04:** Custom events pakai `data-umami-event` attribute pada elemen HTML.

### Conversion Tracking
- **D-05:** Track event `Order Created` saat order berhasil dibuat.
- **D-06:** Track event `Lead Captured` saat lead magnet berhasil diklaim.
- **D-07:** Track event `CTA Click` pada tombol "Dapatkan Sekarang".
- **D-08:** Track event `Download Ebook` saat download ZIP berhasil.

### Deployment & Config
- **D-09:** `UMAMI_WEBSITE_ID` dan `UMAMI_HOST_URL` di .env, fallback kosong (tracking dinonaktifkan kalau tidak diset).
- **D-10:** docker-compose.yml untuk Umami diletakkan di `deploy/docker-compose.umami.yml`.
- **D-11:** Data Umami pakai PostgreSQL volume persisted.

### Performance
- **D-12:** Target Lighthouse ≥ 80 untuk mobile dan desktop.
- **D-13:** Optimasi gambar (kompresi, dimensi tetap).
- **D-14:** Minimalisir render-blocking resources.
- **D-15:** Pasang caching headers untuk asset statis.

</decisions>

<canonical_refs>
## Canonical References

- `.planning/ROADMAP.md` — Phase 2 scope dan success criteria.
- `.planning/phases/01-mvp-landing-page-dan-qris-manual/01-CONTEXT.md` — Keputusan Phase 1.
- `.planning/phases/01-mvp-landing-page-dan-qris-manual/01-03-SUMMARY.md` — Layout dan CSS yang sudah ada.
- `.planning/phases/01-mvp-landing-page-dan-qris-manual/01-UI-SPEC.md` — Design tokens.

### External Docs
- https://umami.is/docs — Official Umami docs.
- https://umami.is/docs/tracking-config — Custom events documentation.
</canonical_refs>

<code_context>
## Existing Code Insights

### Reusable Assets
- `resources/views/layouts/app.blade.php` — Base layout tempat tracking script disisipkan.
- `config/products.php` — Pattern untuk konfigurasi via env.
- `resources/views/landing.blade.php` — Halaman utama dengan tombol CTA untuk tracking events.
- `resources/views/orders/show.blade.php` — Invoice page dengan status order events.

### Integration Points
- Blade layout `<head>` → Umami tracking script.
- Tombol "Dapatkan Sekarang" → `data-umami-event="CTA Click"`.
- OrderController@store → `data-umami-event="Order Created"`.
- LeadMagnetController → `data-umami-event="Lead Captured"`.
- DownloadController → `data-umami-event="Download Ebook"`.
</code_context>

---

*Phase: 02-Optimasi Landing Page*
*Context gathered: 2026-06-13*
