<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\Modules\MenuItem;
use App\Dtos\Modules\ModuleRecordItem;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final readonly class LoadMenuAction
{
    /**
     * @return Collection<ModuleRecordItem>
     */
    public function handle(int $userId): Collection
    {
        $userModules = Cache::tags('users')
            ->remember(
                md5("user:modules:{$userId}"),
                now()->addMonth(),
                function () use ($userId): array {
                    return User::query()
                        ->where('id', $userId)
                        ->with('accessibleModules')
                        ->firstOrFail()
                        ->accessibleModules
                        ->pluck('key')
                        ->toArray();
                }
            );

        if ((blank($userModules))) {
            return collect();
        }

        $modules = resolve('module_records');
        if (! $modules instanceof Collection) {
            return collect();
        }

        if ($modules->isEmpty()) {
            return collect();
        }

        return $modules->where('showMenu', true)
            ->whereIn('key', $userModules)
            ->map(function (ModuleRecordItem $item) use ($userId) {
                return $item->withMenu(
                    $item->menu?->withShortCut(
                        UserSetting::getMenuShortcut($userId, $item->menu?->code)
                    )
                )
                    ->withSubMenus(
                        $item->subMenus?->map(function (MenuItem $subMenuItem) use ($userId) {
                            return $subMenuItem->withShortCut(
                                UserSetting::getMenuShortcut($userId, $subMenuItem->menu?->code)
                            );
                        })
                    );
            });
    }
}
