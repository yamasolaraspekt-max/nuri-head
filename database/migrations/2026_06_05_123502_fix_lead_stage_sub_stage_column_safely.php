<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->unsignedBigInteger('lead_stage_sub_stage_id')
                    ->nullable()
                    ->after('stage');
            });
        }

        if (Schema::hasTable('lead_stage_sub_stages') && Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            $fkExists = collect(DB::select("\n                SELECT CONSTRAINT_NAME\n                FROM information_schema.KEY_COLUMN_USAGE\n                WHERE TABLE_SCHEMA = DATABASE()\n                  AND TABLE_NAME = 'lead_product_lists'\n                  AND COLUMN_NAME = 'lead_stage_sub_stage_id'\n                  AND REFERENCED_TABLE_NAME IS NOT NULL\n            "))->isNotEmpty();

            if (!$fkExists) {
                Schema::table('lead_product_lists', function (Blueprint $table) {
                    $table->foreign('lead_stage_sub_stage_id', 'lpl_lead_stage_sub_stage_fk')
                        ->references('id')
                        ->on('lead_stage_sub_stages')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            $fkName = collect(DB::select("\n                SELECT CONSTRAINT_NAME\n                FROM information_schema.KEY_COLUMN_USAGE\n                WHERE TABLE_SCHEMA = DATABASE()\n                  AND TABLE_NAME = 'lead_product_lists'\n                  AND COLUMN_NAME = 'lead_stage_sub_stage_id'\n                  AND REFERENCED_TABLE_NAME IS NOT NULL\n            "))->pluck('CONSTRAINT_NAME')->first();

            if ($fkName) {
                Schema::table('lead_product_lists', function (Blueprint $table) use ($fkName) {
                    $table->dropForeign($fkName);
                });
            }

            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->dropColumn('lead_stage_sub_stage_id');
            });
        }
    }
};
