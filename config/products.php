<?php

/**
 * Product Configuration — Single Source of Truth
 *
 * This config file is consumed by all downstream plans (01-02 through 01-06)
 * for product pricing and asset paths. Do NOT hardcode prices or paths in
 * controllers, views, or models — always reference via config('products.*').
 *
 * Payment gateway keys, email service config, and other deferred feature
 * configuration do NOT belong here. Add those in their own config files
 * when the features are implemented.
 *
 * @see .planning/REQUIREMENTS.md REQ-001, REQ-002
 * @see .planning/phases/01-mvp-landing-page-dan-qris-manual/01-CONTEXT.md D-11, D-12
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Product Price
    |--------------------------------------------------------------------------
    |
    | Fixed price in Rupiah (integer, cents-free). Decision D-11 sets this at
    | Rp 99.000. Override via PRODUCT_PRICE env for promotions.
    |
    */
    'price' => (int) env('PRODUCT_PRICE', 99000),

    /*
    |--------------------------------------------------------------------------
    | Ebook Bundle ZIP Path
    |--------------------------------------------------------------------------
    |
    | Relative path inside storage/app/private/. When the order is verified,
    | the download controller streams this file using Storage::disk('local').
    |
    */
    'ebook_zip_path' => env('PRODUCT_EBOOK_ZIP_PATH', 'products/Ultimate-ChatGPT-Mastery-Bundle.zip'),

    /*
    |--------------------------------------------------------------------------
    | Lead Magnet File Path
    |--------------------------------------------------------------------------
    |
    | Relative path inside storage/app/private/. After email opt-in, the lead
    | magnet controller streams this file using Storage::disk('local').
    |
    */
    'lead_magnet_path' => env('PRODUCT_LEAD_MAGNET_PATH', 'lead-magnets/50-prompt-pemasaran-gratis.txt'),

    /*
    |--------------------------------------------------------------------------
    | QRIS Image Path
    |--------------------------------------------------------------------------
    |
    | Public asset path served from public/images/qris.png. This must be
    | replaced with the actual merchant QRIS image before deployment.
    |
    */
    'qris_image_path' => env('PRODUCT_QRIS_IMAGE_PATH', '/images/qris.png'),

    /*
    |--------------------------------------------------------------------------
    | Promo Deadline
    |--------------------------------------------------------------------------
    |
    | Configurable deadline used for the countdown timer on the landing page.
    | Decision D-12 requires urgency via promo deadline, not fake stock scarcity.
    | Format: YYYY-MM-DD. Fallback ensures a valid future date.
    |
    */
    'promo_deadline' => env('PROMO_DEADLINE', '2026-07-31'),

];
