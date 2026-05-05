<?php

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Dashboard\Dtos\PinUpdateItem;
use Modules\Dashboard\Models\HomepageItem;
use Modules\Dashboard\Models\HomepageSection;
use Throwable;

class PinUpdateAction
{
    /**
     * @throws Throwable
     */
    public function handle(HomepageItem $pin, PinUpdateItem $item): void
    {
        DB::transaction(static function () use ($pin, $item): void {
            $section = HomepageSection::query()
                ->where('slug', $item->sectionSlug)
                ->where('active', true)
                ->firstOrFail();

            $pin->update(
                array_merge(
                    $item->except('sectionSlug', 'tags', 'url')->toArray(),
                    [
                        'section_id' => $section->id,
                        'url' => $item->getUrl()
                    ],
                )
            );

            if (blank($item->tags)) {
                return;
            }

            $pin->syncTags(
                collect($item->tags)
                    ->map(static fn (string $tag) => str($tag)->trim()->lower()->value())
                    ->all()
            );
        });
    }
}
