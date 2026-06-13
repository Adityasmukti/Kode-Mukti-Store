<?php

namespace App\Http\Controllers;

class LandingPageController extends Controller
{
    public function __invoke()
    {
        return view('landing', [
            'price' => config('products.price'),
            'promoDeadline' => config('products.promo_deadline'),
            'originalPrice' => 499000,
        ]);
    }
}
