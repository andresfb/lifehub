<?php

declare(strict_types=1);

namespace App\Traits;

use App\Factories\ProviderFactory;
use Modules\Core\Dtos\AI\ProviderItem;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

trait AiTaskable
{
    public function getAiProvider(): ProviderItem
    {
        if ($this->toScreen) {
            return $this->askForProvider();
        }

        return ProviderFactory::getRandom();
    }

    private function askForProvider(): ProviderItem
    {
        if (confirm('Random AI Provider?')) {
            return ProviderFactory::getRandom();
        }

        $selection = select(
            label: 'Select a Provider',
            options: ProviderFactory::getList(),
        );

        return ProviderFactory::getProvider($selection);
    }
}
