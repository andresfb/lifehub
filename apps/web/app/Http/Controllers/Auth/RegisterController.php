<?php

namespace App\Http\Controllers\Auth;

use App\Dtos\Auth\RegisterItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Repository\Auth\Enums\AuthStatus;
use App\Repository\Auth\Services\ApiAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        private readonly ApiAuthService $authService
    ) {}

    public function show(): View
    {
        return view('auth.register.show');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $result = $this->authService->register(
            RegisterItem::from($request)
        );

        if ($result === AuthStatus::FAILURE) {
            return redirect()->back()
                ->with('error', __('Cannot register this account'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
