<?php

namespace App\Repository\Auth\Dtos;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;

class User extends Data implements Authenticatable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $isAdmin =false,
        protected ?string $rememberToken = null,
    ) {}

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): string
    {
        return $this->id;
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
        return $this->rememberToken;
    }

    public function setRememberToken($value): void
    {
        $this->rememberToken = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
