<?php

declare(strict_types=1);

namespace App\Actions;

use App\Repository\Auth\Dtos\User;
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

    public function handle(User $user): Collection
    {
        $cached = Cache::remember(
            md5("user-pins-{$user->id}"),
            now()->addDay(),
            function () use ($user): array {
                return $this->pinsService->getUserPins($user->id);
            }
        );

        if (blank($cached)) {
            throw new RuntimeException('Pins not found');
        }

        return collect($cached)->map(fn (array $section): SectionItem => SectionItem::from($section));
    }
}
