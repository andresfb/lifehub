<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Requests\Api\V1\UserAiModelUpdateRequest;
use Modules\Core\Http\Resources\Api\V1\UserAiModelResource;
use Modules\Core\Models\AiModel;

final class UserAiModelController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(AiModel::class);
    }

    public function show(AiModel $model): UserAiModelResource
    {
        $this->authorize('view', $model);

        return new UserAiModelResource($model);
    }

    public function update(UserAiModelUpdateRequest $request, AiModel $model): UserAiModelResource
    {
        $model->fill($request->safe()->toArray());
        $model->save();

        return new UserAiModelResource($model->fresh());
    }

    public function destroy(AiModel $model): JsonResponse
    {
        $this->authorize('delete', $model);

        $model->delete();

        return $this->noContent();
    }
}
