<?php

namespace App\Domain\Bookmarks\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BulkMarkerImportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'markers' => 'required|array|min:1|max:50',
        ];
    }
}
