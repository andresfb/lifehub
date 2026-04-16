<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class SearchGlobalSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:1', 'max:2000'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'module' => ['sometimes', 'string', 'max:255'],
            'entity_type' => ['sometimes', 'string', 'max:255'],
            'is_private' => ['sometimes', 'boolean'],
            'is_archived' => ['sometimes', 'boolean'],
        ];
    }
}
