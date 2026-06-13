@extends('layouts.app')

@section('title', 'Invoice Pembayaran - Kode Mukti')

@section('content')
<div class="container">
    <div class="invoice-page">
        {{-- Invoice Card --}}
        <div class="card invoice-card">
            {{-- Status Badge --}}
            <div class="invoice-status">
                @if ($order->status === 'pending')
                    <span class="badge badge-pending">Menunggu Pembayaran</span>
                @elseif ($order->status === 'proof_submitted')
                    <span class="badge badge-proof-submitted">Bukti Terkirim</span>
                @elseif ($order->status === 'verified')
                    <span class="badge badge-verified">Terverifikasi</span>
                @elseif ($order->status === 'rejected')
                    <span class="badge badge-rejected">Ditolak</span>
                @endif
            </div>

            {{-- Amount --}}
            <div class="invoice-amount">
                <span class="label-text text-muted">Total Pembayaran</span>
                <h2 class="heading-text text-accent">Rp {{ number_format($order->amount, 0, ',', '.') }}</h2>
            </div>

            {{-- QRIS Image --}}
            <div class="qris-section">
                <img src="{{ config('products.qris_image_path') }}"
                     alt="QRIS Pembayaran"
                     class="qris-image">
                <p class="label-text text-secondary mt-sm text-center">
                    Scan QRIS di atas untuk melakukan pembayaran
                </p>
            </div>

            {{-- Payment Steps --}}
            <div class="payment-steps">
                <h3 class="label-text mb-md">Langkah Pembayaran:</h3>
                <ol class="steps-list">
                    <li>Scan QRIS menggunakan aplikasi mobile banking atau e-wallet kamu</li>
                    <li>Bayar sesuai nominal <strong>Rp {{ number_format($order->amount, 0, ',', '.') }}</strong></li>
                    <li>Simpan bukti pembayaran (screenshot / foto) di perangkat kamu</li>
                    <li>Upload bukti pembayaran pada form di bawah</li>
                </ol>
            </div>

            {{-- Reject Alert --}}
            @if ($order->status === 'rejected' && $order->reject_reason)
                <div class="alert alert-error" role="alert">
                    <strong>Pembayaran ditolak:</strong> {{ $order->reject_reason }}
                </div>
            @endif

            {{-- Upload Form: Show for pending and rejected, secondary for proof_submitted --}}
            @if (in_array($order->status, ['pending', 'rejected', 'proof_submitted']))
                <div class="upload-section @if($order->status === 'proof_submitted') upload-section-secondary @endif">
                    <h3 class="label-text mb-md">
                        @if ($order->status === 'proof_submitted')
                            Upload Ulang Bukti Pembayaran
                        @elseif ($order->status === 'rejected')
                            Upload Ulang Bukti Pembayaran
                        @else
                            Upload Bukti Pembayaran
                        @endif
                    </h3>

                    @if ($order->status === 'proof_submitted')
                        <p class="text-secondary mb-md">Admin akan mengecek pembayaran secara manual. Jika perlu mengirim ulang, kamu bisa upload ulang di sini.</p>
                    @endif

                    <form action="{{ route('orders.proof.store', $order->invoice_token) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="upload-form">
                        @csrf

                        <div class="form-group">
                            <label for="proof" class="form-label">Bukti Pembayaran</label>
                            <input type="file"
                                   id="proof"
                                   name="proof"
                                   class="form-file @error('proof') error @enderror"
                                   accept=".jpg,.jpeg,.png,.webp"
                                   {{ $order->status === 'proof_submitted' ? '' : 'required' }}>
                            <p class="form-hint">Format: JPG, PNG, atau WebP. Maksimal 4 MB.</p>
                            @error('proof')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">
                            @if ($order->status === 'rejected')
                                Upload Ulang Bukti
                            @elseif ($order->status === 'proof_submitted')
                                Upload Ulang
                            @else
                                Kirim Bukti Pembayaran
                            @endif
                        </button>
                    </form>
                </div>
            @endif

            {{-- Verified State --}}
            @if ($order->status === 'verified')
                <div class="verified-section">
                    <p class="text-secondary mb-md">
                        Pembayaran kamu telah terverifikasi. Klik tombol di bawah untuk mendownload produk.
                    </p>
                    <a href="{{ route('orders.download', $order->invoice_token) }}"
                       class="btn btn-primary btn-full">
                        Download ZIP Ebook
                    </a>
                </div>
            @endif

            {{-- Support Note --}}
            <div class="support-note mt-lg">
                <p class="text-muted text-center">
                    Simpan link invoice ini untuk mengecek status pembayaran dan download produk. Link ini tetap aktif.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
