<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Manifest\ManifestService;
use Illuminate\Console\Command;

final class ManifestClearCommand extends Command
{
    protected $signature = 'manifest:clear';

    protected $description = 'Clear the cached app manifest';

    public function handle(): int
    {
        ManifestService::invalidateCache();

        $this->info('App manifest cache cleared.');

        return self::SUCCESS;
    }
}
