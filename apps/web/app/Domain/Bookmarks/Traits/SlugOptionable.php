<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Traits;

use App\Enums\ModuleKey;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\SlugOptions;

trait SlugOptionable
{
    private function loadSlugOptions(string $titleField): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom($titleField)
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(100)
            ->useSuffixOnFirstOccurrence()
            ->usingSuffixGenerator(
                fn (string $slug, int $iteration) => str(ModuleKey::BOOKMARKS->value)
                    ->replace('_', '-')
                    ->prepend("-{$this->id}")
                    ->slug()
            )
            ->extraScope(fn (Builder $builder) => $builder->where('user_id', $this->user_id));
    }
}
