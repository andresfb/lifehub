<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Dtos\AI\ResolvedUserAiProvider;
use App\Enums\AiModelFeatures;
use App\Exceptions\UserAiConfigurationException;
use App\Models\AiModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Ai\Ai;
use Laravel\Ai\Enums\Lab;

final class UserAiResolver
{
    public function resolve(
        User $user,
        AiModelFeatures $feature = AiModelFeatures::text,
        ?string $providerCode = null,
        ?string $modelName = null
    ): ResolvedUserAiProvider {
        $model = AiModel::query()
            ->with('provider')
            ->where('user_id', $user->id)
            ->where('enabled', true)
            ->when($providerCode !== null, fn (Builder $query): Builder => $query->whereHas(
                'provider',
                fn (Builder $providerQuery): Builder => $providerQuery
                    ->where('user_id', $user->id)
                    ->where('enabled', true)
                    ->where('code', $providerCode)
            ))
            ->when($providerCode === null, fn (Builder $query): Builder => $query->whereHas(
                'provider',
                fn (Builder $providerQuery): Builder => $providerQuery
                    ->where('user_id', $user->id)
                    ->where('enabled', true)
            ))
            ->when($modelName !== null, fn (Builder $query): Builder => $query->where('name', $modelName))
            ->where($feature->value, true)
            ->orderBy('ai_provider_id')
            ->orderBy('id')
            ->first();

        if (! $model instanceof AiModel) {
            throw new UserAiConfigurationException("No AI provider and model are available for {$feature} feature");
        }

        $provider = $model->provider;
        $runtimeProviderName = sprintf(
            'user-%s-%s',
            $user->id,
            Str::slug($provider->code.'-'.$provider->id)
        );

        config()->set("ai.providers.{$runtimeProviderName}", array_filter([
            'driver' => $provider->code,
            'key' => $provider->api_key,
            'url' => $provider->url,
            'api_version' => $provider->api_version,
            'deployment' => $provider->deployment,
            'embedding_deployment' => $provider->embedding_deployment,
        ], filled(...)));

        Ai::purge($runtimeProviderName);

        return new ResolvedUserAiProvider(
            providerName: $runtimeProviderName,
            providerCode: $provider->code,
            lab: Lab::from($provider->code),
            model: $model->name,
            featureCapabilities: [
                'supports_text' => $model->supports_text,
                'supports_images' => $model->supports_images,
                'supports_tts' => $model->supports_tts,
                'supports_stt' => $model->supports_stt,
                'supports_embeddings' => $model->supports_embeddings,
                'supports_reranking' => $model->supports_reranking,
                'supports_files' => $model->supports_files,
            ],
        );
    }
}
