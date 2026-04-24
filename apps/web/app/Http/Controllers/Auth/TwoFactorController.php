<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorRequest;
use App\Repository\Auth\Enums\AuthStatus;
use App\Repository\Auth\Libraries\AuthSession;
use App\Repository\Auth\Services\ApiAuthService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class TwoFactorController extends Controller
{
    public function show(): View
    {
        return view('auth.two-factor.show');
    }

    /**
     * @throws Exception
     */
    public function store(TwoFactorRequest $request, ApiAuthService $service): RedirectResponse
    {
        abort_unless(AuthSession::has('login.email'), 401);

        $result = $service->validateTwoFactorCode(
            $request->safe()->string('code')->toString(),
            AuthSession::get('login.email')
        );

        abort_if($result === AuthStatus::FAILURE, 401);

        return redirect()->intended(route('dashboard'));
    }
}
