<?php

declare(strict_types=1);

namespace App\Dtos\Profile;

use Spatie\LaravelData\Data;

final class NewUserItem extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}
}
