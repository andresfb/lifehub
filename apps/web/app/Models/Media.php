<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * @property int $id
 * @property int $user_id
 * @property int $model_id
 * @property string $model_type
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string $mime_type
 * @property string $disk
 * @property string $conversions_disk
 * @property int $size
 * @property array $manipulations
 * @property array $custom_properties
 * @property array $generated_conversions
 * @property array $responsive_images
 * @property int $order_column
 * @property bool $is_encrypted
 * @property array $encryption_metadata
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 */
final class Media extends SpatieMedia implements UserModelInterface
{
    use BelongsToUser;

    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    use SoftDeletes;

    public function isEncrypted(): bool
    {
        return $this->is_encrypted;
    }

    #[Override]
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'encryption_metadata' => 'array',
            'is_encrypted' => 'boolean',
        ]);
    }
}
