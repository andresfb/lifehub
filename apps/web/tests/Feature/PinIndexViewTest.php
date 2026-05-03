<?php

declare(strict_types=1);

use App\Repository\Dashboard\Dtos\PinIndexItem;
use App\Repository\Dashboard\Dtos\PinItem;
use App\Repository\Dashboard\Dtos\SectionItem;
use App\Repository\Manifest\Dtos\ModuleItem;
use App\Repository\Manifest\Dtos\NavigationItem;
use Illuminate\Support\Collection;

test('pins page renders modal triggers and pin payload metadata', function () {
    $this->withoutVite();

    $html = (string) $this->view('dashboard.pins.index', [
        'data' => pinIndexData(),
    ]);

    expect($html)
        ->toContain('New Section')
        ->toContain('New Pin')
        ->toContain('btn btn-primary btn-sm')
        ->toContain('table table-zebra')
        ->toContain('>Active<')
        ->toContain('badge-secondary badge-soft')
        ->toContain('x-data="pinsModal(')
        ->toContain('x-on:click="openCreatePin()"')
        ->toContain("storeAction: '".str_replace('/', '\\/', route('dashboard.pins.store'))."'")
        ->toContain("updateActionTemplate: '".str_replace('/', '\\/', route('dashboard.pins.update', ['pin' => '__PIN__']))."'")
        ->toContain('x-on:click="openEditPin(')
        ->toContain('\u0022slug\u0022:\u0022lifehub\u0022')
        ->toContain('\u0022sectionSlug\u0022:\u0022favorites\u0022')
        ->toContain('\u0022sectionName\u0022:\u0022Favorites\u0022')
        ->toContain('\u0022title\u0022:\u0022LifeHub\u0022')
        ->toContain('\u0022url\u0022:')
        ->toContain('example.com')
        ->toContain('\u0022order\u0022:1')
        ->toContain('\u0022active\u0022:true')
        ->toContain('\u0022icon\u0022:\u0022LH\u0022')
        ->toContain('\u0022iconColor\u0022:\u0022#10b981\u0022')
        ->toContain('\u0022description\u0022:\u0022Pinned link\u0022')
        ->toContain('\u0022tagsText\u0022:\u0022docs, tools\u0022')
        ->toContain('id="pin-form-desktop"')
        ->toContain('id="pin-form-mobile"')
        ->toContain('x-bind:action="form.action"')
        ->toContain('name="_method"')
        ->toContain('form="pin-form-desktop"')
        ->toContain('form="pin-form-mobile"')
        ->toContain('x-model="form.active"')
        ->toContain('id="pin-active-desktop"')
        ->toContain('id="pin-active-mobile"')
        ->toContain('toggle toggle-success')
        ->toContain('name="section_slug"')
        ->toContain('name="title"')
        ->toContain('name="url"')
        ->toContain('name="icon_color"')
        ->toContain('name="description"')
        ->toContain('name="tags[]"')
        ->toContain('id="pin-modal-title"')
        ->toContain('id="pin-modal-title-mobile"');
});

/**
 * @return Collection<int, SectionItem>
 */
function pinSections(): Collection
{
    return collect([
        new SectionItem(
            id: 1,
            slug: 'favorites',
            name: 'Favorites',
            order: 1,
            items: collect([
                new PinItem(
                    slug: 'lifehub',
                    title: 'LifeHub',
                    url: 'https://example.com',
                    active: true,
                    tags: ['docs', 'tools'],
                    order: 1,
                    icon: 'LH',
                    icon_color: '#10b981',
                    description: 'Pinned link',
                ),
            ]),
        ),
    ]);
}

function pinIndexData(): PinIndexItem
{
    return new PinIndexItem(
        sections: ['favorites' => 'Favorites'],
        bookmarks: pinSections(),
        modules: pinModules(),
        canCreate: true,
        canEdit: true,
        canDelete: true,
        storeAction: route('dashboard.pins.store'),
        updateActionTemplate: route('dashboard.pins.update', ['pin' => '__PIN__']),
        deleteRouteName: 'dashboard.pins.destroy',
        searchTagsRouteName: 'dashboard.tags.search',
    );
}

/**
 * @return Collection<int, ModuleItem>
 */
function pinModules(): Collection
{
    return collect([
        new ModuleItem(
            key: 'dashboard',
            name: 'Dashboard',
            description: 'Homepage',
            isPublic: false,
            sortOrder: 1,
            navigation: collect([
                new NavigationItem(
                    key: 'dashboard',
                    name: 'Dashboard',
                    web_path: '/dashboard',
                    icon: 'home',
                    show: true,
                    sort_order: 1,
                ),
            ]),
        ),
    ]);
}
