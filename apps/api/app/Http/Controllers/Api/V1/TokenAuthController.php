<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\TokenAuthRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

final class TokenAuthController extends ApiController
{
    public function store(TokenAuthRequest $request): JsonResponse
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

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Token created successfully');
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Token revoked successfully');
    }

    private function validateTwoFactorCode(User $user, string $code): bool
    {
        $provider = app(TwoFactorAuthenticationProvider::class);
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
