<?php

declare(strict_types=1);

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// TODO: remove this Model
abstract class ModelWithMedia extends Model implements HasMedia
{
    use InteractsWithMedia;

    //    public function registerMediaCollections(): void
    //    {
    //        $this->addMediaCollection(MediaNamesLibrary::encrypted());
    //
    //        $this->addMediaCollection(MediaNamesLibrary::document());
    //
    //        $this->addMediaCollection(MediaNamesLibrary::video())
    //            ->acceptsMimeTypes([
    //                'video/mp4',
    //                'video/ogg',
    //                'video/webm',
    //                'video/avi',
    //                'video/3gpp',
    //                'video/quicktime',
    //                'video/x-msvideo',
    //                'video/x-flv',
    //                'video/x-msvideo',
    //                'video/msvideo',
    //                'video/x-ms-asf',
    //                'video/x-ms-wmv',
    //            ]);
    //
    //        $this->addMediaCollection(MediaNamesLibrary::image())
    //            ->acceptsMimeTypes([
    //                'image/jpeg',
    //                'image/png',
    //                'image/avif',
    //                'image/gif',
    //                'image/webp',
    //                'image/tiff',
    //                'image/psd',
    //                'image/svg+xml',
    //                'image/apng',
    //                'image/vnd.adobe.photoshop',
    //                'application/x-photoshop',
    //                'application/photoshop',
    //                'application/psd',
    //            ])
    //            ->withResponsiveImages();
    //    }
}
