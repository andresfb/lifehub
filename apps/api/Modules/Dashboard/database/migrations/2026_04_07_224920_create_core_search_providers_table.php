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
        Schema::create('dashboard_search_providers', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('url');
            $table->boolean('active')->default(true);
            $table->boolean('default')->default(false);
            $table->smallInteger('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(
                ['user_id', 'active', 'default', 'deleted_at'],
                'idx_user_active',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_search_providers');
    }
};
