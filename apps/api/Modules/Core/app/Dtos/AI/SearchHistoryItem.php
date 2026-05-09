<?php

declare(strict_types=1);

namespace Modules\Core\Dtos\AI;

use Override;
use Spatie\LaravelData\Data;

final class SearchHistoryItem extends Data
{
    public function __construct(
        public readonly string $module,
        public readonly string $type,
        public readonly string $term,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'query' => $this->getQuery(),
            'module' => $this->getModule(),
            'type' => $this->getType(),
        ];
    }

    public function getQuery(): string
    {
        return str($this->term)
            ->trim()
            ->lower()
            ->value();
    }

    public function getModule(): string
    {
        return str($this->module)
            ->trim()
            ->upper()
            ->value();
    }

    public function getType(): string
    {
        return str($this->type)
            ->trim()
            ->upper()
            ->value();
    }
}
