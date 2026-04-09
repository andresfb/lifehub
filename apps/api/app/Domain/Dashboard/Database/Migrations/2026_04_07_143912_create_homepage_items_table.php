<?php

declare(strict_types=1);

use App\Domain\Dashboard\Models\HomepageSection;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_homepage_items', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignIdFor(HomepageSection::class, 'section_id')
                ->constrained('dashboard_homepage_sections')
                ->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('url');
            $table->string('bg_color')->nullable();
            $table->boolean('active')->default(true);
            $table->smallInteger('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'slug', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_homepage_items');
    }
};
