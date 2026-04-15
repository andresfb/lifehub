<?php

namespace App\Repository\Common\Libraries;

use App\Repository\Auth\Libraries\AuthSession;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ApiClient
{
    private string $token;

    public int $statusCode = 0 {
        get {
            return $this->statusCode;
        }
    }

    protected function client(): PendingRequest
    {
        $client = Http::baseUrl(Config::string('services.backend.url'))
            ->acceptJson()
            ->asJson()
            ->timeout(10);

        return $client->withToken(
            $this->getToken()
        );
    }

    /**
     * @throws Exception
     */
    public function get(string $uri, array $query = []): array
    {
        $response = $this->client()->get($uri, $query);

        if ($response->unauthorized()) {
            AuthSession::forget(['api_token', 'auth_user']);
        }

        if ($response->failed()) {
            throw new RuntimeException("GET {$uri} failed: {$response->status()}");
        }

        $this->statusCode = $response->getStatusCode();

        return $response->json();
    }

    /**
     * @throws Exception
     */
    public function post(string $uri, array $data = []): array
    {
        $response = $this->client()->post($uri, $data);

        if ($response->unauthorized()) {
            AuthSession::forget(['api_token', 'auth_user']);
        }

        if ($response->failed()) {
            throw new RuntimeException("POST {$uri} failed: {$response->status()}");
        }

        $this->statusCode = $response->getStatusCode();

        return $response->json();
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    private function getToken(): string
    {
        if (blank($this->token)) {
            $this->token = AuthSession::get('api_token');
        }

        return $this->token;
    }
}
