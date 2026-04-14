<?php

return [

    'backend' => [
        'url' => env('API_BASE_URL'),
        'endpoints' => [
            'auth' => [
                'login' => env('API_BASE_URL') . '/login',
                'logout' => env('API_BASE_URL') . '/logout',
                'user' => env('API_BASE_URL') . '/me',
            ],
        ],
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

];
