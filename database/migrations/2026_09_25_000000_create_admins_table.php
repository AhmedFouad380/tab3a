<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'branch_manager', 'support_agent'])->default('admin');
            $table->unsignedBigInteger('branch_id')->nullable(); // For branch staff/managers
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
