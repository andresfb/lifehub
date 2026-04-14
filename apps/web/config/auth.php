<?php

declare(strict_types=1);

return [

    'defaults' => [
        'guard' => 'web',
    ],

    'guards' => [
        'web' => [
            'driver' => 'backend-session',
        ],
    ],

    'providers' => [
        // Not needed for viaRequest guards.
    ],

];
