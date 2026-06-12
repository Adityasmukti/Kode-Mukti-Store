# Phase 01: MVP Landing Page dan QRIS Manual - Context

**Gathered:** 2026-06-13
**Status:** Ready for planning

<domain>
## Phase Boundary

Phase ini membuat MVP Laravel untuk menjual ebook "Ultimate ChatGPT Mastery & Prompt Swipe File" melalui landing page konversi tinggi. Scope mencakup landing page, order QRIS manual, upload bukti pembayaran, admin verifikasi, akses download ebook terproteksi, dan lead magnet instant download.

Tidak termasuk payment gateway otomatis, email marketing automation, export leads CSV, atau sistem akun pembeli penuh.

</domain>

<decisions>
## Implementation Decisions

### Akses Download
- **D-01:** Pembeli mengakses halaman order/download memakai link invoice unik tanpa login.
- **D-02:** Sebelum pembayaran verified, link unik menampilkan status pending, instruksi QRIS, dan form upload atau re-upload bukti pembayaran.
- **D-03:** Setelah admin confirm pembayaran, link download tetap aktif tanpa expiry.
- **D-04:** Produk utama diberikan sebagai satu file ZIP bundle.

### Admin Verifikasi
- **D-05:** Admin panel wajib dilindungi login admin email/password.
- **D-06:** Aksi admin MVP adalah `Confirm` dan `Reject` pembayaran.
- **D-07:** Reject perlu alasan singkat agar pembeli bisa memperbaiki dan upload ulang bukti.
- **D-08:** Bukti pembayaran hanya menerima gambar: JPG, PNG, atau WebP.
- **D-09:** Admin tidak perlu notifikasi otomatis di MVP; admin cukup cek dashboard manual.

### Landing Page Copy
- **D-10:** Positioning utama adalah hemat waktu dan produktivitas untuk pemilik bisnis, kreator konten, dan profesional.
- **D-11:** CTA utama hero section adalah "Dapatkan Sekarang" dengan harga Rp 99.000 ditampilkan dekat tombol.
- **D-12:** Urgency menggunakan countdown promo yang configurable, bukan klaim kuota palsu.
- **D-13:** Jangan memakai testimoni palsu. Karena belum ada testimoni asli, gunakan benefit bullets, trust badge, guarantee, dan contoh isi produk sebagai social proof awal.

### Lead Magnet
- **D-14:** Form lead magnet hanya meminta email untuk menjaga opt-in rate tetap tinggi.
- **D-15:** Setelah email tersimpan, user diarahkan ke halaman download lead magnet.
- **D-16:** Halaman download lead magnet boleh menampilkan soft CTA untuk membeli ebook.
- **D-17:** Email duplikat tetap boleh submit dan download ulang; sistem cukup update timestamp opt-in terakhir.
- **D-18:** Export CSV leads ditunda dari MVP.

### Claude's Discretion
- Saat user memilih "You decide", keputusan yang diambil: status pending dengan upload/re-upload bukti, CTA "Dapatkan Sekarang", countdown promo configurable, email-only lead magnet.

</decisions>

<canonical_refs>
## Canonical References

**Downstream agents MUST read these before planning or implementing.**

### Planning Artifacts
- `.planning/PROJECT.md` — project context, product goal, stack, hosting, and locked MVP decisions.
- `.planning/REQUIREMENTS.md` — REQ-001 and REQ-002 acceptance criteria.
- `.planning/ROADMAP.md` — Phase 1 scope and success criteria.
- `.planning/notes/qris-manual-payment-decision.md` — rationale for using QRIS manual instead of Midtrans/Xendit.

### Product Content Assets
- `docs/Over 2499 ChatGPT Prompts Bonus.md` — candidate bonus prompt content for product bundle.
- `docs/Over 15000 ChatGPT Prompts v20.md` — candidate main prompt swipe file content for product bundle.
- `docs/4600 Ultimate ChatGPT Prompts Update.md` — candidate additional prompt content for product bundle.

</canonical_refs>

<code_context>
## Existing Code Insights

### Reusable Assets
- No Laravel application scaffold exists yet in the workspace.
- Existing `docs/` files appear to be product content assets, not application code.

### Established Patterns
- No application code patterns exist yet. Planner should create a minimal Laravel structure rather than refactor existing code.

### Integration Points
- New Laravel app must introduce public landing/order routes, protected admin routes, private file delivery routes, and storage for uploaded proof images and downloadable bundle files.

</code_context>

<specifics>
## Specific Ideas

- Checkout should avoid account creation for buyers.
- Download access should be driven by an invoice/token link.
- Payment proof flow must support re-upload after rejection.
- Landing copy should follow PAS and avoid fake testimonials.
- Lead magnet download page should be used as a light upsell opportunity.

</specifics>

<deferred>
## Deferred Ideas

- Payment gateway automation through Midtrans/Xendit is deferred until registration friction is acceptable.
- Email marketing integration is deferred; lead capture is database-only in MVP.
- CSV export for leads is deferred.
- Buyer account/login system is deferred.

</deferred>

---

*Phase: 01-MVP Landing Page dan QRIS Manual*
*Context gathered: 2026-06-13*
