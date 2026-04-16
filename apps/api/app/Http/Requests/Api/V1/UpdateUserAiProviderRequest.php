<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Models\AiProvider;
use App\Services\AI\ProviderCatalog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class UpdateUserAiProviderRequest extends FormRequest
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
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'enabled' => ['sometimes', 'boolean'],
            'api_key' => ['sometimes', 'string'],
            'url' => ['sometimes', 'nullable', 'url'],
            'api_version' => ['sometimes', 'nullable', 'string', 'max:255'],
            'deployment' => ['sometimes', 'nullable', 'string', 'max:255'],
            'embedding_deployment' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var AiProvider|null $provider */
            $provider = $this->route('provider');
            if (! $provider instanceof AiProvider) {
                return;
            }

            $catalog = resolve(ProviderCatalog::class);
            $allowedFields = $catalog->allowedFields($provider->code);

            foreach (['url', 'api_version', 'deployment', 'embedding_deployment'] as $field) {
                if ($this->filled($field) && ! in_array($field, $allowedFields, true)) {
                    $validator->errors()->add($field, "The {$field} field is not supported for provider [{$provider->code}].");
                }
            }

            foreach ($catalog->requiredFields($provider->code) as $field) {
                if ($this->exists($field) && blank($this->input($field))) {
                    $validator->errors()->add($field, "The {$field} field is required for provider [{$provider->code}].");
                }
            }
        });
    }
}
