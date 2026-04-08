<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\LoadMenuAction;
use Inertia\Inertia;
use Inertia\Response;

abstract class Controller
{
    public function __construct(
        protected readonly LoadMenuAction $menuAction
    ) {}

    protected function renderInertia(int $userId, string $component, array $props = []): Response
    {
        $props['menu'] = $this->menuAction->handle($userId);

        return Inertia::render($component, $props);
    }
}
