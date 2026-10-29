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
        Schema::table('branch_working_hours', function (Blueprint $table) {
            $table->index(['branch_id', 'day_of_week'], 'branch_working_hours_branch_id_day_of_week_index');
            $table->dropUnique('branch_working_hours_branch_id_day_of_week_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_working_hours', function (Blueprint $table) {
            $table->unique(['branch_id', 'day_of_week'], 'branch_working_hours_branch_id_day_of_week_unique');
            $table->dropIndex('branch_working_hours_branch_id_day_of_week_index');
        });
    }
};
