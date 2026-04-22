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

    'manifest' => [
        'version' => env('MANIFEST_VERSION', '0.0.1'),
    ],

    'providers' => [
        [
            'code' => 'anthropic',
            'name' => 'Anthropic',
            'can_web' => (bool) env('ANTHROPIC_CAN_WEB', true),
        ],
        [
            'code' => 'gemini',
            'name' => 'Gemini',
            'can_web' => (bool) env('GEMINI_CAN_WEB', true),
        ],
        [
            'code' => 'openai',
            'name' => 'OpenAI',
            'can_web' => (bool) env('OPENAI_CAN_WEB', true),
        ],
        [
            'code' => 'openrouter',
            'name' => 'OpenRouter',
            'can_web' => (bool) env('OPENROUTER_CAN_WEB', false),
        ],
        [
            'code' => 'ollama',
            'name' => 'Ollama',
            'can_web' => (bool) env('OLLAMA_CAN_WEB', true),
        ],
        [
            'code' => 'firepass',
            'name' => 'FirePass',
            'can_web' => (bool) env('FIREPASS_CAN_WEB', true),
        ],
    ],

];
