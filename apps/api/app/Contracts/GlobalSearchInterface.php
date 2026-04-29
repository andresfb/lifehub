<?php

declare(strict_types=1);

namespace App\Contracts;

interface GlobalSearchInterface
{
    public function getIdentifier(): string;

    /**
     * @return array<string, mixed>
     */
    public function buildGlobalSearch(): array;
}
