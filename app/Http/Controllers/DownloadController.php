<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function ebook(string $invoiceToken)
    {
        $order = Order::where('invoice_token', $invoiceToken)->firstOrFail();

        abort_unless($order->status === 'verified', 403);

        return Storage::disk('local')->download(
            config('products.ebook_zip_path'),
            'Ultimate-ChatGPT-Mastery-Bundle.zip'
        );
    }
}
