<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\NewUserItem;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final class CreateAdminAction
{
    /**
     * @throws Throwable
     */
    public function handle(NewUserItem $input): string
    {
        return DB::transaction(static function () use ($input): string {
            $user = User::query()->create([
                'name' => $input->name,
                'email' => $input->email,
                'password' => Hash::make($input->password),
            ]);

            $user->email_verified_at = now();
            $user->is_admin = base64_encode(
                hash_hmac('sha256', (string) $user->id, Config::string('app.key'))
            );
            $user->save();

            return $user->createToken('auth-token')->plainTextToken;
        });
    }
}
