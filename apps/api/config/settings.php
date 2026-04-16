<?php

declare(strict_types=1);

return [

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
