<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class BulkMarkerImportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'markers' => ['required', 'array', 'min:1', 'max:50'],
        ];
    }
}
