<?php

declare(strict_types=1);

namespace App\Domain\Core\Http\Controllers;

use App\Domain\Core\Actions\HomepageAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class HomepageController extends Controller
{
    public function __invoke(HomepageAction $homeAction): Response
    {
        $sections = $homeAction->handle(Auth::id());

        // TODO: create the search providers

        return Inertia::render('Dashboard', [
            'sections' => $sections,
            'providers' => [],
        ]);
    }
}
