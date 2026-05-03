<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PinCreateRequest;
use App\Repository\Dashboard\Actions\Pins\PinIndexAction;
use App\Repository\Dashboard\Dtos\PinCreateItem;
use App\Repository\Dashboard\Services\ApiPinsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Mcp\Exceptions\NotImplementedException;
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

    public function create(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }

    public function store(PinCreateRequest $request): RedirectResponse
    {
        try {
            $this->pinsService->createPin(
                (int) Auth::id(),
                PinCreateItem::from($request->validated())
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->flash('success', 'Pin created successfully.');

        return to_route('dashboard.pins.index');
    }

    public function edit(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }

    public function update(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }

    public function destroy(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }
}
