<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invitation;
use Random\RandomException;

final class CreateInvitationAction
{
    /**
     * @throws RandomException
     */
    public function execute(string $email): Invitation
    {
        return Invitation::query()->create([
            'email' => $email,
            'token' => bin2hex(random_bytes(32)),
            'expires_at' => now()->addDays(7),
        ]);
    }
}
