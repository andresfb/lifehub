<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Ai\Contracts\Providers\AudioProvider;
use Laravel\Ai\Contracts\Providers\EmbeddingProvider;
use Laravel\Ai\Contracts\Providers\FileProvider;
use Laravel\Ai\Contracts\Providers\ImageProvider;
use Laravel\Ai\Contracts\Providers\RerankingProvider;
use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Contracts\Providers\TranscriptionProvider;
use Laravel\Ai\Providers\AnthropicProvider;
use Laravel\Ai\Providers\AzureOpenAiProvider;
use Laravel\Ai\Providers\CohereProvider;
use Laravel\Ai\Providers\DeepSeekProvider;
use Laravel\Ai\Providers\ElevenLabsProvider;
use Laravel\Ai\Providers\GeminiProvider;
use Laravel\Ai\Providers\GroqProvider;
use Laravel\Ai\Providers\JinaProvider;
use Laravel\Ai\Providers\MistralProvider;
use Laravel\Ai\Providers\OllamaProvider;
use Laravel\Ai\Providers\OpenAiProvider;
use Laravel\Ai\Providers\OpenRouterProvider;
use Laravel\Ai\Providers\VoyageAiProvider;
use Laravel\Ai\Providers\XaiProvider;

final class ProviderCatalog
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function all(): Collection
    {
        $names = Config::collection('settings.providers')
            ->pluck('name', 'code');

        return collect(array_keys(config('ai.providers')))
            ->map(fn (string $code): array => [
                'code' => $code,
                'name' => (string) ($names[$code] ?? Str::headline($code)),
                'allowed_fields' => $this->allowedFields($code),
                'required_fields' => $this->requiredFields($code),
                'supports' => $this->featureFlags($code),
            ])
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public function codes(): array
    {
        return $this->all()->pluck('code')->all();
    }

    public function label(string $code): string
    {
        return (string) $this->definition($code)['name'];
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(string $code): array
    {
        $definition = $this->all()->firstWhere('code', $code);

        if ($definition === null) {
            throw new InvalidArgumentException("Unsupported AI provider [{$code}].");
        }

        return $definition;
    }

    /**
     * @return array<string, bool>
     */
    public function featureFlags(string $code): array
    {
        $providerClass = $this->providerClass($code);
        $interfaces = class_implements($providerClass);

        return [
            'supports_text' => in_array(TextProvider::class, $interfaces, true),
            'supports_images' => in_array(ImageProvider::class, $interfaces, true),
            'supports_tts' => in_array(AudioProvider::class, $interfaces, true),
            'supports_stt' => in_array(TranscriptionProvider::class, $interfaces, true),
            'supports_embeddings' => in_array(EmbeddingProvider::class, $interfaces, true),
            'supports_reranking' => in_array(RerankingProvider::class, $interfaces, true),
            'supports_files' => in_array(FileProvider::class, $interfaces, true),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function allowedFields(string $code): array
    {
        return match ($code) {
            'azure' => ['url', 'api_version', 'deployment', 'embedding_deployment'],
            'ollama', 'openai' => ['url'],
            default => [],
        };
    }

    /**
     * @return array<int, string>
     */
    public function requiredFields(string $code): array
    {
        return match ($code) {
            'azure' => ['url', 'api_version', 'deployment', 'embedding_deployment'],
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultModelAttributes(string $code): array
    {
        return $this->featureFlags($code);
    }

    /**
     * @return class-string
     */
    private function providerClass(string $code): string
    {
        return match ($code) {
            'anthropic' => AnthropicProvider::class,
            'azure' => AzureOpenAiProvider::class,
            'cohere' => CohereProvider::class,
            'deepseek' => DeepSeekProvider::class,
            'eleven' => ElevenLabsProvider::class,
            'gemini' => GeminiProvider::class,
            'groq' => GroqProvider::class,
            'jina' => JinaProvider::class,
            'mistral' => MistralProvider::class,
            'ollama' => OllamaProvider::class,
            'openai', 'firepass' => OpenAiProvider::class,
            'openrouter' => OpenRouterProvider::class,
            'voyageai' => VoyageAiProvider::class,
            'xai' => XaiProvider::class,
            default => throw new InvalidArgumentException("Unsupported AI provider [{$code}]."),
        };
    }
}
