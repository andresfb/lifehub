<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\UpdateUserAiModelRequest;
use App\Http\Resources\UserAiModelResource;
use App\Models\AiModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserAiModelController extends ApiController
{
    public function show(Request $request, AiModel $model): JsonResponse
    {
        $this->abortIfNotOwned($request, $model);

        return $this->success(new UserAiModelResource($model)->resolve()['data']);
    }

    public function update(UpdateUserAiModelRequest $request, AiModel $model): JsonResponse
    {
        $this->abortIfNotOwned($request, $model);

        $model->fill($request->safe()->toArray());
        $model->save();

        return $this->success(
            new UserAiModelResource($model)->resolve()['data'],
            'AI model updated successfully'
        );
    }

    public function destroy(Request $request, AiModel $model): JsonResponse
    {
        $this->abortIfNotOwned($request, $model);
        $model->delete();

        return $this->noContent();
    }

    private function abortIfNotOwned(Request $request, AiModel $model): void
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($model->user_id === $user->id, 404);
    }
}
