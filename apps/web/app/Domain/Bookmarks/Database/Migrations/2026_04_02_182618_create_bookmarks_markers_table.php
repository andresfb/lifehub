<?php

declare(strict_types=1);

use App\Domain\Bookmarks\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks_markers', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignIdFor(Category::class, 'category_id')
                ->constrained('bookmarks_categories')
                ->cascadeOnDelete();
            $table->string('status', 10)->default('active');
            $table->string('slug', 100)->unique();
            $table->string('title')->nullable();
            $table->string('site_title')->nullable();
            $table->text('url');
            $table->string('domain')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('priority')->default(9999);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'user_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks_markers');
    }
};
