<?php

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Dashboard\Dtos\PinCreateItem;
use Modules\Dashboard\Models\HomepageItem;
use Modules\Dashboard\Models\HomepageSection;
use RuntimeException;
use Throwable;

class PinCreateAction
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

            $item = $section->items()
                ->create(
                    array_merge(
                        $item->except('sectionSlug', 'tags', 'url')->toArray(),
                        [
                            'user_id' => $userId,
                            'section_id' => $section->id,
                            'url' => $item->getUrl(),
                        ],
                    )
                );

            $item->tags()->attach($item->tags);

            return $item->slug;
        });
    }
}
