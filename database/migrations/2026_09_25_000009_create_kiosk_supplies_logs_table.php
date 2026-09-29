<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kiosk_health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kiosk_machine_id')->constrained('kiosk_machines')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Check Results (Matching UI Steps)
            $table->boolean('connection_passed')->default(true);
            $table->boolean('paper_passed')->default(true);
            $table->boolean('toner_passed')->default(true);
            $table->boolean('settings_passed')->default(true);
            $table->boolean('is_ready_to_print')->default(true);

            // Error details if any failed
            $table->string('failed_step')->nullable(); // paper, toner, connection, settings
            $table->json('error_message')->nullable(); // {"ar": "لا تحتوي الماكينة على ورق A4 حالياً...", "en": "Machine out of A4 paper..."}
            
            // Snapshot of telemetry at check moment
            $table->json('telemetry_snapshot')->nullable(); // Paper counts, toner percentages

            $table->timestamps();

            $table->index(['kiosk_machine_id', 'is_ready_to_print']);
        });

        Schema::create('kiosk_maintenance_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kiosk_machine_id')->constrained('kiosk_machines')->cascadeOnDelete();
            $table->enum('alert_type', ['out_of_paper', 'low_paper', 'low_toner', 'paper_jam', 'door_open', 'offline']);
            $table->enum('severity', ['info', 'warning', 'critical'])->default('warning');
            $table->json('message');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_maintenance_alerts');
        Schema::dropIfExists('kiosk_health_checks');
    }
};
