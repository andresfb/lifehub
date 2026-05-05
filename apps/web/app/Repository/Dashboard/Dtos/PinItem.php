<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos;

use Spatie\LaravelData\Data;

use function mb_strtoupper;

final class PinItem extends Data
{
    /**
     * @param  array<int, string>  $tags
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly string $url,
        public readonly bool $active = true,
        public readonly array $tags = [],
        public readonly ?int $order = null,
        public readonly ?string $icon = null,
        public readonly ?string $icon_color = null,
        public readonly ?string $description = null,
        public readonly ?string $section_slug = null,
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
