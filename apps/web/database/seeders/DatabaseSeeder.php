<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Bookmarks\Database\Seeders\BookmarksSeeder;
use App\Domain\Core\Database\Seeders\CoreSeeder;
use App\Domain\Dashboard\Database\Seeders\DashboardSeeder;
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
            CoreSeeder::class,
            BookmarksSeeder::class,
            DashboardSeeder::class,
        ]);
    }
}
