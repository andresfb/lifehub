<?php

namespace App\Models\Bookmarks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read int $order_by
 */
class Section extends Model
{
    use SoftDeletes;

    protected $connection = 'bookmarks';

    public function markers(): HasMany
    {
        return $this->hasMany(Marker::class);
    }
}
