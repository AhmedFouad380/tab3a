<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('service_type', ['self_printing', 'pre_order', 'all'])->default('all');
            $table->string('paper_size', 10)->default('A4'); // A4, A3
            $table->enum('color_mode', ['black_and_white', 'color'])->default('black_and_white');
            $table->enum('side_mode', ['single_sided', 'double_sided'])->default('single_sided');
            $table->string('paper_type')->default('plain_80g'); // plain_80g, glossy_150g, cardstock_300g
            $table->decimal('price_per_page', 8, 2)->default(0.50); // SAR or EGP
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_type', 'paper_size', 'color_mode', 'side_mode']);
        });

        Schema::create('finishing_options', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"ar": "تجليد حلزوني سلك", "en": "Spiral Binding"}
            $table->json('description')->nullable();
            $table->string('code')->unique(); // e.g. spiral_binding, stapling, laminating
            $table->decimal('base_price', 8, 2)->default(0.00);
            $table->boolean('available_for_self_print')->default(false);
            $table->boolean('available_for_pre_order')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finishing_options');
        Schema::dropIfExists('pricing_rules');
    }
};
