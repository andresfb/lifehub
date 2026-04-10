<?php

declare(strict_types=1);

namespace App\Dtos\Modules;

use App\Enums\ModuleKey;
use App\Enums\ModuleStatus;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ModuleRecordItem extends Data
{
    public function __construct(
        public readonly ModuleKey $key,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $isCore,
        public readonly bool $isPublic,
        public readonly ModuleStatus $status,
        public readonly bool $showMenu = true,
        public readonly ?MenuItem $menu = null,
        public readonly ?Collection $subMenus = null,
    ) {}

    public function cleanArray(): array
    {
        return $this->except(
            'show_menu',
            'menu',
            'sub_menus'
        )->toArray();
    }

    public function withMenu(?MenuItem $menu): self
    {
        if (blank($menu)) {
            return $this;
        }

        return new self(
            key: $this->key,
            name: $this->name,
            description: $this->description,
            isCore: $this->isCore,
            isPublic: $this->isPublic,
            status: $this->status,
            showMenu: $this->showMenu,
            menu: $menu,
            subMenus: $this->subMenus,
        );
    }

    public function withSubMenus(?Collection $subMenus): self
    {
        if (blank($subMenus)) {
            return $this;
        }

        return new self(
            key: $this->key,
            name: $this->name,
            description: $this->description,
            isCore: $this->isCore,
            isPublic: $this->isPublic,
            status: $this->status,
            showMenu: $this->showMenu,
            menu: $this->menu,
            subMenus: $subMenus,
        );
    }
}
