<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Sluggable\SlugOptions;

trait SlugOptionable
{
    private function loadSlugOptions(string $titleField, string $module): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom($titleField)
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(200)
            ->usingSuffixGenerator(
                fn (string $slug, int $iteration) => str($module)
                    ->replace('_', '-')
                    ->append('-')
                    ->append(Str::random(6))
                    ->lower()
                    ->toString()
            )
            ->extraScope(fn (Builder $builder) => $builder->where('user_id', $this->user_id))
            ->useSuffixOnFirstOccurrence();
    }
}
