<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_items', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->morphs('entity');
            $table->string('module');
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->json('tags')->nullable();
            $table->json('keyboards')->nullable();
            $table->json('metadata')->nullable();
            $table->json('urls')->nullable();
            $table->boolean('is_private')
                ->default(false)
                ->index();
            $table->boolean('is_archived')
                ->default(false)
                ->index();
            $table->timestamp('source_updated_at')->nullable();
            $table->timestamps();

            $table->index(['entity_id', 'entity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_items');
    }
};
