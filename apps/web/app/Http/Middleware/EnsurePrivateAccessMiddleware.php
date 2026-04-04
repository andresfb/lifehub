<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePrivateAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $this->isPrivateIp($request->ip())) {
            abort(423);
        }

        if (! Auth::check()) {
            abort(401);
        }

        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            ) === false;
    }
}
