@extends('layouts.app')

@section('title', 'Bukti Pembayaran - Kode Mukti')

@section('content')
<div class="container">
    <div class="admin-proof-page">
        <div class="admin-proof-header">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
        </div>

        <div class="card admin-proof-card">
            <h1 class="heading-text text-center mb-lg">Bukti Pembayaran</h1>

            <div class="admin-proof-meta text-secondary mb-md">
                <p><strong>Nama:</strong> {{ $order->name }}</p>
                <p><strong>Email:</strong> {{ $order->email }}</p>
                <p><strong>WhatsApp:</strong> {{ $order->whatsapp }}</p>
                <p><strong>Jumlah:</strong> Rp {{ number_format($order->amount, 0, ',', '.') }}</p>
                <p><strong>Upload:</strong> {{ $proof->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="admin-proof-image">
                <img src="{{ route('admin.orders.proof', [$order, $proof]) }}"
                     alt="Bukti Pembayaran {{ $order->name }}"
                     style="max-width:100%;height:auto;border-radius:8px;">
            </div>
        </div>
    </div>
</div>
@endsection
