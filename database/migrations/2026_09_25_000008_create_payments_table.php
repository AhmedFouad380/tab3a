<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('transaction_id')->unique()->nullable();
            $table->string('gateway'); // moyasar, tap, paymob, apple_pay, wallet
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->string('payment_method_type')->nullable(); // mada, visa, mastercard, applepay, wallet
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['transaction_id', 'status']);
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['deposit', 'withdrawal', 'refund', 'payment']);
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->nullableMorphs('reference'); // Reference to Order or Payment
            $table->json('description'); // Multi-lang description
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('payments');
    }
};
