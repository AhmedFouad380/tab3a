<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"ar": "فرع الرياض الرئيسي", "en": "Riyadh Main Branch"}
            $table->json('address'); // {"ar": "شارع العليا...", "en": "Olaya St..."}
            $table->string('city');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allows_pre_order')->default(true);
            $table->integer('daily_capacity')->default(100);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'allows_pre_order']);
        });

        Schema::create('branch_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->time('opening_time');
            $table->time('closing_time');
            $table->boolean('is_day_off')->default(false);
            $table->timestamps();

            $table->unique(['branch_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_working_hours');
        Schema::dropIfExists('branches');
    }
};
