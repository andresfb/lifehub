<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreUserAiModelRequest;
use App\Http\Requests\Api\V1\StoreUserAiProviderRequest;
use App\Http\Requests\Api\V1\UpdateUserAiProviderRequest;
use App\Http\Resources\UserAiModelResource;
use App\Http\Resources\UserAiProviderResource;
use App\Models\AiProvider;
use App\Models\User;
use App\Services\AI\ProviderCatalog;
use App\Services\AI\UserAiSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class UserAiProviderController extends ApiController
{
    public function __construct(
        private readonly ProviderCatalog $catalog,
        private readonly UserAiSettingsService $settingsService,
    ) {}

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

    public function show(Request $request, AiProvider $provider): JsonResponse
    {
        $this->abortIfNotOwned($request, $provider);

        return $this->success(
            new UserAiProviderResource($provider->load('models'))->resolve()['data']
        );
    }

    public function update(UpdateUserAiProviderRequest $request, AiProvider $provider): JsonResponse
    {
        $this->abortIfNotOwned($request, $provider);

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

    public function destroy(Request $request, AiProvider $provider): JsonResponse
    {
        $this->abortIfNotOwned($request, $provider);
        $provider->delete();

        return $this->noContent();
    }

    public function storeModel(StoreUserAiModelRequest $request, AiProvider $provider): JsonResponse
    {
        $this->abortIfNotOwned($request, $provider);

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

    private function abortIfNotOwned(Request $request, AiProvider $provider): void
    {
        // TODO: move all the abortIfNotOwned() to model Policies

        /** @var User $user */
        $user = $request->user();

        abort_unless($provider->user_id === $user->id, 404);
    }
}
