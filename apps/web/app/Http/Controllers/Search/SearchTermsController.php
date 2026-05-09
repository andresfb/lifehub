<?php

declare(strict_types=1);

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\SearchTermsCreateRequest;
use App\Http\Requests\Search\SearchTermsRequest;
use App\Repository\Core\Actions\SearchHistory\SearchTermCreateAction;
use App\Repository\Core\Actions\SearchHistory\SearchTermListAction;
use App\Repository\Core\Dtos\SearchHistory\SearchTermItem;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class SearchTermsController extends Controller
{
    public function index(
        SearchTermsRequest $request,
        SearchTermListAction $action
    ): Factory|View {
        try {
            $result = $action->handle(
                (int) Auth::id(),
                SearchTermItem::from($request->validated())
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());

            $result = collect();
        }

        return view(
            'core.partials.search-terms.index',
            ['terms' => $result]
        );
    }

    public function store(
        SearchTermsCreateRequest $request,
        SearchTermCreateAction $action
    ): Response|RedirectResponse {
        try {
            $action->handle(
                (int) Auth::id(),
                SearchTermItem::from($request->validated())
            );
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return response()->noContent();
    }
}
