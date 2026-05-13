<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

final class SearchProviderUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'url' => ['required', 'string', 'url', 'max:2000'],
            'term_field' => ['required', 'string', 'max:10'],
            'icon' => ['nullable', 'string', 'max:10'],
            'icon_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/', 'max:20'],
            'default' => ['boolean'],
            'active' => ['boolean'],
            'order' => ['required', 'int'],
        ];
    }
}
