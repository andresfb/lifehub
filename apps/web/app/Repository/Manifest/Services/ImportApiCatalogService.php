<?php

namespace App\Repository\Manifest\Services;

use App\Models\ApiCatalog;
use App\Models\ApiFeature;
use App\Models\ApiModule;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportApiCatalogService
{
    /**
     * @throws Throwable
     */
    public function execute(array $payload, int $userId): ApiCatalog
    {
        return DB::transaction(function () use ($payload, $userId) {
            $catalog = ApiCatalog::create([
                'user_id' => $userId,
                'version' => data_get($payload, 'version'),
                'raw_payload' => $payload,
            ]);

            foreach ((array) data_get($payload, 'modules', []) as $moduleIndex => $moduleData) {
                $module = ApiModule::create([
                    'api_catalog_id' => $catalog->id,
                    'key' => (string) data_get($moduleData, 'key'),
                    'name' => (string) data_get($moduleData, 'name'),
                    'description' => data_get($moduleData, 'description'),
                    'is_public' => (bool) data_get($moduleData, 'is_public', false),
                    'sort_order' => $moduleIndex,
                ]);

                foreach ((array) data_get($moduleData, 'features', []) as $featureIndex => $featureData) {
                    $this->storeFeature(
                        module: $module,
                        featureData: $featureData,
                        parentId: null,
                        sortOrder: $featureIndex
                    );
                }
            }

            return $catalog->load([
                'modules.features.children',
                'modules.allFeatures.nav',
                'modules.allFeatures.endpoints',
            ]);
        });
    }

    protected function storeFeature(ApiModule $module, array $featureData, ?int $parentId, int $sortOrder): ApiFeature
    {
        $feature = ApiFeature::create([
            'api_module_id' => $module->id,
            'parent_id' => $parentId,
            'external_id' => (string) data_get($featureData, 'id'),
            'title' => (string) data_get($featureData, 'title'),
            'kind' => (string) data_get($featureData, 'kind'),
            'required_access' => data_get($featureData, 'required_access'),
            'sort_order' => $sortOrder,
        ]);

        $nav = data_get($featureData, 'nav');
        if (is_array($nav)) {
            $feature->nav()->create([
                'web_path' => data_get($nav, 'web_path'),
                'tui_command' => data_get($nav, 'tui_command'),
                'icon' => data_get($nav, 'icon'),
                'shortcut_key' => data_get($nav, 'shortcut_key'),
                'show_in_menu' => data_get($nav, 'show_in_menu'),
            ]);
        }

        foreach ((array) data_get($featureData, 'endpoints', []) as $endpointIndex => $endpoint) {
            if (! is_array($endpoint)) {
                continue;
            }

            $feature->endpoints()->create([
                'route_name' => data_get($endpoint, 'route_name'),
                'method' => data_get($endpoint, 'method'),
                'path' => data_get($endpoint, 'path'),
                'operation_id' => data_get($endpoint, 'operation_id'),
                'sort_order' => $endpointIndex,
            ]);
        }

        foreach ((array) data_get($featureData, 'children', []) as $childIndex => $child) {
            if (! is_array($child)) {
                continue;
            }

            $this->storeFeature(
                module: $module,
                featureData: $child,
                parentId: $feature->id,
                sortOrder: $childIndex
            );
        }

        return $feature;
    }
}
