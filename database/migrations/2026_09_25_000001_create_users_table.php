<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone_country_code', 10)->default('+966');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('preferred_locale', ['ar', 'en'])->default('ar');
            $table->string('fcm_token')->nullable();
            $table->decimal('wallet_balance', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['phone', 'status']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
