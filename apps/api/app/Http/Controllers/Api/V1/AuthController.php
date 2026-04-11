<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResendVerificationRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->where('email', $request->string('email')->lower()->toString())
            ->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        if ($user->hasEnabledTwoFactorAuthentication()) {
            $code = $request->string('two_factor_code')->toString();

            if ($code === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Two-factor authentication required',
                    'two_factor' => true,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (! $this->validateTwoFactorCode($user, $code)) {
                return $this->unauthorized('Invalid two-factor authentication code');
            }
        }

        $token = $user->createToken($request->string('device_name')->toString())->plainTextToken;
        $userResource = new UserResource($user);

        return $this->created([
            'user' => $userResource->resolveResourceData($request),
            'token' => $token,
        ], 'Token created successfully');
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

        $token = $user->createToken('auth-token')->plainTextToken;
        $userResource = new UserResource($user);

        return $this->created([
            'user' => $userResource->resolveResourceData($request),
            'token' => $token,
        ], 'User registered successfully. Please check your email to verify your account.');
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->success(message: 'Email already verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->success(message: 'Email verified successfully');
    }

    public function resendVerificationEmail(ResendVerificationRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->email)->first();

        if (! $user) {
            return $this->notFound('User not found');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->success(message: 'Verification email sent successfully');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(message: 'Password reset link sent to your email');
        }

        return $this->error('Unable to send reset link', 500);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
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

    private function validateTwoFactorCode(User $user, string $code): bool
    {
        $provider = resolve(TwoFactorAuthenticationProvider::class);
        $secret = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);

        if ($provider->verify($secret, $code)) {
            return true;
        }

        $recoveryCodes = $user->recoveryCodes();

        if (in_array($code, $recoveryCodes, strict: true)) {
            $user->replaceRecoveryCode($code);

            return true;
        }

        return false;
    }
}
