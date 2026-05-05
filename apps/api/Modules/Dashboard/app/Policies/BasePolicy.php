<?php

namespace Modules\Dashboard\Policies;

use App\Contracts\UserModelInterface;
use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleKey;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class BasePolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly ModuleAccessService $accessService
    ) {}

    public function viewAny(User $user): bool
    {
        return $this->accessService->canUse($user, ModuleKey::DASHBOARD);
    }

    public function view(User $user, UserModelInterface $model): bool
    {
        return $model->getUserId() === $user->id
            && $this->accessService->canUse($user, ModuleKey::DASHBOARD);
    }

    public function create(User $user): bool
    {
        return $this->accessService->canUse($user, ModuleKey::DASHBOARD, ModuleAccessLevel::WRITE);
    }

    public function update(User $user, UserModelInterface $model): bool
    {
        return $model->getUserId() === $user->id
            && $this->accessService->canUse($user, ModuleKey::DASHBOARD, ModuleAccessLevel::WRITE);
    }

    public function delete(User $user, UserModelInterface $model): bool
    {
        return $model->getUserId() === $user->id
            && $this->accessService->canUse($user, ModuleKey::DASHBOARD, ModuleAccessLevel::WRITE);
    }
}
