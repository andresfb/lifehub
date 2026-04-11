<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Modules\ModuleAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureModuleEnabledMiddleware
{
    public function __construct(
        private ModuleAccessService $moduleAccess
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        string $moduleKey,
        string $requiredLevel = 'read'
    ): Response {
        $user = $request->user();

        abort_unless($user, 403);

        $canAccess = match ($requiredLevel) {
            'read' => $this->moduleAccess->canRead($user, $moduleKey),
            'write' => $this->moduleAccess->canWrite($user, $moduleKey),
            default => null,
        };

        abort_if(is_null($canAccess), 500, "Invalid module access level: {$requiredLevel}");

        if ($canAccess) {
            return $next($request);
        }

        abort(403, 'You do not have access to this module.');
    }
}
