<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $order = Order::create([
            'invoice_token' => Str::random(32),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'whatsapp' => $request->input('whatsapp'),
            'amount' => config('products.price', 99000),
            'status' => 'pending',
        ]);

        return redirect()->route('orders.show', $order->invoice_token);
    }
}
