<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone_country_code', 10)->default('+966');
            $table->string('phone');
            $table->string('otp_code', 6);
            $table->enum('purpose', ['login', 'register', 'update_phone'])->default('login');
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['phone', 'otp_code', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
