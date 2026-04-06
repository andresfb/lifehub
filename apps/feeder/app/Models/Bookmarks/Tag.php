<?php

namespace App\Models\Bookmarks;

class Tag extends \Spatie\Tags\Tag
{
    protected $connection = 'bookmarks';
}
