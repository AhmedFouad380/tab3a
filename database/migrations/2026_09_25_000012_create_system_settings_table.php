<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., app_name, tax_rate, max_file_size_mb, support_phone
            $table->json('value'); // Can store strings, numbers, json translations {"ar": "...", "en": "..."}
            $table->string('group')->default('general'); // general, print_settings, pricing, contact, payment, notification
            $table->enum('type', ['string', 'number', 'boolean', 'json', 'image'])->default('string');
            $table->json('display_name')->nullable();
            $table->timestamps();

            $table->index(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
