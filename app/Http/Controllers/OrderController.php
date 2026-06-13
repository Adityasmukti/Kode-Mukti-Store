<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
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

        Log::info('Umami Event: Order Created', [
            'invoice_token' => $order->invoice_token,
            'email' => $order->email,
        ]);

        return redirect()->route('orders.show', $order->invoice_token);
    }
}
}
