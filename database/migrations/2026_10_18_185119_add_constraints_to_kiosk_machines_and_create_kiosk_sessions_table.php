<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kiosk_machines', function (Blueprint $table) {
            $table->unsignedInteger('max_file_size_mb')->nullable()->after('supported_paper_sizes');
            $table->decimal('min_order_price', 8, 2)->nullable()->after('max_file_size_mb');
            $table->unsignedInteger('max_sheets_per_job')->nullable()->after('min_order_price');
        });

        Schema::create('kiosk_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kiosk_machine_id')->constrained('kiosk_machines')->cascadeOnDelete();
            $table->string('session_token')->unique();
            $table->string('client_ip')->nullable();
            $table->enum('status', [
                'waiting_scan',
                'connected',
                'file_uploaded',
                'awaiting_payment',
                'printing',
                'completed',
                'cancelled',
                'expired',
            ])->default('waiting_scan');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->unsignedInteger('detected_page_count')->default(0);
            $table->unsignedInteger('copies_count')->default(1);
            $table->unsignedInteger('total_sheets')->default(0);
            $table->string('paper_size')->default('A4');
            $table->string('color_mode')->default('black_and_white');
            $table->string('side_mode')->default('single_sided');
            $table->decimal('total_price', 8, 2)->default(0);
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['kiosk_machine_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_sessions');

        Schema::table('kiosk_machines', function (Blueprint $table) {
            $table->dropColumn(['max_file_size_mb', 'min_order_price', 'max_sheets_per_job']);
        });
    }
};
