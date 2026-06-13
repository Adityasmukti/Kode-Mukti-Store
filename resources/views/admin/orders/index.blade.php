@extends('layouts.app')

@section('title', 'Dashboard Admin - Kode Mukti')

@section('content')
<div class="container">
    <div class="admin-dashboard">
        {{-- Header --}}
        <div class="admin-header">
            <h1 class="heading-text">Dashboard</h1>
            <form action="{{ route('admin.logout') }}" method="POST" class="admin-logout-form">
                @csrf
                <button type="submit" class="btn btn-secondary">Keluar</button>
            </form>
        </div>

        {{-- Empty State --}}
        @if ($orders->isEmpty())
            <div class="card admin-empty">
                <p class="heading-text text-center">{{ __('Belum ada order yang perlu diverifikasi') }}</p>
                <p class="text-secondary text-center mt-sm">
                    {{ __('Order baru dan bukti pembayaran akan muncul di sini setelah pembeli mengunggah bukti QRIS.') }}
                </p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Upload</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->name }}</td>
                                <td>{{ $order->email }}</td>
                                <td>{{ $order->whatsapp }}</td>
                                <td>Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if ($order->status === 'pending')
                                        <span class="badge badge-pending">Pending</span>
                                    @elseif ($order->status === 'proof_submitted')
                                        <span class="badge badge-proof-submitted">Bukti Terkirim</span>
                                    @elseif ($order->status === 'rejected')
                                        <span class="badge badge-rejected">Ditolak</span>
                                    @elseif ($order->status === 'verified')
                                        <span class="badge badge-verified">Terverifikasi</span>
                                    @endif
                                </td>
                                <td class="text-muted">
                                    @if ($order->paymentProofs->isNotEmpty())
                                        {{ $order->paymentProofs->first()->created_at->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if ($order->paymentProofs->isNotEmpty())
                                        @php $latestProof = $order->paymentProofs->first(); @endphp
                                        <a href="{{ route('admin.orders.proof', [$order, $latestProof]) }}"
                                           class="btn btn-secondary"
                                           target="_blank"
                                           rel="noopener noreferrer">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if (in_array($order->status, ['pending', 'proof_submitted']))
                                        <div class="admin-actions">
                                            {{-- Confirm --}}
                                            <form action="{{ route('admin.orders.confirm', $order) }}"
                                                  method="POST"
                                                  class="admin-action-form">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">Confirm</button>
                                            </form>

                                            {{-- Reject --}}
                                            <details class="admin-reject-details">
                                                <summary class="btn btn-destructive">Reject</summary>
                                                <form action="{{ route('admin.orders.reject', $order) }}"
                                                      method="POST"
                                                      class="admin-reject-form mt-sm">
                                                    @csrf
                                                    <p class="text-secondary mb-sm" style="font-size:13px;">
                                                        Tolak pembayaran ini? Masukkan alasan singkat agar pembeli
                                                        bisa upload ulang bukti yang benar.
                                                    </p>
                                                    <textarea name="reject_reason"
                                                              class="form-textarea"
                                                              rows="2"
                                                              placeholder="Alasan penolakan..."
                                                              required></textarea>
                                                    <button type="submit" class="btn btn-destructive btn-full mt-sm">
                                                        Tolak Pembayaran
                                                    </button>
                                                </form>
                                            </details>
                                        </div>
                                    @elseif ($order->status === 'rejected')
                                        <span class="text-muted">Telah ditolak</span>
                                    @elseif ($order->status === 'verified')
                                        <span class="text-muted">Telah dikonfirmasi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Stacked Cards --}}
            <div class="admin-cards">
                @foreach ($orders as $order)
                    <div class="card admin-card @if ($order->status === 'proof_submitted') admin-card-highlight @endif">
                        <div class="admin-card-header">
                            <span class="label-text">{{ $order->name }}</span>
                            @if ($order->status === 'pending')
                                <span class="badge badge-pending">Pending</span>
                            @elseif ($order->status === 'proof_submitted')
                                <span class="badge badge-proof-submitted">Bukti Terkirim</span>
                            @elseif ($order->status === 'rejected')
                                <span class="badge badge-rejected">Ditolak</span>
                            @elseif ($order->status === 'verified')
                                <span class="badge badge-verified">Terverifikasi</span>
                            @endif
                        </div>

                        <div class="admin-card-body">
                            <p class="text-secondary">{{ $order->email }}</p>
                            <p class="text-secondary">{{ $order->whatsapp }}</p>
                            <p class="text-accent label-text mt-sm">Rp {{ number_format($order->amount, 0, ',', '.') }}</p>
                            @if ($order->paymentProofs->isNotEmpty())
                                <p class="text-muted" style="font-size:13px;">
                                    Upload: {{ $order->paymentProofs->first()->created_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>

                        <div class="admin-card-footer">
                            @if ($order->paymentProofs->isNotEmpty())
                                @php $latestProof = $order->paymentProofs->first(); @endphp
                                <a href="{{ route('admin.orders.proof', [$order, $latestProof]) }}"
                                   class="btn btn-secondary btn-full"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Lihat Bukti
                                </a>
                            @endif

                            @if (in_array($order->status, ['pending', 'proof_submitted']))
                                <div class="admin-card-actions">
                                    <form action="{{ route('admin.orders.confirm', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-full">Confirm</button>
                                    </form>

                                    <details class="admin-reject-details">
                                        <summary class="btn btn-destructive btn-full">Reject</summary>
                                        <form action="{{ route('admin.orders.reject', $order) }}"
                                              method="POST"
                                              class="mt-sm">
                                            @csrf
                                            <p class="text-secondary mb-sm" style="font-size:13px;">
                                                Tolak pembayaran ini? Masukkan alasan singkat agar pembeli
                                                bisa upload ulang bukti yang benar.
                                            </p>
                                            <textarea name="reject_reason"
                                                      class="form-textarea"
                                                      rows="2"
                                                      placeholder="Alasan penolakan..."
                                                      required></textarea>
                                            <button type="submit" class="btn btn-destructive btn-full mt-sm">
                                                Tolak Pembayaran
                                            </button>
                                        </form>
                                    </details>
                                </div>
                            @elseif ($order->status === 'rejected')
                                <p class="text-muted text-center mt-sm">Telah ditolak</p>
                            @elseif ($order->status === 'verified')
                                <p class="text-muted text-center mt-sm">Telah dikonfirmasi</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
