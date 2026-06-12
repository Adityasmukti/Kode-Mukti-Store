<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Order;

class PaymentProofController extends Controller
{
    public function store(UploadPaymentProofRequest $request, string $invoiceToken)
    {
        // Business logic will be implemented in Plan 01-05
    }
}
