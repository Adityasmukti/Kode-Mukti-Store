@extends('layouts.app')

@section('title', 'Download 50 Prompt Pemasaran Gratis — Kode Mukti')

@section('content')
    <div class="download-page">
        <div class="container">
            {{-- Download Card --}}
            <div class="card download-card text-center">
                <div class="download-icon">&#128229;</div>
                <h1 class="heading-text">Download 50 Prompt Pemasaran Gratis</h1>
                <p class="body-text text-secondary">
                    Terima kasih! Link download kamu sudah siap. Klik tombol di bawah untuk
                    mengunduh 50 prompt pemasaran terbaik.
                </p>

                <a href="{{ route('lead-magnet.download', $lead->download_token) }}"
                   class="btn btn-primary btn-full download-btn">
                    Download Lead Magnet
                </a>

                <p class="text-muted mt-sm" style="font-size:14px;">
                    &#10003; File PDF &middot; Siap pakai &middot; 50 prompt pemasaran
                </p>
            </div>

            {{-- Soft Upsell Card --}}
            <div class="card upsell-card text-center">
                <h2 class="heading-text">Butuh sistem prompt yang lebih lengkap?</h2>
                <p class="body-text text-secondary">
                    Bundle 15.000+ prompt ChatGPT siap pakai untuk marketing, bisnis, dan
                    produktivitas — tinggal <em>copy-paste</em>, hasil langsung optimal.
                </p>
                <div class="upsell-price">
                    <span class="display-text text-accent">Rp 49.000</span>
                </div>
                <a href="{{ route('landing') }}#checkout" class="btn btn-secondary btn-full" data-umami-event="CTA Click">
                    Lihat Ebook Rp 49.000
                </a>
                <p class="text-muted mt-sm" style="font-size:14px;">
                    &#10003; QRIS manual &middot; Download setelah verifikasi &middot; Tanpa login
                </p>
            </div>

            {{-- Back to Home --}}
            <div class="text-center mt-lg">
                <a href="{{ route('landing') }}" class="text-secondary">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .download-page {
        padding: 48px 0;
    }
    .download-card {
        max-width: 480px;
        margin: 0 auto;
        border: 2px solid var(--color-accent);
    }
    .download-icon {
        font-size: 48px;
        margin-bottom: 8px;
    }
    .download-btn {
        margin-top: 24px;
        font-size: 18px;
        padding: 12px 32px;
    }

    .upsell-card {
        max-width: 480px;
        margin: 24px auto 0;
        background-color: var(--color-bg);
        border: 2px solid var(--color-accent);
    }
    .upsell-price {
        margin: 16px 0;
    }

    @media (min-width: 768px) {
        .download-page {
            padding: 64px 0;
        }
    }
</style>
@endpush
