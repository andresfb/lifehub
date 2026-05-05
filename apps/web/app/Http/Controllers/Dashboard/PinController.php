<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PinCreateRequest;
use App\Http\Requests\Dashboard\PinUpdateRequest;
use App\Repository\Dashboard\Actions\Pins\PinIndexAction;
use App\Repository\Dashboard\Dtos\PinCreateItem;
use App\Repository\Dashboard\Dtos\PinUpdateItem;
use App\Repository\Dashboard\Services\ApiPinsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

final class PinController extends Controller
{
    public function __construct(
        private readonly ApiPinsService $pinsService,
    ) {}

    /**
     * @throws Throwable
     */
    public function index(PinIndexAction $action): View
    {
        $userId = (int) Auth::id();

        return view(
            'dashboard.pins.index',
            ['data' => $action->handle($userId)],
        );
    }

    public function store(PinCreateRequest $request): RedirectResponse
    {
        try {
            $this->pinsService->createPin(
                userId: (int) Auth::id(),
                item: PinCreateItem::from($request->validated())
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->flash('success', 'Pin created successfully.');

        return to_route('dashboard.pins.index');
    }

    public function update(PinUpdateRequest $request, string $pin): RedirectResponse
    {
        try {
            $this->pinsService->updatePin(
                userId: (int) Auth::id(),
                pinSlug: $pin,
                item: PinUpdateItem::from($request->validated())
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->flash('success', 'Pin updated successfully.');

        return to_route('dashboard.pins.index');
    }

    public function destroy(string $pin): RedirectResponse
    {
        try {
            $this->pinsService->deletePin(
                userId: (int) Auth::id(),
                pinSlug: $pin,
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->flash('success', 'Pin deleted successfully.');

        return to_route('dashboard.pins.index');
    }
}
