<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Dtos\Manifest\ModuleActionItem;
use App\Dtos\Manifest\ModuleCommandItem;
use App\Dtos\Manifest\NavigationItem;
use App\Enums\ModuleKey;
use Illuminate\Support\Collection;

interface ManifestProviderInterface
{
    public function moduleKey(): ModuleKey;

    public function moduleName(): string;

    public function moduleDescription(): string;

    public function isPublic(): bool;

    public function navigation(): NavigationItem;

    /**
     * @return Collection<int, ModuleCommandItem>|null
     */
    public function commands(): ?Collection;

    /**
     * @return Collection<int, ModuleActionItem>|null
     */
    public function actions(): ?Collection;
}
