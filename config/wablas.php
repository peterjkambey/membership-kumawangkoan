<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aktifkan/nonaktifkan integrasi Wablas
    |--------------------------------------------------------------------------
    | Set true untuk mengirim WA sungguhan.
    | Set false untuk dry-run (log aja, nggak kirim).
    */
    'enabled' => env('WABLAS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    */
    'domain' => env('WABLAS_DOMAIN', 'https://your-domain.wablas.com'),
    'api_key' => env('WABLAS_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Sender
    |--------------------------------------------------------------------------
    */
    'sender_name' => env('WABLAS_SENDER_NAME', 'Kumawangkoan'),
];
