<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class NewUserItem extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}
}
