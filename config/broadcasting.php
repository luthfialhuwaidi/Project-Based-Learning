<?php

return [

    /*
    | Ubah bagian ini agar defaultnya mengambil dari .env, 
    | atau langsung 'log' jika .env tidak terbaca.
    */
    'default' => env('BROADCAST_CONNECTION', 'log'), 

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            // Nilai default 'fake-xxx' ini mencegah error "TypeError: null given" 
            // saat file cache sedang dibersihkan.
            'key' => env('PUSHER_APP_KEY', 'fake-key'),
            'secret' => env('PUSHER_APP_SECRET', 'fake-secret'),
            'app_id' => env('PUSHER_APP_ID', 'fake-id'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                'useTLS' => true,
                'encrypted' => true,
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'ap1').'.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ],
            ],
            'client_options' => [],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];