<?php

namespace App\Repository\Auth\Services;

use App\Repository\Auth\Dtos\User;
use App\Repository\Auth\Enums\LoginStatus;
use App\Repository\Auth\Libraries\AuthSession;
use App\Repository\Common\Libraries\ApiClient;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

readonly class ApiAuthService
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    /**
     * @throws Exception
     */
    public function login(string $email, string $password): LoginStatus
    {
        $payload = $this->apiClient->post(
            Config::string('services.backend.endpoints.auth.login'),
            [
                'email' => $email,
                'password' => $password,
            ]
        );

        if ($this->needsTwoFactor($payload)) {
            session()->flash('Two Factor Authentication Required');
            AuthSession::put('login.id', $payload['user']['id']);

            return LoginStatus::TWO_FACTOR;
        }

        $this->saveAuthInfo($payload);

        return LoginStatus::SUCCESS;
    }

    /**
     * @throws Exception
     */
    public function logout(): void
    {
        $this->apiClient->post(
            Config::string('services.backend.endpoints.auth.logout'),
        );

        AuthSession::forget(['api_token', 'auth_user']);

        session()->invalidate();
        session()->regenerateToken();
        Auth::guard('web')->logout();
    }

    /**
     * @throws Exception
     */
    public function me(string $token): ?User
    {
        if (AuthSession::has('auth_user')) {
            $user = AuthSession::get('auth_user', []);

            if (filled($user)) {
                return User::from($user);
            }
        }

        $response = $this->apiClient
            ->setToken($token)
            ->get(
                Config::string('services.backend.endpoints.auth.user'),
            );

        AuthSession::put('auth_user', $response);

        return User::from($response);
    }

    private function needsTwoFactor(array $payload): bool
    {
        if ($this->apiClient->statusCode !== Response::HTTP_NON_AUTHORITATIVE_INFORMATION) {
            return false;
        }

        if (! array_key_exists('two_factor', $payload)) {
            return false;
        }

        return (bool) $payload['two_factor'];
    }

    private function saveAuthInfo(array $payload): void
    {
        $token = $payload['token'] ?? null;
        $user = $payload['user'] ?? null;

        if (! $token || ! is_array($user)) {
            throw ValidationException::withMessages([
                'email' => ['The authentication response was invalid.'],
            ]);
        }

        session()->regenerate();

        AuthSession::put('api_token', $token);
        AuthSession::put('auth_user', $user);

        Auth::setUser(
            User::from($user)
        );
    }
}
