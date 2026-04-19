<?php

namespace App\Repository\Common\Libraries;

use App\Repository\Auth\Libraries\AuthSession;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Promises\LazyPromise;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ApiClient
{
    private string $token = '';

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

        return $this->parseResponse($response);
    }

    /**
     * @throws Exception
     */
    public function post(string $uri, array $data = []): array
    {
        $response = $this->client()->post($uri, $data);

        return $this->parseResponse($response);
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    private function getToken(): string
    {
        if (filled($this->token)) {
            $this->token = AuthSession::get('api_token');
        }

        return $this->token;
    }

    private function parseResponse(LazyPromise|PromiseInterface|Response $response): array
    {
        if ($response->unauthorized()) {
            AuthSession::forget(['api_token', 'auth_user']);
        }

        if ($response->failed()) {
            throw new RuntimeException("Request failed: {$response->status()}");
        }

        $result = $response->json();

        if ($result['success'] === false) {
            throw new RuntimeException("Request failed: {$result['message']}");
        }

        $this->statusCode = $response->getStatusCode();

        return array_key_exists('data', $result)
            ? $result['data']
            : $result;
    }
}
