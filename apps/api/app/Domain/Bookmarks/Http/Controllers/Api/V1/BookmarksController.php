<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Http\Controllers\Api\V1;

use App\Domain\Bookmarks\Models\Marker;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

final class BookmarksController extends Controller
{
    public function index() {}

    public function store(Request $request) {}

    public function show(Marker $marker) {}

    public function update(Request $request, Marker $marker) {}

    public function destroy(Marker $marker) {}
}
