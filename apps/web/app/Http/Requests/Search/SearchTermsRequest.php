<?php

declare(strict_types=1);

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

final class SearchTermsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules Method.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'module' => ['required', 'string'],
            'type' => ['required', 'string'],
            'query' => ['required', 'string', 'min:2'],
        ];
    }
}
