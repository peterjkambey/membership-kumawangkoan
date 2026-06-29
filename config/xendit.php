<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aktifkan/nonaktifkan Payment Gateway
    |--------------------------------------------------------------------------
    | Set true untuk beneran bikin VA/QRIS via Xendit.
    | Set false untuk dry-run (simulasi aja, nggak beneran).
    */
    'enabled' => env('XENDIT_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | API Key (Xendit)
    |--------------------------------------------------------------------------
    |https://dashboard.xendit.co/settings/developers
    */
    'api_key' => env('XENDIT_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Callback / Webhook Token
    |--------------------------------------------------------------------------
    | Buat verifikasi bahwa callback beneran dari Xendit.
    */
    'webhook_verification_token' => env('XENDIT_WEBHOOK_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Untuk VA
    |--------------------------------------------------------------------------
    */
    'va' => [
        'prefix' => env('XENDIT_VA_PREFIX', 'KMN'),
        'expiry_minutes' => env('XENDIT_VA_EXPIRY', 1440), // 24 jam default
    ],

    /*
    |--------------------------------------------------------------------------
    | Untuk QRIS
    |--------------------------------------------------------------------------
    */
    'qris' => [
        'expiry_minutes' => env('XENDIT_QRIS_EXPIRY', 1440),
    ],
];
