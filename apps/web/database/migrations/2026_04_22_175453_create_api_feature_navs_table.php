<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_feature_navs', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_feature_id')->constrained()->cascadeOnDelete();
            $table->string('web_path')->nullable();
            $table->string('tui_command')->nullable();
            $table->string('icon')->nullable();
            $table->string('shortcut_key')->nullable();
            $table->boolean('show_in_menu')->nullable();
            $table->timestamps();

            $table->unique('api_feature_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_feature_navs');
    }
};
