<?php

declare(strict_types=1);

namespace App\Repository\Api\Libraries;

use App\Repository\Api\Dtos\ApiConfigItem;
use App\Repository\Auth\Libraries\AuthSession;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use LifeHub\ApiClient\Api\AuthApi;
use LifeHub\ApiClient\Api\ManifestApi;
use LifeHub\ApiClient\Api\PinApi;
use LifeHub\ApiClient\Api\SearchHistoryApi;
use LifeHub\ApiClient\Api\SearchProviderApi;
use LifeHub\ApiClient\Api\SearchTagApi;
use LifeHub\ApiClient\Configuration;

final class ApiLibrary
{
    public static function authApi(?string $token = null): AuthApi
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost(Config::string('services.backend.base_url'))
            ->setUserAgent(Config::string('services.backend.user_agent'));

        $headers = [];
        if (filled($token)) {
            $headers['headers']['Authorization'] = "Bearer {$token}";
        }

        return new AuthApi(
            new Client($headers),
            $config,
        );
    }

    public static function pinApi(int $userId): PinApi
    {
        $configItem = self::getBaseConfiguration($userId);

        return new PinApi(
            new Client($configItem->headers),
            $configItem->config,
        );
    }

    public static function searchProviderApi(int $userId): SearchProviderApi
    {
        $configItem = self::getBaseConfiguration($userId);

        return new SearchProviderApi(
            new Client($configItem->headers),
            $configItem->config,
        );
    }

    public static function searchHistoryApi(int $userId): SearchHistoryApi
    {
        $configItem = self::getBaseConfiguration($userId);

        return new SearchHistoryApi(
            new Client($configItem->headers),
            $configItem->config,
        );
    }

    public static function manifestApi(int $userId): ManifestApi
    {
        $configItem = self::getBaseConfiguration($userId);

        return new ManifestApi(
            new Client($configItem->headers),
            $configItem->config,
        );
    }

    public static function searchTagApi(int $userId): SearchTagApi
    {
        $configItem = self::getBaseConfiguration($userId);

        return new SearchTagApi(
            new Client($configItem->headers),
            $configItem->config,
        );
    }

    private static function getBaseConfiguration(int $userId): ApiConfigItem
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost(Config::string('services.backend.base_url'))
            ->setUserAgent(Config::string('services.backend.user_agent'));

        $token = AuthSession::getUserToken($userId);
        $headers['headers']['Authorization'] = "Bearer {$token}";

        return new ApiConfigItem($config, $headers);
    }
}
