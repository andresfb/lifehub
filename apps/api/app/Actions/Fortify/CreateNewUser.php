<?php

namespace App\Actions\Fortify;

use App\Models\Invitation;
use App\Models\User;
use App\Traits\ModulesAssignable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Throwable;

class CreateNewUser implements CreatesNewUsers
{
    use ModulesAssignable;
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws Throwable
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'invitation' => ['required', 'string'],
        ])->validate();

        $invitation = Invitation::query()
            ->valid()
            ->forToken($input['invitation'])
            ->firstOr(fn () => throw ValidationException::withMessages([
                'invitation' => __('This invitation is invalid or has expired.'),
            ]));

        if (! hash_equals($invitation->email, $input['email'])) {
            throw ValidationException::withMessages([
                'email' => __('This email address does not match the invitation.'),
            ]);
        }

        return DB::transaction(function () use ($input, $invitation) {
            $user = User::query()
                ->create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => $input['password'],
                ]);

            $this->assignDefaultModules($user);

            $invitation->update(['accepted_at' => now()]);

            return $user;
        });
    }
}
