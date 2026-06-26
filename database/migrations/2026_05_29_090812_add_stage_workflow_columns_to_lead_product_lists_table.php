<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_product_lists', 'stage_mode')) {
                $table->string('stage_mode')->default('company')->after('status');
            }

            if (!Schema::hasColumn('lead_product_lists', 'product_stage_id')) {
                $table->foreignId('product_stage_id')
                    ->nullable()
                    ->after('stage_mode')
                    ->constrained('stages')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('lead_product_lists', 'product_task_phase_id')) {
                $table->foreignId('product_task_phase_id')
                    ->nullable()
                    ->after('product_stage_id')
                    ->constrained('task_phases')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (Schema::hasColumn('lead_product_lists', 'product_task_phase_id')) {
                $table->dropConstrainedForeignId('product_task_phase_id');
            }

            if (Schema::hasColumn('lead_product_lists', 'product_stage_id')) {
                $table->dropConstrainedForeignId('product_stage_id');
            }

            if (Schema::hasColumn('lead_product_lists', 'stage_mode')) {
                $table->dropColumn('stage_mode');
            }
        });
    }
};