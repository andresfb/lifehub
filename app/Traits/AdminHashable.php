<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait AdminHashable
{
    private function isHashValid(int $userId, string $hash): bool
    {
        return hash_equals(
            base64_decode($this->hash($userId)),
            base64_decode($hash)
        );
    }

    private function hash(int $userId): string
    {
        return base64_encode(
            hash_hmac('sha256', (string) $userId, $this->rehashKey())
        );
    }

    private function rehashKey(): string
    {
        $appKey = str(Config::string('app.key'))
            ->explode(':')
            ->last();

        $encoded = base64_encode(
            hash('ripemd256', base64_decode($appKey), true)
        );

        return "base64:ripemd256:{$encoded}";
    }
}
