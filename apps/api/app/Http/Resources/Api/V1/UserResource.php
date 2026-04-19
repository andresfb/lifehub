<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Override;

/**
 * @mixin User
 */
final class UserResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->isAdmin(),
            'remember_token' => $this->remember_token,
            'two_factor_enabled' => ! is_null($this->two_factor_confirmed_at),
            'email_verified_at' => $this->email_verified_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
