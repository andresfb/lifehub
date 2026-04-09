<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Observers;

use App\Domain\Bookmarks\Models\Category;
use Illuminate\Support\Facades\Cache;

final class CategoryObserver
{
    public function creating(Category $category): void
    {
        if (filled($category->order)) {
            return;
        }

        $category->order = Category::query()->max('order') + 1;
    }

    public function saved(Category $category): void
    {
        Cache::tags("categories:{$category->user_id}")->flush();
    }
}
