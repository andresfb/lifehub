<?php

namespace App\Dtos\Modules;

use Spatie\LaravelData\Data;

class MenuItem extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly string $title,
        public readonly array $routes,
        public readonly ?string $icon = null,
        public readonly ?string $shortCut = null,
    ) {}

    public function withShortCut(?string $shortCut): self
    {
        if (blank($shortCut)) {
            return $this;
        }

        return new self(
            code: $this->code,
            title: $this->title,
            routes: $this->routes,
            icon: $this->icon,
            shortCut: $shortCut,
        );
    }
}
