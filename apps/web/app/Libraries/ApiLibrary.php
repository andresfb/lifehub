<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Repository\Auth\Libraries\AuthSession;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use LifeHub\ApiClient\Api\AuthApi;
use LifeHub\ApiClient\Api\PinApi;
use LifeHub\ApiClient\Api\SearchProviderApi;
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
        $config = Configuration::getDefaultConfiguration()
            ->setHost(Config::string('services.backend.base_url'))
            ->setUserAgent(Config::string('services.backend.user_agent'));

        $token = AuthSession::getUserToken($userId);
        $headers['headers']['Authorization'] = "Bearer {$token}";

        return new PinApi(
            new Client($headers),
            $config,
        );
    }

    public static function searchProviderApi(int $userId): SearchProviderApi
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost(Config::string('services.backend.base_url'))
            ->setUserAgent(Config::string('services.backend.user_agent'));

        $token = AuthSession::getUserToken($userId);
        $headers['headers']['Authorization'] = "Bearer {$token}";

        return new SearchProviderApi(
            new Client($headers),
            $config,
        );
    }
}
