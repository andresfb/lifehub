<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

abstract class PageBaseController extends Controller
{
    abstract protected function getPageActions(): Collection;
}
