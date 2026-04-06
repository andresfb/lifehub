<?php

namespace App\Dtos;

enum LifeHubApiEndpoint: string
{
    case BULK_MARKER_IMPORT = 'bookmarks/marker/bulk/import';
}
