<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SearchProviderController extends Controller
{
    public function index(): View
    {
        $userId = (int) Auth::id();

        return view(
            'dashboard.search-providers.index',
//            ['data' => $action->handle($userId)],
        // TODO: implement the Search Provider CRUD functions
        );
    }
}
