<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->foreignId('lead_stage_sub_stage_id')
                    ->nullable()
                    ->after('stage')
                    ->constrained('lead_stage_sub_stages')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->dropConstrainedForeignId('lead_stage_sub_stage_id');
            });
        }
    }
};