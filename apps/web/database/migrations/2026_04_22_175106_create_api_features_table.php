<?php

use App\Models\ApiModule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_features', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ApiModule::class)
                ->constrained('api_modules')
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('api_features')
                ->nullOnDelete();
            $table->string('external_id');
            $table->string('title');
            $table->string('kind');
            $table->string('required_access')->nullable();
            $table->string('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['api_module_id', 'external_id']);
            $table->index(['api_module_id', 'parent_id', 'sort_order']);
            $table->index(['kind', 'required_access']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_features');
    }
};
