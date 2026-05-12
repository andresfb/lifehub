<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\Dashboard\Actions\PinCreateAction;
use Modules\Dashboard\Actions\PinsAction;
use Modules\Dashboard\Actions\PinUpdateAction;
use Modules\Dashboard\Dtos\PinCreateItem;
use Modules\Dashboard\Dtos\PinUpdateItem;
use Modules\Dashboard\Http\Requests\Api\V1\PinCreateRequest;
use Modules\Dashboard\Http\Requests\Api\V1\PinListRequest;
use Modules\Dashboard\Http\Requests\Api\V1\PinUpdateRequest;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionResource;
use Modules\Dashboard\Models\HomepageItem;
use Throwable;

final class PinController extends ApiController
{
    public function index(PinListRequest $request, PinsAction $homeAction): AnonymousResourceCollection
    {
        $this->authorize('viewAny', HomepageItem::class);

        return HomepageSectionResource::collection(
            $homeAction->handle(
                (int) Auth::id(),
                (int) $request->validated('status', 1)
            )
        );
    }

    public function store(PinCreateRequest $request, PinCreateAction $action): JsonResponse
    {
        try {
            $slug = $action->handle(
                Auth::id(),
                PinCreateItem::from($request->validated())
            );
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }

        return $this->created(
            data: ['slug' => $slug],
            message: 'Pin created successfully'
        );
    }

    public function update(PinUpdateRequest $request, HomepageItem $pin, PinUpdateAction $action): JsonResponse
    {
        try {
            $action->handle(
                pin: $pin,
                item: PinUpdateItem::from($request->validated()),
            );
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }

        return $this->success(message: 'Pin updated successfully');
    }

    public function destroy(HomepageItem $pin): JsonResponse
    {
        $this->authorize('delete', $pin);

        $pin->delete();

        return $this->noContent();
    }
}
