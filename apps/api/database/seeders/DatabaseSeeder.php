<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Dashboard\Database\Seeders\DashboardDatabaseSeeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @noinspection ClassConstantCanBeUsedInspection
     */
    public function run(): void
    {
        $this->call([
            DashboardDatabaseSeeder::class,
            '\Database\Seeders\AiProvidersSeeder',
        ]);
    }
}
