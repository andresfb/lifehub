<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\LoadMenuAction;

abstract class Controller
{
    public function __construct(
        protected readonly LoadMenuAction $menuAction
    ) {}
}
