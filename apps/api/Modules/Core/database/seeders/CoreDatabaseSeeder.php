<?php

declare(strict_types=1);

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

final class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @noinspection ClassConstantCanBeUsedInspection
     */
    public function run(): void
    {
         $this->call([
             '\Modules\Core\Database\Seeders\AiProvidersSeeder',
         ]);
    }
}
