<?php

declare(strict_types=1);

return [

    'admin_name' => env('ADMIN_NAME'),

    'admin_email' => env('ADMIN_EMAIL'),

    'browsershot_fallback' => (bool) env('BOOKMARKS_BROWSERSHOT_FALLBACK', true),

    'browsershot_timeout' => (int) env('BOOKMARKS_BROWSERSHOT_TIMEOUT', 30),

    'crawler_agent' => env(
        'CRAWLER_AGENT',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Safari/605.1.15'
    ),

];
