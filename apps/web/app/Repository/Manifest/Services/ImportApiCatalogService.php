<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Services;

use App\Models\ApiManifest;
use App\Models\ApiManifestAction;
use App\Models\ApiManifestCommand;
use App\Models\ApiManifestModule;
use App\Models\ApiManifestNavigationNode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use JsonException;
use Throwable;

final class ImportApiCatalogService
{
    /**
     * @throws Throwable
     */
    public function execute(array $payload, int $userId): void
    {
        DB::transaction(function () use ($payload, $userId): void {
            $manifest = ApiManifest::query()
                ->updateOrCreate([
                    'user_id' => $userId,
                ], [
                    'version' => (string) Arr::get($payload, 'version', ''),
                    'payload' => $payload,
                ]);

            $manifest->modules()->delete();
            $manifest->endpoints()->delete();

            $endpointIds = [];
            $modules = Arr::get($payload, 'modules', []);

            foreach ($modules as $moduleIndex => $modulePayload) {
                $module = $manifest->modules()
                    ->create([
                        'key' => $modulePayload['key'],
                        'name' => $modulePayload['name'],
                        'description' => $modulePayload['description'] ?? null,
                        'is_public' => (bool) ($modulePayload['is_public'] ?? $modulePayload['isPublic'] ?? false),
                        'sort_order' => $moduleIndex,
                    ]);

                $this->createNavigationNode(
                    module: $module,
                    nodePayload: $modulePayload['navigation'] ?? null,
                    sortOrder: 0,
                );

                foreach (($modulePayload['commands'] ?? []) as $commandIndex => $commandPayload) {
                    ApiManifestCommand::query()
                        ->create([
                            'api_manifest_module_id' => $module->id,
                            'api_manifest_endpoint_id' => $this->resolveEndpointId(
                                manifest: $manifest,
                                endpointPayload: $commandPayload['endpoint'] ?? [],
                                endpointIds: $endpointIds,
                            ),
                            'owner' => $commandPayload['owner'],
                            'code' => $commandPayload['code'],
                            'name' => $commandPayload['name'],
                            'required_access' => $commandPayload['required_access'],
                            'shortcut' => $commandPayload['shortcut'] ?? null,
                            'sort_order' => $commandIndex,
                        ]);
                }

                foreach (($modulePayload['actions'] ?? []) as $actionIndex => $actionPayload) {
                    ApiManifestAction::query()
                        ->create([
                            'api_manifest_module_id' => $module->id,
                            'api_manifest_endpoint_id' => $this->resolveEndpointId(
                                manifest: $manifest,
                                endpointPayload: $actionPayload['endpoint'] ?? [],
                                endpointIds: $endpointIds,
                            ),
                            'owner' => $actionPayload['owner'],
                            'name' => $actionPayload['name'],
                            'required_access' => $actionPayload['required_access'],
                            'sort_order' => $actionIndex,
                        ]);
                }
            }
        });
    }

    /**
     * @param  array<int, string|int|null>  $endpointIds
     * @param  array<string, mixed>  $endpointPayload
     *
     * @throws JsonException
     */
    private function resolveEndpointId(ApiManifest $manifest, array $endpointPayload, array &$endpointIds): int
    {
        $signature = json_encode([
            $endpointPayload['route_name'] ?? null,
            $endpointPayload['method'] ?? null,
            $endpointPayload['path'] ?? null,
            $endpointPayload['operation_id'] ?? null,
        ], JSON_THROW_ON_ERROR);

        if (isset($endpointIds[$signature])) {
            return $endpointIds[$signature];
        }

        $endpoint = $manifest->endpoints()->create([
            'route_name' => $endpointPayload['route_name'] ?? null,
            'method' => $endpointPayload['method'] ?? null,
            'path' => $endpointPayload['path'] ?? null,
            'operation_id' => $endpointPayload['operation_id'] ?? null,
        ]);

        $endpointIds[$signature] = $endpoint->id;

        return $endpoint->id;
    }

    /**
     * @param  array<string, mixed>|null  $nodePayload
     */
    private function createNavigationNode(
        ApiManifestModule $module,
        ?array $nodePayload,
        int $sortOrder,
        ?ApiManifestNavigationNode $parent = null,
    ): void {
        if ($nodePayload === null) {
            return;
        }

        $node = ApiManifestNavigationNode::query()->create([
            'api_manifest_module_id' => $module->id,
            'parent_id' => $parent?->id,
            'node_id' => $nodePayload['id'],
            'key' => $nodePayload['key'] ?? null,
            'name' => $nodePayload['name'],
            'web_path' => $nodePayload['web_path'] ?? null,
            'icon' => $nodePayload['icon'] ?? null,
            'shortcut' => $nodePayload['shortcut'] ?? null,
            'show' => (bool) ($nodePayload['show'] ?? false),
            'sort_order' => $sortOrder,
        ]);

        foreach (($nodePayload['nodes'] ?? []) ?: [] as $childIndex => $childNode) {
            $this->createNavigationNode(
                module: $module,
                nodePayload: $childNode,
                sortOrder: $childIndex,
                parent: $node,
            );
        }
    }
}
