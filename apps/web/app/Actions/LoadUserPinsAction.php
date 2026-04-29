<?php

declare(strict_types=1);

namespace App\Actions;

use App\Repository\Dashboard\Dtos\SectionItem;
use App\Repository\Dashboard\Services\ApiPinsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

final readonly class LoadUserPinsAction
{
    public function __construct(
        private ApiPinsService $pinsService,
    ) {}

    /**
     * @return Collection<string, SectionItem>
     */
    public function handle(int $userId): Collection
    {
        $cached = Cache::remember(
            md5("user-pins-{$userId}"),
            now()->addDay(),
            function () use ($userId): array {
                return $this->pinsService->getUserPins($userId);
            }
        );

        if (blank($cached)) {
            throw new RuntimeException('Pins not found');
        }

        return collect($cached)->map(fn (array $section): SectionItem => SectionItem::from($section));
    }
}
