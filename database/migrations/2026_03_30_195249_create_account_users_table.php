<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_users', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class)
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('role');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_users');
    }
};
