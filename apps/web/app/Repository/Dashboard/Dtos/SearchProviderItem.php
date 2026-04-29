<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos;

use Spatie\LaravelData\Data;

use function mb_strtoupper;

final class SearchProviderItem extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $url,
        public readonly bool $default,
        public readonly int $order = 0,
        public readonly ?string $icon = null,
        public readonly ?string $icon_color = null,
    ) {}

    public function iconName(): string
    {
        return $this->icon ?? mb_strtoupper($this->name[0]);
    }

    public function iconColor(): string
    {
        return $this->icon_color ?? '#1f2937';
    }
}
