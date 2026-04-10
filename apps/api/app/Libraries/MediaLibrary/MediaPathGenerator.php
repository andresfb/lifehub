<?php

declare(strict_types=1);

namespace App\Libraries\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

final class MediaPathGenerator implements PathGenerator
{
    /**
     * getPath Method.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media).'/';
    }

    /**
     * getPathForConversions Method.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media).'/conversions/';
    }

    /**
     * getPathForResponsiveImages Method.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media).'/responsive-images/';
    }

    /**
     * getBasePath Method.
     */
    private function getBasePath(Media $media): string
    {
        $contentId = mb_str_pad((string) $media->model_id, 6, '0', STR_PAD_LEFT);
        $mediaId = mb_str_pad((string) $media->id, 6, '0', STR_PAD_LEFT);
        $userId = $this->getUserId($media);

        return Str::of($media->model_type)
            ->replace('_', '-')
            ->lower()
            ->append('/')
            ->append($userId)
            ->append('/')
            ->append($media->collection_name)
            ->append('/')
            ->append(
                collect(mb_str_split($contentId, 3))
                    ->implode('/')
            )
            ->append('/')
            ->append('media')
            ->append('/')
            ->append(
                collect(mb_str_split($mediaId, 3))
                    ->implode('/')
            )
            ->trim()
            ->toString();
    }

    private function getUserId(Media $media): string
    {
        $userId = mb_str_pad((string) $media->user_id, 6, '0', STR_PAD_LEFT);

        return Str::of('user')
            ->append('/')
            ->append(
                collect(mb_str_split($userId, 3))
                    ->implode('/')
            )
            ->trim()
            ->toString();
    }
}
