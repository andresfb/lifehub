<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repository\Auth\Enums\LoginStatus;
use App\Repository\Auth\Services\ApiAuthService;
use Exception;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    /**
     * @throws Exception
     */
    public function store(LoginRequest $request, ApiAuthService $service): RedirectResponse
    {
        $result = $service->login(
            $request->string('email')->toString(),
            $request->string('password')->toString()
        );

        if ($result === LoginStatus::TWO_FACTOR) {
            return redirect()->route('login.two-factor.create');
        }

        return redirect()->intended(route('dashboard'));
    }
}
