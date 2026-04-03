<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\Profile\NewUserItem;
use App\Models\User;
use App\Traits\AdminHashable;
use App\Traits\ModulesAssignable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

use function Laravel\Prompts\warning;

final class CreateAdminAction
{
    use AdminHashable;
    use ModulesAssignable;

    /**
     * @throws Throwable
     */
    public function handle(NewUserItem $input): string
    {
        return DB::transaction(function () use ($input): string {
            $user = User::query()
                ->create([
                    'name' => $input->name,
                    'email' => $input->email,
                    'password' => Hash::make($input->password),
                ]);

            $this->assignModulesToAdmin($user);

            $user->email_verified_at = now();
            $user->admin_hash = $this->hash($user->id);
            $user->save();

            info('User created');
            warning("User ID: {$user->id}");

            return $user->createToken('auth-token')->plainTextToken;
        });
    }
}
