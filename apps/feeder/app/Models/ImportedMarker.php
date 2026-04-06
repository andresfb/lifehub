<?php

namespace App\Models;

use App\Dtos\Bookmarks\MarkerItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property-read int $id
 * @property int $imported_id
 */
class ImportedMarker extends Model
{
    protected $guarded = [];

    /**
     * @param Collection<MarkerItem> $markers
     */
    public static function saveImported(Collection $markers): void
    {
        $markers->each(function (MarkerItem $marker) {
            self::query()
                ->updateOrCreate([
                    'imported_id' => $marker->id,
                ]);
        });
    }

    public static function imported(): array
    {
        return self::query()
            ->pluck('imported_id')
            ->toArray();
    }
}
