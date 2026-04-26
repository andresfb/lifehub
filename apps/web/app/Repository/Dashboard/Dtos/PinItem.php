<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos;

use Spatie\LaravelData\Data;

final class PinItem extends Data
{
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly string $url,
        public readonly string $order,
        public readonly ?string $icon = null,
        public readonly ?string $icon_color = null,
        public readonly ?string $description = null,
        public readonly array $tags = [],
    ) {}

    public function iconName(): string
    {
        return $this->icon ?? mb_strtoupper($this->title[0]);
    }

    public function iconColor(): string
    {
        return $this->icon_color ?? '#1f2937';
    }
}
