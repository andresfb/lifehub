<?php

declare(strict_types=1);

namespace App\Repository\Api\Dtos;

use LifeHub\ApiClient\Configuration;
use Spatie\LaravelData\Dto;

final class ApiConfigItem extends Dto
{
    /**
     * @param  array<string, mixed>  $headers
     */
    public function __construct(
        public readonly Configuration $config,
        public readonly array $headers,
    ) {}
}
