<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Domain\Core\Providers\CoreServiceProvider::class,
    App\Domain\Bookmarks\Providers\BookmarksServiceProvider::class,
    App\Domain\Dashboard\Providers\DashboardServiceProvider::class,
];
