<?php

use App\Domain\Bookmarks\Models\Marker;

return [
    Marker::class => [
        'collection-schema' => [
            'name' => 'bookmarks_markers_index',
            'fields' => [
                ['name' => 'id', 'type' => 'string'],
                ['name' => 'user_id', 'type' => 'string', 'facet' => true],
                ['name' => 'category_id', 'type' => 'string', 'facet' => true],
                ['name' => 'category', 'type' => 'string'],
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'url', 'type' => 'string'],
                ['name' => 'domain', 'type' => 'string', 'optional' => true],
                ['name' => 'description', 'type' => 'string', 'optional' => true],
                ['name' => 'summary', 'type' => 'string', 'optional' => true],
                ['name' => 'notes', 'type' => 'string', 'optional' => true],
                ['name' => '__soft_deleted', 'type' => 'int32', 'optional' => true,],
                ['name' => 'created_at', 'type' => 'int64', 'sort' => true, 'optional' => true],
                ['name' => 'updated_at', 'type' => 'int64', 'sort' => true],
                ['name' => 'tags', 'type' => 'string[]', 'facet' => true],
            ],
            'default_sorting_field' => 'updated_at',
        ],
        'search-parameters' => [
            'query_by' => 'title,category,tags,url,domain,description,summary,notes',
        ],
    ],
];
