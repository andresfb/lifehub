<?php

declare(strict_types=1);

namespace App\Factories;

use Illuminate\Support\Facades\Config;
use Laravel\Ai\Enums\Lab;
use Modules\Core\Dtos\AI\ProviderItem;

final class ProviderFactory
{
    public static function getProvider(string $code): ProviderItem
    {
        $providers = Config::collection('settings.providers');
        $provider = $providers->where('enabled', true)
            ->where('code', $code)
            ->firstOrFail();

        $lab = Lab::from($provider['code']);

        return new ProviderItem(
            lab: $lab,
            model: (string) collect($provider['models'])->random(),
        );
    }

    public static function getRandom(): ProviderItem
    {
        $providers = Config::collection('settings.providers');
        $provider = $providers->where('enabled', true)->random();
        $lab = Lab::from($provider['code']);

        return new ProviderItem(
            lab: $lab,
            model: (string) collect($provider['models'])->random(),
        );
    }

    public static function getWebRandom(): ProviderItem
    {
        $providers = Config::collection('settings.providers');
        $provider = $providers->where('enabled', true)
            ->where('can_web', true)
            ->random();

        $lab = Lab::from($provider['code']);

        return new ProviderItem(
            lab: $lab,
            model: (string) collect($provider['models'])->random(),
        );
    }

    public static function getList(): array
    {
        $providers = Config::collection('settings.providers');

        return $providers->where('enabled', true)
            ->sortBy('name')
            ->pluck('name', 'code')
            ->toArray();
    }
}
