<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\TwoFactorCodeRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AuthController extends ApiController
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->with('roles')
            ->where('email', $request->safe()->string('email')->lower()->toString())
            ->first();

        if (! $user || ! Hash::check($request->safe()->string('password')->toString(), $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        if ($user->hasEnabledTwoFactorAuthentication()) {
            $expire = 60 * 5; // 5 minutes

            Cache::put(
                md5(sprintf("%s:%s", $user->id, $user->email)),
                $request->safe()->string('device')->toString(),
                $expire,
            );

            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication required',
                'two_factor' => true,
                'ttl' => $expire,
            ], Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }

        return $this->authorized(
            $user,
            $request->safe()->string('device')->toString()
        );
    }

    public function validateTwoFactorCode(TwoFactorCodeRequest $request): JsonResponse
    {
        $user = User::query()
            ->with('roles')
            ->where('email', $request->safe()->string('email')->lower()->toString())
            ->first();

        $code = $request->safe()->string('two_factor_code')->toString();

        $key = md5(sprintf("%s:%s", $user->id, $user->email));
        if (! Cache::has($key)) {
            return $this->unauthorized('Two Factor Validation Expired');
        }

        $device = Cache::get($key, '');

        $provider = resolve(TwoFactorAuthenticationProvider::class);
        $secret = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);

        if ($provider->verify($secret, $code)) {
            return $this->authorized($user, $device);
        }

        $recoveryCodes = $user->recoveryCodes();

        if (in_array($code, $recoveryCodes, strict: true)) {
            $user->replaceRecoveryCode($code);

            return $this->authorized($user, $device);
        }

        return $this->unauthorized('Invalid two-factor authentication code');
    }

    public function logout(Request $request): JsonResponse
    {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Token revoked successfully');
    }

    public function me(Request $request): JsonResponse
    {
        $userResource = new UserResource($request->user());

        return $this->success($userResource->resolveResourceData($request));
    }

    /**
     * @throws Throwable
     */
    public function register(RegisterRequest $request, CreateNewUser $action): JsonResponse
    {
        $user = $action->create($request->validated());

        $user->sendEmailVerificationNotification();

        return $this->authorized(
            user: $user,
            device: 'auth-token',
            message: 'User registered successfully. Please check your email to verify your account'
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->safe()->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(message: 'Password reset link sent to your email');
        }

        return $this->error('Unable to send reset link', 500);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->safe()->only('email', 'password', 'password_confirmation', 'token'),
            static function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(message: 'Password reset successfully');
        }

        return $this->error(
            match ($status) {
                Password::INVALID_TOKEN => 'Invalid or expired reset token',
                Password::INVALID_USER => 'User not found',
                default => 'Unable to reset password',
            },
            400
        );
    }

    private function authorized(
        User $user,
        string $device,
        string $message = 'Login successfully'
    ): JsonResponse
    {
        $user->tokens()
            ->where('name', $device)
            ->delete();

        $token = $user->createToken($device)->plainTextToken;
        $userResource = new UserResource($user);

        return $this->created([
            'user' => $userResource->jsonSerialize(),
            'token' => $token,
        ], $message);
    }
}
