<?php

declare(strict_types=1);

namespace App\Repository\Common\Libraries;

use App\Models\User;
use App\Repository\Auth\Libraries\AuthSession;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Promises\LazyPromise;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class ApiClient
{
    public int $statusCode = 0 {
        get {
            return $this->statusCode;
        }
    }

    private ?int $userId = null;

    private string $token = '';

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function get(string $uri, array $query = []): array
    {
        $response = $this->client()->get($uri, $query);

        return $this->parseResponse($response);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function request(string $method, string $uri, array $data = []): array
    {
        return match (mb_strtoupper($method)) {
            'GET' => $this->get($uri, $data),
            'POST' => $this->post($uri, $data),
            default => throw new RuntimeException("Unsupported method: {$method}"),
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function post(string $uri, array $data = []): array
    {
        $response = $this->client()->post($uri, $data);

        return $this->parseResponse($response);
    }

    private function client(): PendingRequest
    {
        $client = Http::baseUrl(Config::string('services.backend.url'))
            ->acceptJson()
            ->asJson()
            ->timeout(10);

        return $client->withToken(
            $this->getToken()
        );
    }

    private function getToken(): string
    {
        if (filled($this->token)) {
            return $this->token;
        }

        $this->token = AuthSession::get('api_token', '');

        if (blank($this->token) && filled($this->userId)) {
            $this->token = User::getToken($this->userId);
        }

        return $this->token;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseResponse(LazyPromise|PromiseInterface|Response $response): array
    {
        if ($response->unauthorized()) {
            AuthSession::forget(['api_token', 'auth_user']);
        }

        if ($response->failed()) {
            throw new RuntimeException("Request failed: {$response->status()}");
        }

        if ($response->noContent()) {
            return [];
        }

        $result = $response->json();

        if ($result['success'] === false) {
            throw new RuntimeException("Request failed: {$result['message']}");
        }

        $this->statusCode = $response->status();

        return array_key_exists('data', $result)
            ? $result['data']
            : $result;
    }
}
