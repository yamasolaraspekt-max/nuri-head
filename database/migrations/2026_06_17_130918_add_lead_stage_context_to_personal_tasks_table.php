<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_tasks', 'lead_product_list_id')) {
                $table->unsignedBigInteger('lead_product_list_id')
                    ->nullable()
                    ->after('product_id')
                    ->index();
            }

            if (!Schema::hasColumn('personal_tasks', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')
                    ->nullable()
                    ->after('lead_product_list_id')
                    ->index();
            }

            if (!Schema::hasColumn('personal_tasks', 'lead_stage_sub_stage_id')) {
                $table->unsignedBigInteger('lead_stage_sub_stage_id')
                    ->nullable()
                    ->after('lead_stage_id')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('personal_tasks', 'lead_stage_sub_stage_id')) {
                $table->dropColumn('lead_stage_sub_stage_id');
            }

            if (Schema::hasColumn('personal_tasks', 'lead_stage_id')) {
                $table->dropColumn('lead_stage_id');
            }

            if (Schema::hasColumn('personal_tasks', 'lead_product_list_id')) {
                $table->dropColumn('lead_product_list_id');
            }
        });
    }
};