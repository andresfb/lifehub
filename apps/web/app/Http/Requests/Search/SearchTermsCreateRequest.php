<?php

declare(strict_types=1);

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

final class SearchTermsCreateRequest extends FormRequest
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
        return [
            'module' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'term' => ['required', 'string', 'min:2'],
        ];
    }
}
