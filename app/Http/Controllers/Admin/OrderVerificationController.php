<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPaymentRequest;
use App\Models\Order;

class OrderVerificationController extends Controller
{
    public function confirm(Order $order)
    {
        // Business logic will be implemented in Plan 01-06
    }

    public function reject(RejectPaymentRequest $request, Order $order)
    {
        // Business logic will be implemented in Plan 01-06
    }
}
