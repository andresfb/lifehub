<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserAiModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
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
