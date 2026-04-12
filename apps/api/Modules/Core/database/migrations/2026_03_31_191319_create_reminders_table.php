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
        Schema::create('core_reminders', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->morphs('remindable');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->timestamp('due_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('snoozed_until')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'due_at', 'deleted_at']);
            $table->index(['remindable_id', 'remindable_type', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_reminders');
    }
};
