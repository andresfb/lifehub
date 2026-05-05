<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Actions\Pins;

use App\Repository\Dashboard\Dtos\SectionItem;
use App\Repository\Dashboard\Enums\PinStatus;
use App\Repository\Dashboard\Services\ApiPinsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Throwable;

final readonly class LoadUserPinsAction
{
    public function __construct(
        private ApiPinsService $pinsService,
    ) {}

    /**
     * @return Collection<string, SectionItem>
     *
     * @throws Throwable
     */
    public function handle(int $userId, PinStatus $status = PinStatus::ACTIVE): Collection
    {
        $cached = Cache::tags(['pins'])
            ->remember(
                md5("user-pins-{$userId}-{$status->name}"),
                now()->addDay(),
                function () use ($userId, $status): array {
                    return $this->pinsService->getUserPins($userId, $status);
                }
            );

        if (blank($cached)) {
            throw new RuntimeException('Pins not found');
        }

        return collect($cached)
            ->map(fn (array $section): SectionItem => SectionItem::from($section));
    }
}
