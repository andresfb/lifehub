<?php

namespace Modules\Dashboard\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Dashboard\Models\HomepageItem;

class PinCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', HomepageItem::class);
    }

    public function rules(): array
    {
        return [
            'section_slug' => ['required', 'exists:dashboard_homepage_sections,slug'],
            'title' => ['required'],
            'url' => ['required', 'url'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string'],
            'icon_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'tags' => ['nullable', 'array'],
        ];
    }
}
