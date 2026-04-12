<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Dashboard\Http\Resources\HomepageSectionResource;
use Modules\Dashboard\Models\HomepageSection;
use Throwable;

final class PinsAction
{
    /**
     * @return Collection<HomepageSection>
     *
     * @throws Throwable
     */
    public function handle(int $userId): Collection
    {
        return HomepageSection::query()
            ->where('user_id', $userId)
            ->where('active', true)
            ->with('items.tags')
            ->with('items.media')
            ->orderBy('order')
            ->get();
    }

    /**
     * @throws Throwable
     */
    public function getJsonPayload(int $userId, string $routeName, array $validated): string
    {
        $key = str(sprintf(
            'ROUTE:%s|USER:%s|OPTIONS:%s',
            $routeName,
            $userId,
            collect($validated)->implode(':')
        ))
            ->upper()
            ->toString();

        $payload = Cache::get($key);
        if (filled($payload)) {
            return $payload;
        }

        $data = $this->handle($userId);
        if ($data->isEmpty()) {
            return json_encode([
                'data' => [],
            ], JSON_THROW_ON_ERROR);
        }

        $payload = HomepageSectionResource::collection($data)
            ->response()
            ->getContent();

        Cache::put($key, $payload, now()->addWeek());

        return $payload;
    }
}
