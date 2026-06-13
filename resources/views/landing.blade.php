@extends('layouts.app')

@section('title', 'Ultimate ChatGPT Mastery & Prompt Swipe File — Kode Mukti')

@section('content')
    {{-- 1. Hero Section --}}
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-copy">
                    <img src="/images/logo.png"
                         alt="Kode Mukti"
                         width="80" height="80"
                         class="hero-logo"
                         loading="eager">
                    <h1 class="display-text hero-headline">
                        Hemat 100+ Jam Setiap Bulan — <span class="text-accent">Tanpa Pusing Mikirin Prompt</span>
                    </h1>
                    <p class="body-text hero-subcopy text-secondary">
                        Bundle 15.000+ prompt siap pakai untuk konten marketing, bisnis, dan produktivitas.
                        Tinggal <em>copy-paste</em>, hasil langsung maksimal. Cocok untuk pemilik bisnis,
                        kreator konten, dan profesional yang butuh hasil cepat.
                    </p>
                    <div class="hero-actions">
                        <div class="price-card-hero">
                            <div class="price-tier">
                                <span class="price-tier-label">Harga Normal</span>
                                <span class="price-tier-value cross">Rp {{ number_format($priceNormal, 0, ',', '.') }}</span>
                            </div>
                            <div class="price-tier">
                                <span class="price-tier-label">Diskon Launching</span>
                                <span class="price-tier-value cross">Rp {{ number_format($priceDiscount, 0, ',', '.') }}</span>
                            </div>
                            <div class="price-tier price-tier-highlight">
                                <span class="badge badge-promo">PROMO 100 PEMBELI PERTAMA</span>
                                <span class="price-tier-label">Spesial 100 Pembeli Pertama</span>
                                <span class="price-tier-final display-text text-accent">Rp {{ number_format($priceSpecial, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <a href="#checkout" class="btn btn-primary btn-full btn-hero" data-umami-event="CTA Click">Dapatkan Sekarang — Rp4.900</a>
                        <p class="text-muted mt-sm" style="font-size:14px;">
                            &#10003; Akses langsung &middot; File ZIP &middot; Tanpa login
                        </p>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="card hero-card">
                        <div class="hero-card-header">
                            <span class="badge badge-verified">Best Seller</span>
                        </div>
                        <div class="hero-card-body">
                            <p class="label-text">Termasuk:</p>
                            <ul class="hero-bundles">
                                <li>&#9989; 15.000+ Prompt ChatGPT</li>
                                <li>&#9989; 2.499 Prompt Bonus</li>
                                <li>&#9989; 4.600 Ultimate Prompt</li>
                                <li>&#9989; File Excel &amp; PDF</li>
                                <li>&#9989; Update Gratis</li>
                            </ul>
                        </div>
                        <div class="hero-card-footer">
                            <a href="#lead-magnet" class="btn btn-secondary btn-full">Ambil 50 Prompt Gratis</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Problem / Agitation Section --}}
    <section class="section section-problem">
        <div class="container">
            <h2 class="heading-text text-center">Buang Waktu Nulis Prompt Tiap Hari?</h2>
            <div class="problems-grid">
                <div class="card problem-card">
                    <h3 class="label-text">&#9200; 30 Menit Per Sesi</h3>
                    <p class="body-text text-secondary">
                        Nulis prompt dari nol untuk setiap konten. Dalam seminggu, berapa jam terbuang?
                    </p>
                </div>
                <div class="card problem-card">
                    <h3 class="label-text">&#128200; Output Tidak Konsisten</h3>
                    <p class="body-text text-secondary">
                        Kadang bagus, kadang melenceng. Hasil ChatGPT tergantung prompt yang kamu kasih.
                    </p>
                </div>
                <div class="card problem-card">
                    <h3 class="label-text">&#128206; Content Bottleneck</h3>
                    <p class="body-text text-secondary">
                        Ide habis, konten mandek. Padahal bisnis butuh konsistensi biar tetap relevan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Solution / Benefits Section --}}
    <section class="section section-solution">
        <div class="container">
            <h2 class="heading-text text-center">Solusi: 15.000+ Prompt Siap Pakai</h2>
            <p class="body-text text-secondary text-center" style="max-width:600px;margin:0 auto 24px;">
                Satu bundle berisi ribuan prompt yang sudah teruji. Tinggal pilih, salin, dan tempel.
                Hasilnya langsung optimal tanpa riset ulang.
            </p>
            <div class="benefits-grid">
                <div class="card benefit-card">
                    <h3 class="label-text">&#128176; Produktivitas Bisnis</h3>
                    <ul class="benefit-list">
                        <li>Copywriting iklan &amp; landing page</li>
                        <li>Email marketing &amp; automation sequences</li>
                        <li>Sales script &amp; negotiation</li>
                        <li>Business planning &amp; strategy</li>
                    </ul>
                </div>
                <div class="card benefit-card">
                    <h3 class="label-text">&#127912; Konten Kreator</h3>
                    <ul class="benefit-list">
                        <li>Caption &amp; thread sosial media</li>
                        <li>SEO blog &amp; artikel</li>
                        <li>Script YouTube, TikTok, Reels</li>
                        <li>Brand voice &amp; tone panduan</li>
                    </ul>
                </div>
                <div class="card benefit-card">
                    <h3 class="label-text">&#128187; Produktivitas Kerja</h3>
                    <ul class="benefit-list">
                        <li>Meeting agenda &amp; minutes</li>
                        <li>Project management prompts</li>
                        <li>Data analysis &amp; reporting</li>
                        <li>HR &amp; recruitment workflow</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Product Preview Section --}}
    <section class="section section-preview">
        <div class="container">
            <h2 class="heading-text text-center">Apa yang Ada di Dalam Bundle?</h2>
            <div class="preview-grid">
                <div class="card preview-card">
                    <h3 class="label-text">&#128196; Over 15.000 ChatGPT Prompts v20</h3>
                    <p class="body-text text-secondary">
                        Koleksi prompt terbesar: marketing, bisnis, kesehatan, pendidikan, teknologi,
                        kreatif, dan masih banyak lagi. Setiap prompt sudah dikategorikan.
                    </p>
                </div>
                <div class="card preview-card">
                    <h3 class="label-text">&#127775; Over 2.499 ChatGPT Prompts Bonus</h3>
                    <p class="body-text text-secondary">
                        Prompt bonus spesial: product launch, customer retention, storytelling,
                        dan framework copywriting lanjutan.
                    </p>
                </div>
                <div class="card preview-card">
                    <h3 class="label-text">&#128640; 4.600 Ultimate ChatGPT Prompts Update</h3>
                    <p class="body-text text-secondary">
                        Update terbaru dengan prompt-prompt terkini untuk AI marketing,
                        konten viral, dan automation.
                    </p>
                </div>
            </div>
            <p class="text-center text-muted mt-lg">
                Format: ZIP bundle berisi file PDF, Excel, dan Markdown.
            </p>
        </div>
    </section>

    {{-- 5. Pricing / CTA Section --}}
    <section class="section section-pricing" id="checkout">
        <div class="container">
            <div class="card pricing-card">
                <div class="pricing-header">
                    <h2 class="heading-text text-center">Ultimate ChatGPT Mastery &amp; Prompt Swipe File</h2>
                    <div class="pricing-compare">
                        <div class="price-row">
                            <span class="price-row-label">Harga Normal</span>
                            <span class="price-row-value cross">Rp {{ number_format($priceNormal, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-row">
                            <span class="price-row-label">Harga Diskon Launching</span>
                            <span class="price-row-value cross">Rp {{ number_format($priceDiscount, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-row price-row-promo">
                            <span class="badge badge-promo">PROMO 100 PEMBELI PERTAMA</span>
                            <div class="price-row-final">
                                <span class="price-row-label">Spesial 100 Pembeli Pertama</span>
                                <span class="display-text text-accent pricing-current">Rp {{ number_format($priceSpecial, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-muted mt-sm" style="font-size:14px;">
                        Khusus untuk 100 pembeli pertama, Anda bisa mendapatkan Ultimate ChatGPT Mastery hanya Rp4.900.
                        Setelah kuota promo habis, harga akan naik ke Rp49.000, lalu kembali ke harga normal Rp490.000.
                    </p>
                </div>
                <div class="pricing-badges">
                    <span class="badge badge-verified">&#10003; Pembayaran QRIS manual</span>
                    <span class="badge badge-verified">&#10003; Download setelah verifikasi</span>
                    <span class="badge badge-verified">&#10003; File ZIP bundle</span>
                    <span class="badge badge-verified">&#10003; Tanpa login pembeli</span>
                </div>
                <div class="pricing-cta">
                    <form method="POST" action="{{ route('orders.store') }}" class="checkout-form">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" id="name" name="name" class="form-input"
                                   placeholder="Nama lengkap" required maxlength="120">
                            <div class="error-text" id="name-error" role="alert"></div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-input"
                                   placeholder="email@contoh.com" required maxlength="255">
                            <div class="error-text" id="email-error" role="alert"></div>
                        </div>
                        <div class="form-group">
                            <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-input"
                                   placeholder="08123456789" required maxlength="32">
                            <div class="error-text" id="whatsapp-error" role="alert"></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full btn-hero" data-umami-event="CTA Click">Dapatkan Sekarang — Rp4.900</button>
                        <p class="text-center text-muted mt-sm" style="font-size:14px;">
                            &#10003; QRIS manual &middot; Download setelah verifikasi
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Countdown Promo Section --}}
    <section class="section section-countdown">
        <div class="container text-center">
            <h2 class="heading-text">Promo Berakhir Dalam:</h2>
            <div class="countdown-timer" data-deadline="{{ $promoDeadline }}">
                <div class="countdown-item">
                    <span class="countdown-value display-text text-accent" id="countdown-days">--</span>
                    <span class="countdown-label label-text">Hari</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value display-text text-accent" id="countdown-hours">--</span>
                    <span class="countdown-label label-text">Jam</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value display-text text-accent" id="countdown-minutes">--</span>
                    <span class="countdown-label label-text">Menit</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value display-text text-accent" id="countdown-seconds">--</span>
                    <span class="countdown-label label-text">Detik</span>
                </div>
            </div>
            <p class="text-muted body-text mt-sm">
                Promo deadline: {{ \Carbon\Carbon::parse($promoDeadline)->isoFormat('D MMMM YYYY') }}.
                Harga bisa berubah setelah promo berakhir.
            </p>
        </div>
    </section>

    {{-- 7. Lead Magnet Section --}}
    <section class="section section-lead" id="lead-magnet">
        <div class="container">
            <div class="card lead-card text-center">
                <h2 class="heading-text">Ambil <span class="text-accent">50 Prompt Pemasaran Gratis</span></h2>
                <p class="body-text text-secondary">
                    Isi email di bawah, langsung dapat 50 prompt pemasaran terbaik — gratis,
                    tanpa syarat, tanpa spam.
                </p>
                <form method="POST" action="{{ route('lead-magnet.store') }}" class="lead-form">
                    @csrf
                    <div class="form-group">
                        <label for="lead-email" class="form-label">Email</label>
                        <input type="email" id="lead-email" name="email" class="form-input"
                               placeholder="email@contoh.com" required maxlength="255">
                        <div class="error-text" id="lead-email-error" role="alert"></div>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-full" data-umami-event="Lead Captured">Kirim &amp; Download Gratis</button>
                    <p class="text-muted mt-sm" style="font-size:14px;">
                        &#10003; Email-only &middot; Instant download &middot; Tidak ada spam
                    </p>
                </form>
            </div>
        </div>
    </section>

    {{-- 8. FAQ / Support Note --}}
    <section class="section section-faq">
        <div class="container">
            <h2 class="heading-text text-center">Pertanyaan Umum</h2>
            <div class="faq-list">
                <div class="card faq-item">
                    <h3 class="label-text">Bagaimana cara pembayaran?</h3>
                    <p class="body-text text-secondary">
                        Pembayaran menggunakan QRIS manual. Setelah order, kamu akan melihat kode QRIS
                        untuk bayar. Upload bukti bayar, admin verifikasi, lalu link download aktif.
                    </p>
                </div>
                <div class="card faq-item">
                    <h3 class="label-text">Berapa lama verifikasi pembayaran?</h3>
                    <p class="body-text text-secondary">
                        Admin verifikasi manual dalam 1×24 jam di hari kerja. Kamu akan dapat akses
                        download setelah status berubah menjadi "Terverifikasi".
                    </p>
                </div>
                <div class="card faq-item">
                    <h3 class="label-text">Apa itu lead magnet? Apakah saya wajib ambil?</h3>
                    <p class="body-text text-secondary">
                        Lead magnet adalah bonus gratis berupa 50 prompt pemasaran. Tidak wajib —
                        tapi siapa sih yang nolak gratis? Isi email, langsung download.
                    </p>
                </div>
                <div class="card faq-item">
                    <h3 class="label-text">Apakah saya perlu akun atau login?</h3>
                    <p class="body-text text-secondary">
                        Tidak. Cukup isi nama, email, WhatsApp, transfer sesuai nominal, upload bukti,
                        tunggu verifikasi. Simpan link invoice karena link itu tetap aktif.
                    </p>
                </div>
                <div class="card faq-item">
                    <h3 class="label-text">Apa isi file yang saya download?</h3>
                    <p class="body-text text-secondary">
                        Satu file ZIP berisi bundle lengkap: 15.000+ prompt, bonus 2.499 prompt,
                        dan update 4.600 prompt — dalam format PDF, Excel (.xlsx), dan Markdown (.md).
                    </p>
                </div>
                <div class="card faq-item">
                    <h3 class="label-text">Ada yang perlu ditanyakan lain?</h3>
                    <p class="body-text text-secondary">
                        Hubungi kami melalui email atau WhatsApp yang tertera di halaman order.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    @vite('resources/css/landing.css')
@endpush

@push('scripts')
<script>
    (function() {
        var deadline = '{{ $promoDeadline }}';
        var target = new Date(deadline).getTime();

        function updateCountdown() {
            var now = new Date().getTime();
            var diff = target - now;

            if (diff <= 0) {
                document.getElementById('countdown-days').textContent = '00';
                document.getElementById('countdown-hours').textContent = '00';
                document.getElementById('countdown-minutes').textContent = '00';
                document.getElementById('countdown-seconds').textContent = '00';
                return;
            }

            var days = Math.floor(diff / (1000 * 60 * 60 * 24));
            var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('countdown-days').textContent = String(days).padStart(2, '0');
            document.getElementById('countdown-hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('countdown-minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('countdown-seconds').textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    })();
</script>
@endpush
