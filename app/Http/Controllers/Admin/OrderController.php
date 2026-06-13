<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        $orders = Order::with('paymentProofs')
            ->orderByRaw("CASE status WHEN 'proof_submitted' THEN 0 WHEN 'pending' THEN 1 WHEN 'rejected' THEN 2 WHEN 'verified' THEN 3 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function proof(Request $request, Order $order, PaymentProof $proof)
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        // Verify proof belongs to the requested order
        if ($proof->order_id !== $order->id) {
            abort(404);
        }

        $path = Storage::disk('local')->path($proof->path);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $proof->mime,
            'Content-Disposition' => 'inline',
        ]);
    }
}
