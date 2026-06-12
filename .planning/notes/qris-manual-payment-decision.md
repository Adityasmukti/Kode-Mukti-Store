---
title: QRIS Manual Payment Decision
date: 2026-06-12
context: Exploration for ebook landing page MVP
---

# QRIS Manual Payment Decision

Diputuskan untuk tidak memakai Midtrans/Xendit pada MVP karena proses pendaftaran dinilai terlalu rumit untuk kebutuhan awal.

Keputusan MVP:
- Stack aplikasi: Laravel.
- Hosting: VPS sendiri dengan Cloudflared tunnel.
- Payment: QRIS manual.
- Flow pembayaran: order dibuat, QRIS ditampilkan, pembeli upload bukti pembayaran, admin verifikasi manual, lalu akses download ebook dibuka.

Alasan:
- Lebih cepat dibuat.
- Tidak bergantung pada approval payment gateway.
- Tetap bisa di-upgrade ke payment gateway otomatis di masa depan tanpa mengubah konsep besar checkout.

Risiko:
- Verifikasi pembayaran belum otomatis.
- Admin perlu mengecek bukti pembayaran secara manual.
- Perlu proteksi download agar file ebook tidak bisa diakses publik tanpa order terverifikasi.
