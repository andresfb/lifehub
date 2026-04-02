<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Enums\ModuleStatus;
use Spatie\LaravelData\Data;

final class ModuleRecordItem extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $is_core,
        public readonly bool $is_public,
        public readonly ModuleStatus $status,
    ) {}
}
