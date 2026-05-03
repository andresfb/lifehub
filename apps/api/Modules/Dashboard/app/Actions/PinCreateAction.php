<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Dashboard\Dtos\PinCreateItem;
use Modules\Dashboard\Models\HomepageItem;
use Modules\Dashboard\Models\HomepageSection;
use RuntimeException;
use Throwable;

final class PinCreateAction
{
    /**
     * @throws Throwable
     */
    public function handle(int $userId, PinCreateItem $item): string
    {
        return DB::transaction(static function () use ($userId, $item): string {
            $section = HomepageSection::query()
                ->where('slug', $item->sectionSlug)
                ->where('active', true)
                ->firstOrFail();

            if (HomepageItem::found($userId, $item->getUrl(), $section->id)) {
                throw new RuntimeException('Pin already exists');
            }

            $homePageItem = $section->items()
                ->create(
                    array_merge(
                        $item->except('sectionSlug', 'tags', 'url')->toArray(),
                        [
                            'user_id' => $userId,
                            'section_id' => $section->id,
                            'url' => $item->getUrl(),
                            'active' => true,
                        ],
                    )
                );

            if (blank($item->tags)) {
                return $homePageItem->slug;
            }

            $homePageItem->tags()->attach(
                collect($item->tags)
                    ->map(static fn (string $tag) => str($tag)->trim()->lower()->value())
                    ->all()
            );

            return $homePageItem->slug;
        });
    }
}
