<?php

declare(strict_types=1);

namespace App\Repository\Auth\Services;

use App\Dtos\Auth\RegisterItem;
use App\Models\User as UserModel;
use App\Repository\Auth\Dtos\User;
use App\Repository\Auth\Enums\AuthStatus;
use App\Repository\Auth\Libraries\AuthSession;
use App\Repository\Common\Libraries\ApiClient;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final readonly class ApiAuthService
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    /**
     * @throws Exception
     */
    public function login(string $email, string $password): AuthStatus
    {
        try {
            $payload = $this->apiClient->post(
                uri: Config::string('services.backend.endpoints.auth.login'),
                data: [
                    'email' => $email,
                    'password' => $password,
                    'device' => str(Config::string('app.name'))
                        ->slug()
                        ->value(),
                ]
            );
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            Log::error($e->getMessage());

            return AuthStatus::FAILURE;
        }

        if ($this->needsTwoFactor($payload)) {
            session()->flash('message', 'Two Factor Authentication Required');
            session()->flash('tfa-ttl', $payload['ttl']);

            AuthSession::put('login.email', $email);

            return AuthStatus::TWO_FACTOR;
        }

        $this->saveAuthInfo($payload);

        return AuthStatus::SUCCESS;
    }

    /**
     * @throws Exception
     */
    public function validateTwoFactorCode(string $email, string $code): AuthStatus
    {
        try {
            $payload = $this->apiClient->post(
                uri: Config::string('services.backend.endpoints.auth.validate'),
                data: [
                    'email' => $email,
                    'code' => $code,
                ]
            );
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            Log::error($e->getMessage());

            return AuthStatus::FAILURE;
        }

        AuthSession::forget('login.email');
        $this->saveAuthInfo($payload);

        return AuthStatus::SUCCESS;
    }

    public function logout(): void
    {
        $token = AuthSession::get('api_token');

        AuthSession::forget(['api_token', 'auth_user']);
        session()->invalidate();
        session()->regenerateToken();

        Concurrency::defer([
            function () use ($token) {
                try {
                    $this->apiClient->setToken($token)
                        ->post(
                            Config::string('services.backend.endpoints.auth.logout'),
                        );
                } catch (Exception $e) {
                    Log::error($e->getMessage(), $e->getTrace());
                }
            },
        ]);
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

    public function register(RegisterItem $item): AuthStatus
    {
        try {
            $payload = $this->apiClient->post(
                uri: Config::string('services.backend.endpoints.auth.register'),
                data: [
                    'name' => $item->name,
                    'email' => $item->email,
                    'password' => $item->password,
                    'password_confirmation' => $item->password_confirmation,
                    'invitation' => $item->invitation,
                ]
            );
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            Log::error($e->getMessage());

            return AuthStatus::FAILURE;
        }

        $this->saveAuthInfo($payload);

        return AuthStatus::SUCCESS;
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

        $user = User::from($user);
        Auth::setUser($user);

        UserModel::query()
            ->updateOrCreate([
                'id' => $user->id,
            ], [
                'name' => $user->name,
                'email' => $user->email,
                'two_factor_enabled' => $user->two_factor_enabled,
                'token_hash' => AuthSession::getTokenHash($token),
                'api_token' => $token,
                'is_admin' => $user->is_admin,
                'remember_token' => $user->getRememberToken(),
                'password' => "{$user->email}:{$token}",
            ]);
    }
}
