<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Actions\SearchProviders;

use App\Repository\Dashboard\Dtos\SearchProviderItem;
use App\Repository\Dashboard\Services\ApiSearchProviderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

final readonly class LoadUserSearchProvidersAction
{
    public function __construct(
        private ApiSearchProviderService $providerService,
    ) {}

    /**
     * @return Collection<string, SearchProviderItem>
     */
    public function handle(int $userId): Collection
    {
        $cached = Cache::remember(
            md5("user-search-providers-{$userId}"),
            now()->addDay(),
            function () use ($userId): array {
                return $this->providerService->getProviders($userId);
            }
        );

        if (blank($cached)) {
            throw new RuntimeException('Search Providers not found');
        }

        return collect($cached)->map(fn (array $payload): SearchProviderItem => SearchProviderItem::from($payload));
    }
}
