<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SearchProviderCreateRequest;
use App\Http\Requests\Dashboard\SearchProviderUpdateRequest;
use App\Repository\Dashboard\Actions\SearchProviders\SearchProviderIndexAction;
use App\Repository\Dashboard\Dtos\SearchProviders\SearchProviderCreateItem;
use App\Repository\Dashboard\Dtos\SearchProviders\SearchProviderUpdateItem;
use App\Repository\Dashboard\Services\SearchProviders\ApiSearchProviderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

final class SearchProviderController extends Controller
{
    public function __construct(
        private readonly ApiSearchProviderService $providerService,
    ) {}

    public function index(SearchProviderIndexAction $action): View|RedirectResponse
    {
        try {
            return view(
                'dashboard.search-providers.index',
                ['data' => $action->handle((int) Auth::id())],
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(SearchProviderCreateRequest $request): RedirectResponse
    {
        try {
            $this->providerService->createProvider(
                userId: (int) Auth::id(),
                item: SearchProviderCreateItem::from($request->validated())
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->flash('success', 'Search Provider created successfully.');

        return to_route('dashboard.search-providers.index');
    }

    public function update(SearchProviderUpdateRequest $request, string $provider): RedirectResponse
    {
        try {
            $this->providerService->updateProvider(
                userId: (int) Auth::id(),
                providerSlug: $provider,
                item: SearchProviderUpdateItem::from($request->validated())
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->flash('success', 'Search Provider updated successfully.');

        return to_route('dashboard.search-providers.index');
    }

    public function destroy(string $provider): RedirectResponse
    {
        try {
            $this->providerService->deleteProvider(
                userId: (int) Auth::id(),
                providerSlug: $provider,
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->flash('success', 'Search Provider deleted successfully.');

        return to_route('dashboard.search-providers.index');
    }
}
