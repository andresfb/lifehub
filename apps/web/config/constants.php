<?php

declare(strict_types=1);

return [

    'admin_name' => env('ADMIN_NAME'),

    'admin_email' => env('ADMIN_EMAIL'),

    'providers' => [
        [
            'code' => 'anthropic',
            'name' => 'Anthropic',
            'models' => explode(',', env('ANTHROPIC_MODELS')),
            'enabled' => (bool) env('ANTHROPIC_ENABLED'),
            'can_web' => (bool) env('ANTHROPIC_CAN_WEB', true),
        ],
        [
            'code' => 'gemini',
            'name' => 'Gemini',
            'models' => explode(',', env('GEMINI_MODELS')),
            'enabled' => (bool) env('GEMINI_ENABLED'),
            'can_web' => (bool) env('GEMINI_CAN_WEB', true),
        ],
        [
            'code' => 'openai',
            'name' => 'OpenAI',
            'models' => explode(',', env('OPENAI_MODELS')),
            'enabled' => (bool) env('OPENAI_ENABLED'),
            'can_web' => (bool) env('OPENAI_CAN_WEB', true),
        ],
        [
            'code' => 'openrouter',
            'name' => 'OpenRouter',
            'models' => explode(',', env('OPENROUTER_MODELS')),
            'enabled' => (bool) env('OPENROUTER_ENABLED'),
            'can_web' => (bool) env('OPENROUTER_CAN_WEB', false),
        ],
        [
            'code' => 'ollama',
            'name' => 'Ollama',
            'models' => explode(',', env('OLLAMA_MODELS')),
            'enabled' => (bool) env('OLLAMA_ENABLED'),
            'can_web' => (bool) env('OLLAMA_CAN_WEB', false),
        ],
    ],

    'crawler_agent' => env(
        'CRAWLER_AGENT',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Safari/605.1.15'
    ),

];
