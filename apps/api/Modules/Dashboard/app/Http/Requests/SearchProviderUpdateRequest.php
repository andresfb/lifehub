<?php

namespace Modules\Dashboard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Dashboard\Models\SearchProvider;

class SearchProviderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', SearchProvider::class);
    }

    public function rules(): array
    {
        $rules = SearchProvider::rules();
        $rules['order'] = ['required', 'integer'];

        return $rules;
    }
}
