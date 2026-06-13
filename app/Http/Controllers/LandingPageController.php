<?php

namespace App\Http\Controllers;

class LandingPageController extends Controller
{
    public function __invoke()
    {
        return view('landing', [
            'price' => config('products.price'),
            'priceNormal' => config('products.price_normal'),
            'priceDiscount' => config('products.price_discount'),
            'priceSpecial' => config('products.price_special'),
            'promoDeadline' => config('products.promo_deadline'),
        ]);
    }
}
