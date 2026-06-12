---
title: Design MVP Order Flow
date: 2026-06-12
priority: high
---

# Design MVP Order Flow

Rancang flow MVP untuk penjualan ebook berbasis Laravel dengan QRIS manual.

Tasks:
- Buat struktur order: nama, email, nomor WhatsApp, status pembayaran, bukti pembayaran, dan akses download.
- Tentukan status order minimal: pending, proof_uploaded, verified, rejected.
- Rancang halaman checkout/order confirmation yang menampilkan QRIS dan instruksi pembayaran.
- Rancang upload bukti pembayaran dari sisi pembeli.
- Rancang panel admin untuk melihat order pending dan mengonfirmasi pembayaran.
- Rancang proteksi halaman download agar hanya order verified yang bisa mengakses ebook.

Definition of done:
- Flow end-to-end jelas dari landing page sampai download ebook.
- Field database dan halaman utama sudah teridentifikasi.
- Risiko manual verification dan file protection sudah tercakup.
