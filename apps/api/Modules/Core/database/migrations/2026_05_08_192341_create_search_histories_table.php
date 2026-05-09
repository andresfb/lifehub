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
        Schema::create('search_histories', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('hash')->unique();
            $table->string('module', 50);
            $table->string('type', 50);
            $table->text('query');
            $table->timestamps();

            $table->index(
                ['user_id', 'module', 'type', 'hash'],
                'user_module_type_hash_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_histories');
    }
};
