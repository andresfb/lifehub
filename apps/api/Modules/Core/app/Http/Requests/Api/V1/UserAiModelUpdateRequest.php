<?php

declare(strict_types=1);

namespace Modules\Core\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Models\AiModel;

final class UserAiModelUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', AiModel::class);
    }

    /**
     * @return array<string, ValidationRule|array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'enabled' => ['sometimes', 'boolean'],
            'supports_text' => ['sometimes', 'boolean'],
            'supports_images' => ['sometimes', 'boolean'],
            'supports_tts' => ['sometimes', 'boolean'],
            'supports_stt' => ['sometimes', 'boolean'],
            'supports_embeddings' => ['sometimes', 'boolean'],
            'supports_reranking' => ['sometimes', 'boolean'],
            'supports_files' => ['sometimes', 'boolean'],
        ];
    }
}
