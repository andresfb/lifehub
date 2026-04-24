<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Mcp\Exceptions\NotImplementedException;

final class SearchPinController extends ApiController
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        throw new NotImplementedException('pin search not implemented');
    }
}
