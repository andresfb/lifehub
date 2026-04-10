<?php

declare(strict_types=1);
/** @noinspection LaravelFunctionsInspection */

return [

    'summary_prompt' => env('BOOKMARKS_MARKER_SUMMARY_PROMPT'),

    'max_bulk_imports' => env('BOOKMARKS_MAX_BULK_IMPORTS', 50),

    'hidden_key' => 'MARKER:HIDDEN:SHOW:%s',

    'mutator_banded_domains' => explode(',', (string) env('BOOKMARKS_MUTATOR_BANDED_DOMAINS', 'imdb.com')),

];
