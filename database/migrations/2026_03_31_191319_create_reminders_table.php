<?php

declare(strict_types=1);

use App\Models\Account;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Account::class)
                ->constrained('accounts')
                ->cascadeOnDelete();
            $table->uuidMorphs('remindable');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->timestamp('due_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('snoozed_until')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['account_id', 'due_at']);
            $table->index(['remindable_id', 'remindable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
