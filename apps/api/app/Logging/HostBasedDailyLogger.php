<?php

declare(strict_types=1);

namespace App\Logging;

use Monolog\Handler\RotatingFileHandler;

final class HostBasedDailyLogger
{
    public function __invoke($logger): void
    {
        $hostname = str(gethostname())->kebab()->value();
        $template = "laravel-{$hostname}-{date}";

        foreach ($logger->getHandlers() as $handler) {
            if (! $handler instanceof RotatingFileHandler) {
                continue;
            }

            $handler->setFilenameFormat($template, 'Y-m-d');
        }
    }
}
