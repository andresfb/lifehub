<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Fortify;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $required = 'nullable';
        if (Fortify::confirmsTwoFactorAuthentication()) {
            $required = 'required';
        }

        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
            'two_factor_code' => [$required, 'string'],
        ];
    }
}
