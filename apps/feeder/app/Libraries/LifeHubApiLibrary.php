<?php

namespace App\Libraries;

use App\Dtos\LifeHubApiEndpoint;
use App\Dtos\LifeHubApiResponse;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Spatie\LaravelData\Data;

class LifeHubApiLibrary
{
    /**
     * @throws Exception
     */
    public static function post(
        LifeHubApiEndpoint $endpoint,
        Data $requestItem,
    ): LifeHubApiResponse
    {
        $url = sprintf(
            Config::string('lifehub-api.base_uri'),
            $endpoint->value,
        );

        $response = Http::withToken(Config::string('lifehub-api.token'))
            ->connectTimeout(30)
            ->timeout(60)
            ->acceptJson()
            ->contentType('application/json')
            ->withBody(
                json_encode($requestItem->toArray(), JSON_THROW_ON_ERROR|JSON_INVALID_UTF8_IGNORE)
            )
            ->post($url)
            ->throw()
            ->object();

        if ($response === null) {
            throw new RuntimeException('Failed to get response from Prompter API');
        }

        return LifeHubApiResponse::from($response);
    }
}
