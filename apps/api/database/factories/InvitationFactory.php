<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<Invitation>
 */
final class InvitationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'token' => bin2hex(random_bytes(32)),
            'expires_at' => now()->addDays(7),
        ];
    }

    public function expired(): self
    {
        return $this->state([
            'expires_at' => now()->subDay(),
        ]);
    }

    public function accepted(): self
    {
        return $this->state([
            'accepted_at' => now(),
        ]);
    }
}
