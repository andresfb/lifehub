<?php

declare(strict_types=1);

return [
    'hybrid' => [
        'index' => env('MEILISEARCH_GLOBAL_SEARCH_CHUNKS_INDEX', 'global_search_chunks'),
        'embedder' => env('MEILISEARCH_GLOBAL_SEARCH_EMBEDDER', 'global_search_user_provided'),
        'semantic_ratio' => (float) env('MEILISEARCH_GLOBAL_SEARCH_SEMANTIC_RATIO', 0.6),
        'dimensions' => (int) env('GLOBAL_SEARCH_EMBEDDING_DIMENSIONS', 1536),
        'target_tokens' => (int) env('GLOBAL_SEARCH_CHUNK_TARGET_TOKENS', 512),
        'overlap_tokens' => (int) env('GLOBAL_SEARCH_CHUNK_OVERLAP_TOKENS', 64),
        'timeout' => (int) env('GLOBAL_SEARCH_EMBEDDING_TIMEOUT', 300), // 5 Minutes
    ],
];
