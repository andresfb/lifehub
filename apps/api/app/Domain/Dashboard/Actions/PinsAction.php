<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Actions;

use App\Domain\Dashboard\Dtos\HomepageItemDto;
use App\Domain\Dashboard\Dtos\HomepageSectionItem;
use App\Domain\Dashboard\Models\HomepageItem;
use App\Domain\Dashboard\Models\HomepageSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class PinsAction
{
    /**
     * @return Collection<HomepageSectionItem>
     */
    public function handle(int $userId): Collection
    {
        /** @var array<int, array<string, mixed>> $cached */
        $cached = Cache::tags("homepage:$userId")
            ->remember(
                md5("HOMEPAGE:{$userId}"),
                now()->addWeek(),
                function () use ($userId): array {
                    return HomepageSection::query()
                        ->where('user_id', $userId)
                        ->where('active', true)
                        ->with('items.tags')
                        ->with('items.media')
                        ->orderBy('order')
                        ->get()
                        ->map(function (HomepageSection $section): array {
                            return [
                                'id' => $section->id,
                                'user_id' => $section->user_id,
                                'slug' => $section->slug,
                                'name' => $section->name,
                                'items' => $section->items->map(function (HomepageItem $item): array {
                                    return [
                                        'id' => $item->id,
                                        'slug' => $item->slug,
                                        'title' => $item->title,
                                        'url' => $item->url,
                                        'bg_color' => $item->bg_color ?? '',
                                        'image' => $item->getIcon(),
                                        'tags' => $item->getTags(),
                                    ];
                                })->all(),
                            ];
                        })
                        ->all();
                }
            );

        return collect($cached)->map(function (array $section): HomepageSectionItem {
            return new HomepageSectionItem(
                id: $section['id'],
                userId: $section['user_id'],
                slug: $section['slug'],
                name: $section['name'],
                items: collect($section['items'])->map(function (array $item): HomepageItemDto {
                    return HomepageItemDto::from($item);
                }),
            );
        });
    }
}
