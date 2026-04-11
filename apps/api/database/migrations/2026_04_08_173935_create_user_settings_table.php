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
        Schema::create('user_settings', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('code');
            $table->string('key');
            $table->text('value');
            $table->timestamps();

            $table->index(['code', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
