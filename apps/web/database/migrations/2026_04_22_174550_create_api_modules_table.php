<?php

use App\Models\ApiCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_modules', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ApiCatalog::class)
                ->constrained('api_catalogs')
                ->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_public');
            $table->unsignedSmallInteger('sort_order');
            $table->timestamps();

            $table->unique(['api_catalog_id', 'key']);
            $table->index(['api_catalog_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_modules');
    }
};
