<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Exceptions\NotImplementedException;
use Modules\Dashboard\Actions\PinCreateAction;
use Modules\Dashboard\Actions\PinsAction;
use Modules\Dashboard\Dtos\PinCreateItem;
use Modules\Dashboard\Http\Requests\Api\V1\PinCreateRequest;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionCollection;
use Modules\Dashboard\Models\HomepageItem;
use Throwable;

final class PinController extends ApiController
{
    public function index(PinsAction $homeAction): HomepageSectionCollection
    {
        return $homeAction->handle(Auth::id());
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

    public function show(HomepageItem $bookmark): never
    {
        throw new NotImplementedException('show action not implemented');
    }

    public function update(Request $request, HomepageItem $bookmark): never
    {
        throw new NotImplementedException('update action not implemented');
    }

    public function destroy(HomepageItem $bookmark): never
    {
        throw new NotImplementedException('destroy action not implemented');
    }
}
