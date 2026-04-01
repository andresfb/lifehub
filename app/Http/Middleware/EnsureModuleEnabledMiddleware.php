<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleKey;
use App\Services\Modules\ModuleAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureModuleEnabledMiddleware
{
    public function __construct(
        protected ModuleAccessService $moduleAccess
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        string $moduleKey,
        string $requiredLevel = 'read'
    ): Response {
        $user = $request->user();

        abort_unless($user, 403);

        $levelEnum = ModuleAccessLevel::tryFrom($requiredLevel);
        abort_if(blank($levelEnum), 500, "Invalid module access level: {$requiredLevel}");

        $keyEnum = ModuleKey::tryFrom($moduleKey);
        abort_if(blank($keyEnum), 500, "Invalid module key: {$moduleKey}");

        if ($this->moduleAccess->canUse($user, $keyEnum, $levelEnum)) {
            return $next($request);
        }

        abort(403, 'You do not have access to this module.');
    }
}
