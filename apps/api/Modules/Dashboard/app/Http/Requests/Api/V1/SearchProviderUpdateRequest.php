<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Dashboard\Models\SearchProvider;

final class SearchProviderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', SearchProvider::class);
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
            'order' => ['required', 'integer'],
        ];
    }
}
