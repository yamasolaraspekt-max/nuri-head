<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('problems')) {
            Schema::table('problems', function (Blueprint $table) {
                if (!Schema::hasColumn('problems', 'lead_product_list_id')) {
                    $table->unsignedBigInteger('lead_product_list_id')->nullable()->after('product_id')->index();
                }

                if (!Schema::hasColumn('problems', 'lead_stage_id')) {
                    $table->unsignedBigInteger('lead_stage_id')->nullable()->after('lead_product_list_id')->index();
                }

                if (!Schema::hasColumn('problems', 'lead_stage_sub_stage_id')) {
                    $table->unsignedBigInteger('lead_stage_sub_stage_id')->nullable()->after('lead_stage_id')->index();
                }
            });
        }

        if (Schema::hasTable('ticket_tasks')) {
            Schema::table('ticket_tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('ticket_tasks', 'lead_product_list_id')) {
                    $table->unsignedBigInteger('lead_product_list_id')->nullable()->after('appointment_id')->index();
                }

                if (!Schema::hasColumn('ticket_tasks', 'lead_stage_id')) {
                    $table->unsignedBigInteger('lead_stage_id')->nullable()->after('lead_product_list_id')->index();
                }

                if (!Schema::hasColumn('ticket_tasks', 'lead_stage_sub_stage_id')) {
                    $table->unsignedBigInteger('lead_stage_sub_stage_id')->nullable()->after('lead_stage_id')->index();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ticket_tasks')) {
            Schema::table('ticket_tasks', function (Blueprint $table) {
                foreach (['lead_stage_sub_stage_id', 'lead_stage_id', 'lead_product_list_id'] as $column) {
                    if (Schema::hasColumn('ticket_tasks', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('problems')) {
            Schema::table('problems', function (Blueprint $table) {
                foreach (['lead_stage_sub_stage_id', 'lead_stage_id', 'lead_product_list_id'] as $column) {
                    if (Schema::hasColumn('problems', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
