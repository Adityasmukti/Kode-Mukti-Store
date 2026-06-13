# Requirements

## REQ-001: Landing Page Laravel dengan QRIS Manual

Landing page harus dibangun menggunakan Laravel dan mendukung flow penjualan ebook tanpa payment gateway otomatis.

Acceptance criteria:
- [x] Pengunjung dapat melihat landing page penjualan ebook "Ultimate ChatGPT Mastery & Prompt Swipe File".
- [x] Pengunjung dapat membuat order dengan mengisi data minimal: nama, email, dan nomor WhatsApp.
- [x] Sistem menampilkan QRIS sebagai metode pembayaran manual untuk nominal Rp 99.000.
- [x] Pengunjung dapat mengunggah bukti pembayaran setelah membayar melalui QRIS.
- [ ] Admin dapat melihat order pending dan mengonfirmasi pembayaran secara manual.
- [ ] Setelah pembayaran dikonfirmasi, halaman download ebook aktif untuk pembeli tersebut.

## REQ-002: Lead Magnet Instant Download

Landing page harus menyediakan lead magnet "50 Prompt Pemasaran Gratis" untuk menangkap email calon pembeli.

Acceptance criteria:
- Pengunjung dapat mengisi email untuk mendapatkan lead magnet.
- Email pengunjung tersimpan sebagai prospek.
- Setelah email berhasil disimpan, pengunjung langsung mendapat akses download lead magnet tanpa menunggu email otomatis.
- Flow lead magnet harus tetap sederhana dan tidak membutuhkan integrasi email marketing pada MVP.
