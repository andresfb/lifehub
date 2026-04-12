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
        Schema::create('dashboard_homepage_sections', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('slug', 200)->unique();
            $table->string('name');
            $table->string('active')
                ->default(true)
                ->index();
            $table->smallInteger('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'slug', 'active', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_homepage_sections');
    }
};
