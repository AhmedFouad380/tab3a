<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kiosk_machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('machine_code')->unique(); // Unique Kiosk Code (e.g., KSK-101)
            $table->string('qr_token')->unique(); // Generated token embedded in the physical QR code
            $table->string('nfc_tag_id')->nullable()->unique(); // NFC serial / tag payload
            $table->json('name'); // {"ar": "ماكينة المكتبة - الدور الأول", "en": "Library Kiosk - 1st Floor"}
            $table->json('location_description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Printer Health & Status
            $table->enum('status', ['online', 'offline', 'maintenance', 'busy'])->default('online');
            $table->timestamp('last_ping_at')->nullable();
            $table->string('ip_address')->nullable();

            // Supplies & Capabilities
            $table->boolean('supports_color')->default(true);
            $table->boolean('supports_duplex')->default(true);
            $table->json('supported_paper_sizes')->nullable(); // ["A4", "A3"]
            
            // Paper Levels
            $table->integer('paper_tray_a4_sheets')->default(500); // Current sheet count available
            $table->integer('paper_tray_a3_sheets')->default(0);
            $table->boolean('is_paper_low')->default(false);
            $table->boolean('is_paper_empty')->default(false);

            // Ink / Toner Levels (0 - 100%)
            $table->unsignedTinyInteger('black_toner_level')->default(100);
            $table->unsignedTinyInteger('cyan_toner_level')->default(100);
            $table->unsignedTinyInteger('magenta_toner_level')->default(100);
            $table->unsignedTinyInteger('yellow_toner_level')->default(100);
            $table->boolean('is_toner_low')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'qr_token', 'nfc_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_machines');
    }
};
