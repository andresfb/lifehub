<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Libraries\MediaLibrary\MediaNamesLibrary;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

abstract class ModelWithMedia extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public function registerMediaConversions(Media|SpatieMedia|null $media = null): void
    {
        $this->addMediaCollection(MediaNamesLibrary::encrypted());

        $this->addMediaCollection(MediaNamesLibrary::document());

        $this->addMediaCollection(MediaNamesLibrary::video())
            ->acceptsMimeTypes([
                'video/mp4',
                'video/ogg',
                'video/webm',
                'video/avi',
                'video/3gpp',
                'video/quicktime',
                'video/x-msvideo',
                'video/x-flv',
                'video/x-msvideo',
                'video/msvideo',
                'video/x-ms-asf',
                'video/x-ms-wmv',
            ]);

        $this->addMediaCollection(MediaNamesLibrary::image())
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/avif',
                'image/gif',
                'image/webp',
                'image/tiff',
                'image/psd',
                'image/svg+xml',
                'image/apng',
                'image/vnd.adobe.photoshop',
                'application/x-photoshop',
                'application/photoshop',
                'application/psd',
            ])
            ->withResponsiveImages();
    }
}
