<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', static function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('is_core')->default(false);
            $table->boolean('is_public')->default(true);
            $table->string('status', 30)->default('active');
            $table->json('settings_schema')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
