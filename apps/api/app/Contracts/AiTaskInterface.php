<?php

declare(strict_types=1);

namespace App\Contracts;

use Laravel\Ai\Responses\AgentResponse;
use Modules\Core\Dtos\AI\ProviderItem;

interface AiTaskInterface
{
    public function setModel(mixed $model): self;

    public function handle(): AgentResponse;

    public function getAiProvider(): ProviderItem;

    public function setToScreen(bool $toScreen): self;
}
