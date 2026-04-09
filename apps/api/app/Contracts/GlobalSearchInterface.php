<?php

declare(strict_types=1);

namespace App\Contracts;

interface GlobalSearchInterface
{
    public function getIdentifier(): string;

    public function buildGlobalSearch(): array;
}
