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
        $earlyBirdLimit = config('products.early_bird_limit');
        $orderCount = Order::count();
        $price = $orderCount < $earlyBirdLimit
            ? config('products.early_bird_price')
            : config('products.price');

        $order = Order::create([
            'invoice_token' => Str::random(32),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'whatsapp' => $request->input('whatsapp'),
            'amount' => $price,
            'status' => 'pending',
        ]);

        Log::info('Umami Event: Order Created', [
            'invoice_token' => $order->invoice_token,
            'email' => $order->email,
            'amount' => $order->amount,
        ]);

        return redirect()->route('orders.show', $order->invoice_token);
    }
}
