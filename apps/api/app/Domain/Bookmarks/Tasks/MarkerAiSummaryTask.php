<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Tasks;

use App\Contracts\AiTaskInterface;
use App\Domain\Bookmarks\Ai\Agents\MarkerAiSummaryAgent;
use App\Domain\Bookmarks\Models\Marker;
use App\Dtos\AI\ProviderItem;
use App\Factories\ProviderFactory;
use App\Traits\AiTaskable;
use App\Traits\Screenable;
use Laravel\Ai\Responses\AgentResponse;

final class MarkerAiSummaryTask implements AiTaskInterface
{
    use AiTaskable;
    use Screenable;

    private Marker $marker;

    /**
     * @param  Marker  $model
     */
    public function setModel(mixed $model): AiTaskInterface
    {
        $this->marker = $model;

        return $this;
    }

    public function handle(): AgentResponse
    {
        $provider = $this->getAiProvider();

        $this->notice("Using {$provider->lab->name} with model: {$provider->model}");

        return MarkerAiSummaryAgent::make()
            ->prompt(
                prompt: $this->marker->url,
                provider: $provider->lab,
                model: $provider->model,
                timeout: 120 // 2 minutes
            );
    }

    public function getAiProvider(): ProviderItem
    {
        return ProviderFactory::getWebRandom();
    }
}
