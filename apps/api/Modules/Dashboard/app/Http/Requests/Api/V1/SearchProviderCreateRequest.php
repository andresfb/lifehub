<?php

namespace Modules\Dashboard\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Dashboard\Models\SearchProvider;

class SearchProviderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', SearchProvider::class);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return SearchProvider::rules();
    }
}
