<?php

declare(strict_types=1);

use App\Models\GlobalSearch;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('global_search_chunks')) {
            Schema::create('global_search_chunks', static function (Blueprint $table): void {
                $table->id();
                $table->foreignIdFor(GlobalSearch::class)
                    ->constrained()
                    ->cascadeOnDelete();
                $table->foreignIdFor(User::class)
                    ->constrained()
                    ->cascadeOnDelete();
                $table->unsignedInteger('chunk_index');
                $table->longText('content');
                $table->string('content_hash', 64);
                $table->unsignedInteger('content_length')->default(0);
                $table->string('embedded_provider_code')->nullable();
                $table->string('embedded_model')->nullable();
                $table->unsignedInteger('embedded_dimensions')->nullable();
                $table->string('embedded_content_hash', 64)->nullable();
                $table->timestamp('embedded_at')->nullable();
                $table->text('embedding_failed_reason')->nullable();
                $table->timestamp('embedding_failed_at')->nullable();
                $table->timestamps();

                $table->unique(['global_search_id', 'chunk_index']);
                $table->index(['user_id', 'global_search_id']);
                $table->index('content_hash');
                $table->index(['embedded_model', 'embedded_content_hash']);
            });

            return;
        }

        Schema::table('global_search_chunks', static function (Blueprint $table): void {
            if (! Schema::hasColumn('global_search_chunks', 'embedded_provider_code')) {
                $table->string('embedded_provider_code')->nullable()->after('content_length');
            }

            if (! Schema::hasColumn('global_search_chunks', 'embedded_model')) {
                $table->string('embedded_model')->nullable()->after('embedded_provider_code');
            }

            if (! Schema::hasColumn('global_search_chunks', 'embedded_dimensions')) {
                $table->unsignedInteger('embedded_dimensions')->nullable()->after('embedded_model');
            }

            if (! Schema::hasColumn('global_search_chunks', 'embedded_content_hash')) {
                $table->string('embedded_content_hash', 64)->nullable()->after('embedded_dimensions');
            }

            if (! Schema::hasColumn('global_search_chunks', 'embedded_at')) {
                $table->timestamp('embedded_at')->nullable()->after('embedded_content_hash');
            }

            if (! Schema::hasColumn('global_search_chunks', 'embedding_failed_reason')) {
                $table->text('embedding_failed_reason')->nullable()->after('embedded_at');
            }

            if (! Schema::hasColumn('global_search_chunks', 'embedding_failed_at')) {
                $table->timestamp('embedding_failed_at')->nullable()->after('embedding_failed_reason');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('global_search_chunks')) {
            return;
        }

        Schema::table('global_search_chunks', static function (Blueprint $table): void {
            $table->dropColumn([
                'embedded_provider_code',
                'embedded_model',
                'embedded_dimensions',
                'embedded_content_hash',
                'embedded_at',
                'embedding_failed_reason',
                'embedding_failed_at',
            ]);
        });
    }
};
