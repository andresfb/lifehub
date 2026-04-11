<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\Profile\NewUserItem;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final readonly class CreateAdminAction
{
    public function __construct(
        private ModuleAccessService $moduleAccess
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(NewUserItem $input): User
    {
        return DB::transaction(function () use ($input): User {
            $user = User::query()
                ->create([
                    'name' => $input->name,
                    'email' => $input->email,
                    'password' => Hash::make($input->password),
                ]);

            $user->email_verified_at = now();
            $user->save();

            $this->moduleAccess->grantSuperAdmin($user);

            return $user;
        });
    }
}
