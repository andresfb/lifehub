<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\PinIndexAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Mcp\Exceptions\NotImplementedException;
use Throwable;

final class PinController extends Controller
{
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

    public function store(): never
    {
        throw new NotImplementedException('Not implemented yet.');
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
