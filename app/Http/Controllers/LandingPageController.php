<?php

namespace App\Http\Controllers;

use App\Models\Order;

class LandingPageController extends Controller
{
    public function __invoke()
    {
        $earlyBirdLimit = config('products.early_bird_limit');
        $orderCount = Order::count();
        $remainingSlots = max(0, $earlyBirdLimit - $orderCount);
        $isEarlyBird = $remainingSlots > 0;

        $price = $isEarlyBird
            ? config('products.early_bird_price')
            : config('products.price');

        return view('landing', [
            'price' => $price,
            'priceNormal' => config('products.price_normal'),
            'priceDiscount' => config('products.price_discount'),
            'priceSpecial' => config('products.price_special'),
            'promoDeadline' => config('products.promo_deadline'),
            'isEarlyBird' => $isEarlyBird,
            'remainingSlots' => $remainingSlots,
            'earlyBirdLimit' => $earlyBirdLimit,
        ]);
    }
}
