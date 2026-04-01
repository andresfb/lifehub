<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Models\Audit as AuditModel;

final class Audit extends AuditModel
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';
}
