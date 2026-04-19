<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\AiProvider;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_ai_models', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(AiProvider::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->boolean('enabled')->default(true);
            $table->boolean('supports_text')->default(false);
            $table->boolean('supports_images')->default(false);
            $table->boolean('supports_tts')->default(false);
            $table->boolean('supports_stt')->default(false);
            $table->boolean('supports_embeddings')->default(false);
            $table->boolean('supports_reranking')->default(false);
            $table->boolean('supports_files')->default(false);
            $table->timestamps();

            $table->unique(['ai_provider_id', 'name']);
            $table->index(['user_id', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_ai_models');
    }
};
