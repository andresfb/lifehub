<?php

declare(strict_types=1);

namespace Modules\Core\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Models\SearchHistory;

final class SearchHistoryCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', SearchHistory::class);
    }

    public function rules(): array
    {
        return [
            'module' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'term' => ['required', 'string', 'min:2'],
        ];
    }
}
