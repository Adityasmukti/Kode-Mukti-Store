<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderPortalController;
use App\Http\Controllers\PaymentProofController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LeadMagnetController;
use App\Http\Controllers\Admin;

Route::get('/', LandingPageController::class)->name('landing');

Route::get('/robots.txt', function () {
    return response()->view('seo.robots', [
        'sitemapUrl' => url('/sitemap.xml'),
    ])->header('Content-Type', 'text/plain');
});

Route::get('/sitemap.xml', function () {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $xml .= '    <url>' . "\n";
    $xml .= '        <loc>' . url('/') . '</loc>' . "\n";
    $xml .= '        <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    $xml .= '        <changefreq>weekly</changefreq>' . "\n";
    $xml .= '        <priority>1.0</priority>' . "\n";
    $xml .= '    </url>' . "\n";
    $xml .= '</urlset>';

    return response($xml)->header('Content-Type', 'application/xml');
});
Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('throttle:orders')
    ->name('orders.store');
Route::get('/orders/{invoice_token}', [OrderPortalController::class, 'show'])
    ->name('orders.show');
Route::post('/orders/{invoice_token}/proof', [PaymentProofController::class, 'store'])
    ->middleware('throttle:uploads')
    ->name('orders.proof.store');
Route::get('/orders/{invoice_token}/download', [DownloadController::class, 'ebook'])
    ->name('orders.download');

Route::post('/lead-magnet', [LeadMagnetController::class, 'store'])
    ->middleware('throttle:leads')
    ->name('lead-magnet.store');
Route::get('/lead-magnet/{download_token}', [LeadMagnetController::class, 'downloadPage'])
    ->name('lead-magnet.show');
Route::get('/lead-magnet/{download_token}/download', [LeadMagnetController::class, 'download'])
    ->name('lead-magnet.download');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'create'])
        ->middleware('guest')
        ->name('login');
    Route::post('/login', [Admin\AuthController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
    Route::post('/logout', [Admin\AuthController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/orders', [Admin\OrderController::class, 'index'])
            ->name('orders.index');
        Route::post('/orders/{order}/confirm', [Admin\OrderVerificationController::class, 'confirm'])
            ->name('orders.confirm');
        Route::post('/orders/{order}/reject', [Admin\OrderVerificationController::class, 'reject'])
            ->name('orders.reject');
        Route::get('/orders/{order}/proof/{proof}', [Admin\OrderController::class, 'proof'])
            ->name('orders.proof');
    });
});
