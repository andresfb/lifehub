<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Dashboard\Dtos\HomepageItemDto;
use Modules\Dashboard\Dtos\HomepageSectionItem;
use Modules\Dashboard\Models\HomepageItem;
use Modules\Dashboard\Models\HomepageSection;

final class PinsAction
{
    /**
     * @return Collection<HomepageSectionItem>
     */
    public function handle(int $userId): Collection
    {
        /** @var array<int, array<string, mixed>> $cached */
        $cached = Cache::tags("homepage:{$userId}")
            ->remember(
                md5("HOMEPAGE:{$userId}"),
                now()->addWeek(),
                fn (): array => HomepageSection::query()
                    ->where('user_id', $userId)
                    ->where('active', true)
                    ->with('items.tags')
                    ->with('items.media')
                    ->orderBy('order')
                    ->get()
                    ->map(fn (HomepageSection $section): array => [
                        'id' => $section->id,
                        'user_id' => $section->user_id,
                        'slug' => $section->slug,
                        'name' => $section->name,
                        'order' => $section->order,
                        'items' => $section->items->map(fn (HomepageItem $item): array => [
                            'id' => $item->id,
                            'slug' => $item->slug,
                            'title' => $item->title,
                            'url' => $item->url,
                            'order' => $item->order,
                            'description' => $item->description ?? '',
                            'color' => $item->bg_color ?? '',
                            'image' => $item->getIcon(),
                            'tags' => $item->getTags(),
                        ])->all(),
                    ])
                    ->all()
            );

        return collect($cached)->map(fn (array $section): HomepageSectionItem => new HomepageSectionItem(
            id: $section['id'],
            userId: $section['user_id'],
            slug: $section['slug'],
            name: $section['name'],
            order: $section['order'],
            items: collect($section['items'])->map(fn (array $item): HomepageItemDto => HomepageItemDto::from($item)),
        ));
    }
}
