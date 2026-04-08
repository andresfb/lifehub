<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Bookmarks\Database\Seeders\BookmarksSeeder;
use App\Domain\Core\Seeders\CoreSeeder;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CoreSeeder::class,
            BookmarksSeeder::class,
            '\App\Database\Seeders\AdminHomepageSeeder',
        ]);
    }
}
