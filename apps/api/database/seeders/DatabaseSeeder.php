<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @noinspection ClassConstantCanBeUsedInspection
     */
    public function run(): void
    {
        $this->call([
            '\App\Domain\Dashboard\Database\Seeders\AdminHomepageSeeder',
            '\App\Domain\Dashboard\Database\Seeders\AdminSearchProviders',
            '\App\Domain\Bookmarks\Database\Seeders\BookmarksSeeder',
            '\App\Domain\Dashboard\Database\Seeders\DashboardSeeder',
        ]);
    }
}
