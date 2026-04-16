<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Services\AI\ProviderCatalog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class StoreUserAiProviderRequest extends FormRequest
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
            'code' => ['required', 'string', Rule::in(resolve(ProviderCatalog::class)->codes())],
            'name' => ['nullable', 'string', 'max:255'],
            'enabled' => ['sometimes', 'boolean'],
            'api_key' => ['required', 'string'],
            'url' => ['nullable', 'url'],
            'api_version' => ['nullable', 'string', 'max:255'],
            'deployment' => ['nullable', 'string', 'max:255'],
            'embedding_deployment' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateProviderFields($validator, (string) $this->input('code'));
        });
    }

    private function validateProviderFields(Validator $validator, string $code): void
    {
        $catalog = resolve(ProviderCatalog::class);
        $allowedFields = $catalog->allowedFields($code);

        foreach (['url', 'api_version', 'deployment', 'embedding_deployment'] as $field) {
            if ($this->filled($field) && ! in_array($field, $allowedFields, true)) {
                $validator->errors()->add($field, "The {$field} field is not supported for provider [{$code}].");
            }
        }

        foreach ($catalog->requiredFields($code) as $field) {
            if (! $this->filled($field)) {
                $validator->errors()->add($field, "The {$field} field is required for provider [{$code}].");
            }
        }
    }
}
