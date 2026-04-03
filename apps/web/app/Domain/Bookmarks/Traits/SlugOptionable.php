<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Traits;

use App\Enums\ModuleKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Sluggable\SlugOptions;

trait SlugOptionable
{
    private function loadSlugOptions(string $titleField): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom($titleField)
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(200)
            ->usingSuffixGenerator(
                fn (string $slug, int $iteration) => str(ModuleKey::BOOKMARKS->value)
                    ->replace('_', '-')
                    ->append('-')
                    ->append(Str::random(9))
                    ->toString()
            )
            ->extraScope(fn (Builder $builder) => $builder->where('user_id', $this->user_id))
            ->useSuffixOnFirstOccurrence();
    }
}
