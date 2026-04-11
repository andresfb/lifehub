<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Modules\Dashboard\Dtos\HomepageItemDto;
use Modules\Dashboard\Dtos\HomepageSectionItem;
use Modules\Dashboard\Http\Resources\HomepageSectionResource;

test('pins index returns homepage sections as json api resources', function (): void {
    $response = HomepageSectionResource::collection(collect([
        new HomepageSectionItem(
            id: 10,
            userId: 25,
            slug: 'favorites',
            name: 'Favorites',
            items: collect([
                new HomepageItemDto(
                    id: 77,
                    slug: 'laravel-docs',
                    title: 'Laravel Docs',
                    url: 'https://laravel.com/docs',
                ),
            ]),
        ),
    ]))->toResponse(Request::create('/api/v1/dashboard/pins', 'GET'));

    $payload = $response->getData(true);

    expect($response->headers->get('Content-Type'))->toBe('application/vnd.api+json')
        ->and($payload['data'])->toHaveCount(1)
        ->and($payload['data'][0]['id'])->toBe('10')
        ->and($payload['data'][0]['type'])->toBe('homepage-sections')
        ->and($payload['data'][0]['attributes'])->toBe([
            'userId' => 25,
            'slug' => 'favorites',
            'name' => 'Favorites',
            'bg_color' => '',
        ])
        ->and($payload['data'][0]['relationships']['items'])->toBe([
            [
                'id' => '77',
                'type' => 'homepage-items',
                'attributes' => [
                    'slug' => 'laravel-docs',
                    'title' => 'Laravel Docs',
                    'url' => 'https://laravel.com/docs',
                    'image' => '',
                    'tags' => [],
                ],
            ],
        ]);
});
