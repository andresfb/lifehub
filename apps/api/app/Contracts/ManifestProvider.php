<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Dtos\Manifest\FeatureNode;
use App\Enums\ModuleKey;
use Illuminate\Support\Collection;

interface ManifestProvider
{
    public function moduleKey(): ModuleKey;

    public function moduleName(): string;

    public function moduleDescription(): string;

    public function isPublic(): bool;

    /**
     * @return Collection<int, FeatureNode>
     */
    public function features(): Collection;
}
