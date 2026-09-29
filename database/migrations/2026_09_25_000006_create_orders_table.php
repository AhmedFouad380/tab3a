<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('order_type', ['self_printing', 'pre_order']);
            
            // Locations & Execution Targets
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('kiosk_machine_id')->nullable()->constrained('kiosk_machines')->nullOnDelete();
            
            // Scheduling for Pre-Orders
            $table->dateTime('scheduled_pickup_at')->nullable();
            $table->dateTime('actual_pickup_at')->nullable();

            // Status Management
            $table->string('status')->default('pending');
            // Self Print: pending, paired, printing, completed, failed, cancelled
            // Pre-Order: pending, processing, printing, ready_for_pickup, completed, cancelled

            $table->enum('payment_status', ['unpaid', 'paid', 'refunded', 'failed'])->default('unpaid');
            $table->string('payment_method')->nullable(); // card, apple_pay, wallet, cash_at_branch

            // Financial Summary
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);

            // Job Execution metadata (for Kiosks)
            $table->string('printer_job_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('user_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'order_type', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index(['kiosk_machine_id', 'status']);
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('status');
            $table->json('comment')->nullable(); // Multi-language message
            $table->string('created_by_type')->nullable(); // user, admin, branch_staff, kiosk_agent
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('orders');
    }
};
