<?php

declare(strict_types=1);

namespace Modules\Core\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Models\SearchHistory;

final class SearchHistoryFindRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', SearchHistory::class);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'module' => ['required', 'string'],
            'type' => ['required', 'string'],
            'term' => ['required', 'string'],
        ];
    }
}
