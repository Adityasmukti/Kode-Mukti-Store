# Project: Landing Page Kode Mukti

## Context

Project ini adalah landing page konversi tinggi untuk menjual produk digital ebook "Ultimate ChatGPT Mastery & Prompt Swipe File".

Target utama:
- Menjual ebook seharga Rp 99.000 dengan harga coret Rp 499.000.
- Mengumpulkan leads melalui lead magnet "50 Prompt Pemasaran Gratis".
- Menggunakan Laravel di VPS sendiri dengan Cloudflared tunnel.

Keputusan awal:
- Payment MVP menggunakan QRIS manual, bukan Midtrans/Xendit.
- Flow pembayaran: order dibuat, QRIS ditampilkan, pembeli upload bukti pembayaran, admin verifikasi manual, lalu akses download ebook dibuka.
- Lead magnet diberikan sebagai instant download setelah email tersimpan.

## Product Goals

- Landing page berfungsi sebagai funnel penjualan 24/7.
- Copywriting mengikuti struktur PAS: Problem, Agitate, Solve.
- Halaman harus responsive dan fast-loading.
- Social proof, trust badge, urgency, dan CTA harus terlihat jelas.
