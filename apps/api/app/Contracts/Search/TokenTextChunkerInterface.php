<?php

declare(strict_types=1);

namespace App\Contracts\Search;

interface TokenTextChunkerInterface
{
    /**
     * @return array<int, string>
     */
    public function chunk(string $text, string $model = 'text-embedding-3-small'): array;
}
