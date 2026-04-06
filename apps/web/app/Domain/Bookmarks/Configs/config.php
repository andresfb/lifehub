<?php

declare(strict_types=1);
/** @noinspection LaravelFunctionsInspection */

return [

    'summary_prompt' => env('BOOKMARKS_MARKER_SUMMARY_PROMPT'),

    'max_bulk_imports' => env('BOOKMARKS_MAX_BULK_IMPORTS', 50),

    'hidden_key' => 'MARKER:HIDDEN:SHOW:%s',

    'browsershot_fallback' => (bool) env('BOOKMARKS_BROWSERSHOT_FALLBACK', true),

    'browsershot_timeout' => (int) env('BOOKMARKS_BROWSERSHOT_TIMEOUT', 30),

];
