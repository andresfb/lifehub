<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

final class PinCreateRequest extends FormRequest
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
            'section_slug' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'url' => ['required', 'url'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:10'],
            'icon_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/', 'max:20'],
            'tags' => ['nullable', 'array'],
        ];
    }
}
