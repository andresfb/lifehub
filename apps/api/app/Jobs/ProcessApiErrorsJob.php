<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Dtos\AI\ApiErrorItem;
use App\Services\Api\ProcessApiErrorsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class ProcessApiErrorsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly Collection|ApiErrorItem $errors)
    {
        $this->delay = now()->addSeconds(5);
    }

    public function handle(ProcessApiErrorsService $service): void
    {
        try {
            $service->execute($this->errors);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
