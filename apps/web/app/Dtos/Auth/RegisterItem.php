<?php

namespace App\Dtos\Auth;

use Spatie\LaravelData\Data;

class RegisterItem extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $password_confirmation,
        public readonly string $invitation,
    ){}
}
