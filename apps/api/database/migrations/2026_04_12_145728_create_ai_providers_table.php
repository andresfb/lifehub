<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(UserSetting::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->boolean('enabled')->default(true);
            $table->text('api_key');
            $table->text('url')->nullable();
            $table->string('api_version')->nullable();
            $table->string('deployment')->nullable();
            $table->string('embedding_deployment')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'code']);
            $table->index(['user_id', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
