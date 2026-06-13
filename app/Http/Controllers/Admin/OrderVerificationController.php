<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPaymentRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderVerificationController extends Controller
{
    public function confirm(Request $request, Order $order)
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        $order->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
            'reject_reason' => null,
        ]);

        // Mark latest proof as accepted
        $order->paymentProofs()->latest()->first()?->update(['status' => 'accepted']);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pembayaran telah dikonfirmasi.');
    }

    public function reject(RejectPaymentRequest $request, Order $order)
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        $order->update([
            'status' => 'rejected',
            'reject_reason' => $request->validated('reject_reason'),
        ]);

        // Mark latest proof as rejected
        $order->paymentProofs()->latest()->first()?->update(['status' => 'rejected']);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pembayaran telah ditolak.');
    }
}
