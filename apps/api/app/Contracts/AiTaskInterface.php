<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Dtos\AI\ProviderItem;
use Laravel\Ai\Responses\AgentResponse;

interface AiTaskInterface
{
    public function setModel(mixed $model): self;

    public function handle(): AgentResponse;

    public function getAiProvider(): ProviderItem;

    public function setToScreen(bool $toScreen): self;
}
