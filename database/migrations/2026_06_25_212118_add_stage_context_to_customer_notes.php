<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_notes', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->after('stage')->index();
            }

            if (!Schema::hasColumn('customer_notes', 'lead_stage_key')) {
                $table->string('lead_stage_key', 100)->nullable()->after('lead_stage_id')->index();
            }

            if (!Schema::hasColumn('customer_notes', 'lead_stage_name')) {
                $table->string('lead_stage_name')->nullable()->after('lead_stage_key');
            }

            if (!Schema::hasColumn('customer_notes', 'lead_stage_color')) {
                $table->string('lead_stage_color', 30)->nullable()->after('lead_stage_name');
            }

            if (!Schema::hasColumn('customer_notes', 'lead_stage_sub_stage_id')) {
                $table->unsignedBigInteger('lead_stage_sub_stage_id')->nullable()->after('lead_stage_color')->index();
            }

            if (!Schema::hasColumn('customer_notes', 'lead_stage_sub_stage_name')) {
                $table->string('lead_stage_sub_stage_name')->nullable()->after('lead_stage_sub_stage_id');
            }

            if (!Schema::hasColumn('customer_notes', 'lead_stage_sub_stage_color')) {
                $table->string('lead_stage_sub_stage_color', 30)->nullable()->after('lead_stage_sub_stage_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_notes', function (Blueprint $table) {
            foreach ([
                'lead_stage_sub_stage_color',
                'lead_stage_sub_stage_name',
                'lead_stage_sub_stage_id',
                'lead_stage_color',
                'lead_stage_name',
                'lead_stage_key',
                'lead_stage_id',
            ] as $column) {
                if (Schema::hasColumn('customer_notes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
