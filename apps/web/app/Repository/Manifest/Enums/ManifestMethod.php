<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Enums;

enum ManifestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}
