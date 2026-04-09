<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow if user is authenticated and the ID matches
        return $this->user() && (int) $this->route('id') === $this->user()->id;
    }

    public function rules(): array
    {
        return [];
    }
}
