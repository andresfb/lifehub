<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Requests\Api\V1\UserAiModelStoreRequest;
use Modules\Core\Http\Requests\Api\V1\UserAiProviderStoreRequest;
use Modules\Core\Http\Requests\Api\V1\UserAiProviderUpdateRequest;
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
        $this->authorize('viewAny', AiProvider::class);

        $user = $request->user();
        $user->ensureAiSettings();

        return UserAiProviderResource::collection(
            $user->aiProviders()
                ->with('models')
                ->orderBy('name')
                ->get()
        );
    }

    public function show(AiProvider $provider): UserAiProviderResource
    {
        $this->authorize('view', $provider);

        return new UserAiProviderResource($provider->load('models'));
    }

    public function store(UserAiProviderStoreRequest $request): UserAiProviderResource
    {
        $user = $request->user();
        $provider = $this->settingsService->createProvider($user, $request->validated());

        return new UserAiProviderResource($provider->load('models'));
    }

    public function update(UserAiProviderUpdateRequest $request, AiProvider $provider): UserAiProviderResource
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

        return new UserAiProviderResource($provider->load('models'));
    }

    public function destroy(AiProvider $provider): JsonResponse
    {
        $this->authorize('delete', $provider);

        $provider->delete();

        return $this->noContent();
    }

    public function storeModel(UserAiModelStoreRequest $request, AiProvider $provider): UserAiModelResource
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

        return new UserAiModelResource($model);
    }
}
