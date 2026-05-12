<?php

declare(strict_types=1);

namespace App\Repository\Auth\Services;

use App\Libraries\ApiLibrary;
use App\Libraries\CacheLibrary;
use App\Models\User as UserModel;
use App\Repository\Auth\Dtos\RegisterItem;
use App\Repository\Auth\Dtos\User;
use App\Repository\Auth\Enums\AuthStatus;
use App\Repository\Auth\Libraries\AuthSession;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use LifeHub\ApiClient\Model\InlineObject;
use LifeHub\ApiClient\Model\LoginRequest;
use LifeHub\ApiClient\Model\RegisterRequest;
use LifeHub\ApiClient\Model\TwoFactorCodeRequest;
use LifeHub\ApiClient\Model\V1Login203Response;
use LifeHub\ApiClient\Model\V1Login401Response;
use LifeHub\ApiClient\Model\V1Register200Response;
use RuntimeException;

final readonly class ApiAuthService
{
    /**
     * @throws Exception
     */
    public function login(string $email, string $password): AuthStatus
    {
        try {
            $request = new LoginRequest()
                ->setEmail($email)
                ->setPassword($password)
                ->setDevice(
                    str(Config::string('app.name'))
                        ->slug()
                        ->value()
                );

            $response = ApiLibrary::authApi()->v1Login($request);

            if ($response instanceof V1Login401Response || $response instanceof InlineObject) {
                throw new RuntimeException($response->getErrors() ?: $response->getMessage());
            }

            if ($response instanceof V1Login203Response) {
                session()->flash('message', 'Two Factor Authentication Required');
                session()->flash('tfa-ttl', $response->getTtl());

                AuthSession::put('login.email', $email);

                return AuthStatus::TWO_FACTOR;
            }

            $this->saveAuthInfo($response);

            return AuthStatus::SUCCESS;
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            Log::error($e->getMessage(), $e->getTrace());

            return AuthStatus::FAILURE;
        }
    }

    /**
     * @throws Exception
     */
    public function validateTwoFactorCode(string $email, string $code): AuthStatus
    {
        try {
            $request = new TwoFactorCodeRequest()
                ->setEmail($email)
                ->setTwoFactorCode($code);

            $response = ApiLibrary::authApi()->v1LoginValidate($request);

            if ($response instanceof V1Login401Response || $response instanceof InlineObject) {
                throw new RuntimeException($response->getErrors() ?: $response->getMessage());
            }

            AuthSession::forget('login.email');
            $this->saveAuthInfo($response);

            return AuthStatus::SUCCESS;
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            Log::error($e->getMessage());

            return AuthStatus::FAILURE;
        }
    }

    public function logout(): void
    {
        $token = AuthSession::get('api_token');
        $user = AuthSession::get('auth_user');

        AuthSession::forget(['api_token', 'auth_user']);

        session()->invalidate();
        session()->regenerateToken();

        CacheLibrary::clearCache($user);

        Concurrency::defer([
            function () use ($token) {
                try {
                    ApiLibrary::authApi($token)->v1Logout();
                } catch (Exception $e) {
                    Log::error($e->getMessage(), $e->getTrace());
                }
            },
        ]);
    }

    /**
     * @throws Exception
     */
    public function me(string $token): User
    {
        if (AuthSession::has('auth_user')) {
            $user = AuthSession::get('auth_user', []);

            if (filled($user)) {
                return User::from($user);
            }
        }

        $response = ApiLibrary::authApi($token)->v1Me();
        [$user] = $this->loadResponseUser($response);
        AuthSession::put('auth_user', $user->toArray());

        return $user;
    }

    public function register(RegisterItem $item): AuthStatus
    {
        try {
            $request = new RegisterRequest()
                ->setName($item->name)
                ->setEmail($item->email)
                ->setPassword($item->password)
                ->setPasswordConfirmation($item->password_confirmation)
                ->setInvitation($item->invitation);

            $response = ApiLibrary::authApi()->v1Register($request);

            if ($response instanceof InlineObject) {
                throw new RuntimeException($response->getErrors() ?: $response->getMessage());
            }

            $this->saveAuthInfo($response);

            return AuthStatus::SUCCESS;
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            Log::error($e->getMessage());

            return AuthStatus::FAILURE;
        }
    }

    private function saveAuthInfo(V1Register200Response $response): void
    {
        [$user, $token] = $this->loadResponseUser($response);

        session()->regenerate();

        AuthSession::put('api_token', $token);
        AuthSession::put('auth_user', $user->toArray());
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

    /**
     * @return array<int, mixed>
     */
    private function loadResponseUser(V1Register200Response $response): array
    {
        if ($response->isNullableSetToNull('data')) {
            throw ValidationException::withMessages([
                'email' => ['The authentication response was invalid.'],
            ]);
        }

        $payload = $response->getData();
        if ($payload->isNullableSetToNull('attributes')) {
            throw ValidationException::withMessages([
                'email' => ['The authentication response was invalid.'],
            ]);
        }

        $token = $payload->getAttributes()->getAccessToken();
        $userData = $payload->getAttributes();

        $user = new User(
            id: (int) $payload->getId(),
            name: $userData->getName(),
            email: $userData->getEmail(),
            two_factor_enabled: $userData->getTwoFactorEnabled(),
            is_admin: $userData->getIsAdmin(),
            remember_token: $userData->getRememberToken(),
        );

        return [$user, $token];
    }
}
