<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class PinListRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'include' => ['nullable', 'string'],
            'status' => ['required', 'integer', 'in:-1,0,1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('status')) {
            $this->merge(['status' => 1]);
        }
    }
}
