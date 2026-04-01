<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Models\Audit as AuditModel;

class Audit extends AuditModel
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;
}
