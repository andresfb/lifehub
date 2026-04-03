<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Seeders;

use App\Services\Modules\ModuleRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

final class BookmarksSeeder extends Seeder
{
    public function __construct(
        private readonly ModuleRegistry $registry,
    ) {}

    public function run(): void
    {
        $modules = resolve('module_records');
        if (! $modules instanceof Collection) {
            throw new RuntimeException('Modules Records not found');
        }

        if ($modules->isEmpty()) {
            throw new RuntimeException('Modules Records not found');
        }

        $records = $modules->where('key', 'BOOKMARKS')->firstOrFail();

        $this->registry->syncAndAssign($records->toArray());
    }
}
