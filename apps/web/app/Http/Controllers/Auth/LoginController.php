<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repository\Auth\Enums\AuthStatus;
use App\Repository\Auth\Services\ApiAuthService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly ApiAuthService $authService
    ) {}

    public function show(): View
    {
        return view('auth.login.show');
    }

    /**
     * @throws Exception
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $result = $this->authService->login(
            $request->string('email')->toString(),
            $request->string('password')->toString()
        );

        if ($result === AuthStatus::FAILURE) {
            abort(401);
        }

        if ($result === AuthStatus::TWO_FACTOR) {
            return redirect()->route('login.two-factor.show');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('login');
    }
}
