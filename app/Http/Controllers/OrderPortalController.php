<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderPortalController extends Controller
{
    public function show(string $invoiceToken)
    {
        $order = Order::where('invoice_token', $invoiceToken)->firstOrFail();

        return view('orders.show', compact('order'));
    }
}
