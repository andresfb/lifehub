<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\UpdateUserAiModelRequest;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Resources\Api\V1\UserAiModelResource;
use Modules\Core\Models\AiModel;

final class UserAiModelController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(AiModel::class);
    }

    public function show(AiModel $model): JsonResponse
    {
        return $this->success(new UserAiModelResource($model)->resolve()['data']);
    }

    public function update(UpdateUserAiModelRequest $request, AiModel $model): JsonResponse
    {
        $model->fill($request->safe()->toArray());
        $model->save();

        return $this->success(
            new UserAiModelResource($model)->resolve()['data'],
            'AI model updated successfully'
        );
    }

    public function destroy(AiModel $model): JsonResponse
    {
        $model->delete();

        return $this->noContent();
    }
}
