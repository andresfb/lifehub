<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dtos\PageActionItem;
use Illuminate\Support\Collection;

abstract class PageBaseController extends Controller
{
    /**
     * @return Collection<int, PageActionItem>
     */
    abstract protected function getPageActions(): Collection;
}
