<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Database\Seeders;

use App\Services\Modules\ModuleRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

final class DashboardSeeder extends Seeder
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

        $records = $modules->where('key', 'DASHBOARD')->firstOrFail();

        $this->registry->syncAndAssign($records->toArray());
    }
}
