<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_manifest_module', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_manifest_id')
                ->constrained(table: 'api_manifest')
                ->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['api_manifest_id', 'key']);
        });

        Schema::create('api_manifest_endpoint', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_manifest_id')
                ->constrained(table: 'api_manifest')
                ->cascadeOnDelete();
            $table->string('route_name')->nullable();
            $table->string('method')->nullable();
            $table->string('path')->nullable();
            $table->string('operation_id')->nullable();
            $table->timestamps();
            $table->unique(
                ['api_manifest_id', 'route_name', 'method', 'path', 'operation_id'],
                'api_manifest_endpoint_identity_unique'
            );
        });

        Schema::create('api_manifest_navigation_node', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_manifest_module_id')
                ->constrained(table: 'api_manifest_module')
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained(table: 'api_manifest_navigation_node')
                ->cascadeOnDelete();
            $table->string('node_id');
            $table->string('key')->nullable();
            $table->string('name');
            $table->string('web_path')->nullable();
            $table->string('icon')->nullable();
            $table->string('shortcut')->nullable();
            $table->boolean('show')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['api_manifest_module_id', 'node_id']);
        });

        Schema::create('api_manifest_command', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_manifest_module_id')
                ->constrained(table: 'api_manifest_module')
                ->cascadeOnDelete();
            $table->foreignId('api_manifest_endpoint_id')
                ->nullable()
                ->constrained(table: 'api_manifest_endpoint')
                ->nullOnDelete();
            $table->string('owner');
            $table->string('code');
            $table->string('name');
            $table->string('required_access');
            $table->string('shortcut')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['api_manifest_module_id', 'owner', 'code']);
        });

        Schema::create('api_manifest_action', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_manifest_module_id')
                ->constrained(table: 'api_manifest_module')
                ->cascadeOnDelete();
            $table->foreignId('api_manifest_endpoint_id')
                ->nullable()
                ->constrained(table: 'api_manifest_endpoint')
                ->nullOnDelete();
            $table->string('owner');
            $table->string('name');
            $table->string('required_access');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['api_manifest_module_id', 'owner', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_manifest_action');
        Schema::dropIfExists('api_manifest_command');
        Schema::dropIfExists('api_manifest_navigation_node');
        Schema::dropIfExists('api_manifest_endpoint');
        Schema::dropIfExists('api_manifest_module');
    }
};
