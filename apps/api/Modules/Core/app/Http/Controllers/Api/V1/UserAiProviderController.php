<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Requests\Api\V1\StoreUserAiModelRequest;
use Modules\Core\Http\Requests\Api\V1\StoreUserAiProviderRequest;
use Modules\Core\Http\Requests\Api\V1\UpdateUserAiProviderRequest;
use Modules\Core\Http\Resources\Api\V1\UserAiModelResource;
use Modules\Core\Http\Resources\Api\V1\UserAiProviderResource;
use Modules\Core\Models\AiProvider;
use Modules\Core\Services\AI\ProviderCatalog;
use Modules\Core\Services\AI\UserAiSettingsService;

final class UserAiProviderController extends ApiController
{
    public function __construct(
        private readonly ProviderCatalog $catalog,
        private readonly UserAiSettingsService $settingsService,
    ) {
        $this->authorizeResource(AiProvider::class);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();
        $user->ensureAiSettings();

        return UserAiProviderResource::collection(
            $user->aiProviders()->with('models')->orderBy('name')->get()
        );
    }

    public function store(StoreUserAiProviderRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $provider = $this->settingsService->createProvider($user, $request->validated());

        return $this->created(
            new UserAiProviderResource($provider->load('models'))->resolve()['data'],
            'AI provider created successfully'
        );
    }

    public function show(AiProvider $provider): JsonResponse
    {
        return $this->success(
            new UserAiProviderResource($provider->load('models'))->resolve()['data']
        );
    }

    public function update(UpdateUserAiProviderRequest $request, AiProvider $provider): JsonResponse
    {
        if ($request->exists('name')) {
            $provider->name = (string) $request->input('name');
        }

        if ($request->exists('enabled')) {
            $provider->enabled = $request->boolean('enabled');
        }

        if ($request->exists('api_key')) {
            $provider->api_key = $request->string('api_key')->toString();
        }

        foreach (['url', 'api_version', 'deployment', 'embedding_deployment'] as $field) {
            if ($request->exists($field)) {
                $provider->{$field} = $request->input($field) ?: null;
            }
        }

        $provider->save();

        return $this->success(
            new UserAiProviderResource($provider->load('models'))->resolve()['data'],
            'AI provider updated successfully'
        );
    }

    public function destroy(AiProvider $provider): JsonResponse
    {
        $provider->delete();

        return $this->noContent();
    }

    public function storeModel(StoreUserAiModelRequest $request, AiProvider $provider): JsonResponse
    {
        $attributes = array_merge(
            $this->catalog->defaultModelAttributes($provider->code),
            $request->safe()->except(['name']),
            [
                'name' => $request->string('name')->toString(),
                'enabled' => $request->boolean('enabled', true),
                'user_id' => $provider->user_id,
            ],
        );

        $model = $provider->models()->create($attributes);

        return $this->created(
            new UserAiModelResource($model)->resolve()['data'],
            'AI model created successfully'
        );
    }
}
