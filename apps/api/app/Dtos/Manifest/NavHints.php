<?php

declare(strict_types=1);

namespace App\Dtos\Manifest;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class NavHints extends Data
{
    public function __construct(
        public readonly ?string $webPath = null,
        public readonly ?string $tuiCommand = null,
        public readonly ?string $icon = null,
        public readonly ?string $shortcutKey = null,
        public readonly bool $showInMenu = false,
    ) {}
}
