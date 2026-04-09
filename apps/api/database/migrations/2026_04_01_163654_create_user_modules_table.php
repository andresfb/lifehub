<?php

declare(strict_types=1);

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_modules', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Module::class, 'module_id')
                ->constrained('modules')
                ->cascadeOnDelete();
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'granted_by')
                ->constrained('users');
            $table->boolean('enabled')->default(true);
            $table->string('access_level', 20)->default('read');
            $table->string('visibility', 30)->default('visible');
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_modules');
    }
};
