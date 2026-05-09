<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Dashboard\Models\HomepageItem;

final class PinUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', HomepageItem::class);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'section_slug' => ['required', 'exists:dashboard_homepage_sections,slug', 'max:255'],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'url' => ['required', 'url'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:10'],
            'icon_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/', 'max:20'],
            'order' => ['required', 'int'],
            'active' => ['required', 'boolean'],
            'tags' => ['nullable', 'array'],
        ];
    }
}
