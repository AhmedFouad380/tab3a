<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            
            // File Metadata
            $table->string('original_file_name');
            $table->string('file_path');
            $table->string('file_extension', 10); // pdf, png, jpg
            $table->unsignedBigInteger('file_size_bytes');
            $table->integer('detected_page_count')->default(1); // Read automatically via pdf parser
            $table->integer('pages_to_print_count')->default(1); // After applying range
            $table->string('page_range_selection')->default('all'); // 'all', '1-5', '3,7,9'
            
            // Print Configuration
            $table->string('paper_size', 10)->default('A4');
            $table->string('paper_type')->default('plain_80g');
            $table->enum('color_mode', ['black_and_white', 'color'])->default('black_and_white');
            $table->enum('side_mode', ['single_sided', 'double_sided'])->default('single_sided');
            $table->enum('orientation', ['auto', 'portrait', 'landscape'])->default('auto');
            $table->unsignedSmallInteger('copies_count')->default(1);
            
            // Finishing & Binding (Optional)
            $table->foreignId('finishing_option_id')->nullable()->constrained('finishing_options')->nullOnDelete();
            $table->decimal('finishing_price', 8, 2)->default(0.00);

            // Calculations
            $table->integer('total_sheets_needed')->default(1); // (pages / 2 if double-sided) * copies
            $table->decimal('unit_price_per_page', 8, 2)->default(0.00);
            $table->decimal('total_item_price', 10, 2)->default(0.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
