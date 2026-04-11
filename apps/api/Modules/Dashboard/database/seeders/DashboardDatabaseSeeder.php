<?php

declare(strict_types=1);

namespace Modules\Dashboard\Database\Seeders;

use Illuminate\Database\Seeder;

final class DashboardDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @noinspection ClassConstantCanBeUsedInspection
     */
    public function run(): void
    {
        $this->call([
            '\Modules\Dashboard\Database\Seeders\AdminHomepageSeeder',
            '\Modules\Dashboard\Database\Seeders\AdminSearchProviders',
        ]);
    }
}
