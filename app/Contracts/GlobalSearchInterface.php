<?php

namespace App\Contracts;

interface GlobalSearchInterface
{
    public function getIdentifier(): string;

    public function buildGlobalSearch(): array;
}
