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
        Schema::create('entity_links', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class)
                ->constrained('accounts')
                ->cascadeOnDelete();
            $table->morphs('source');
            $table->morphs('target');
            $table->string('relation_type');
            $table->timestamps();

            $table->index(['source_id', 'source_type']);
            $table->index(['target_id', 'target_type']);
            $table->unique([
                'account_id',
                'source_id',
                'source_type',
                'target_id',
                'target_type',
            ], 'unq_entity_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_links');
    }
};
