<?php

declare(strict_types=1);

namespace App\Repository\Auth\Dtos;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;

final class User extends Data implements Authenticatable
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $two_factor_enabled = false,
        public readonly bool $is_admin = false,
        public ?string $remember_token = null,
    ) {}

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): string
    {
        return (string) $this->id;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): ?string
    {
        return null;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
