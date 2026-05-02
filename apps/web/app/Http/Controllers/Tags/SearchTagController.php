<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tags;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchTagRequest;
use App\Repository\Common\Services\ApiSearchTagsService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class SearchTagController extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke(SearchTagRequest $request, ApiSearchTagsService $service): Factory|View
    {
        try {
            $result = $service->getUseTags(
                (int) Auth::id(),
                $request->safe()
                    ->string('q')
                    ->value()
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $result = collect();
        }

        return view(
            'core.partials.tags.index',
            ['tags' => $result]
        );
    }
}
