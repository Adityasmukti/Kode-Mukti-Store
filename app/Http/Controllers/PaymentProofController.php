<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function store(UploadPaymentProofRequest $request, string $invoiceToken)
    {
        $order = Order::where('invoice_token', $invoiceToken)->firstOrFail();

        $file = $request->file('proof');

        // Store file on local private disk under payment-proofs/{invoice_token}
        // Laravel generates a hashed filename automatically
        $path = $file->store('payment-proofs/' . $invoiceToken, 'local');

        // Create payment_proofs record
        $order->paymentProofs()->create([
            'disk' => 'local',
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'status' => 'submitted',
        ]);

        // Update order status
        $order->update([
            'status' => 'proof_submitted',
            'reject_reason' => null,
        ]);

        return redirect()->route('orders.show', $order->invoice_token)
            ->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan memeriksa pembayaran kamu.');
    }
}
