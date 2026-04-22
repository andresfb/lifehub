<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_catalogs', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->string('version');
            $table->json('raw_payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_catalogs');
    }
};
