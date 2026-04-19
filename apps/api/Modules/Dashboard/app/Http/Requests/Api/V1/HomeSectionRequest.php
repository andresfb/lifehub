<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class HomeSectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'include' => ['nullable', 'string'],
        ];
    }
}
