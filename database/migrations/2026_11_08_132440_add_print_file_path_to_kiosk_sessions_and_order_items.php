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
        if (Schema::hasTable('kiosk_sessions') && ! Schema::hasColumn('kiosk_sessions', 'print_file_path')) {
            Schema::table('kiosk_sessions', function (Blueprint $table) {
                $table->string('print_file_path')->nullable()->after('file_path');
            });
        }

        if (Schema::hasTable('order_items') && ! Schema::hasColumn('order_items', 'print_file_path')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('print_file_path')->nullable()->after('file_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kiosk_sessions') && Schema::hasColumn('kiosk_sessions', 'print_file_path')) {
            Schema::table('kiosk_sessions', function (Blueprint $table) {
                $table->dropColumn('print_file_path');
            });
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'print_file_path')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('print_file_path');
            });
        }
    }
};
