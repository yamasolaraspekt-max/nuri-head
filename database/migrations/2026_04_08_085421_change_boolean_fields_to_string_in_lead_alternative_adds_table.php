<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Relax session SQL mode so legacy zero-dates can be read and cleaned
        DB::statement("SET SESSION sql_mode = REPLACE(@@SESSION.sql_mode, 'NO_ZERO_DATE', '')");
        DB::statement("SET SESSION sql_mode = REPLACE(@@SESSION.sql_mode, 'NO_ZERO_IN_DATE', '')");
        DB::statement("SET SESSION sql_mode = REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', '')");
        DB::statement("SET SESSION sql_mode = REPLACE(@@SESSION.sql_mode, 'STRICT_ALL_TABLES', '')");

        // Clean invalid legacy date/datetime values first
        DB::statement("
            UPDATE lead_alternative_adds
            SET
                request_date = CASE
                    WHEN request_date = '0000-00-00 00:00:00' THEN NULL
                    ELSE request_date
                END,
                appointment = CASE
                    WHEN appointment = '0000-00-00' THEN NULL
                    ELSE appointment
                END,
                project_date = CASE
                    WHEN project_date = '0000-00-00' THEN NULL
                    ELSE project_date
                END,
                created_at = CASE
                    WHEN created_at = '0000-00-00 00:00:00' THEN NULL
                    ELSE created_at
                END,
                updated_at = CASE
                    WHEN updated_at = '0000-00-00 00:00:00' THEN NULL
                    ELSE updated_at
                END,
                deleted_at = CASE
                    WHEN deleted_at = '0000-00-00 00:00:00' THEN NULL
                    ELSE deleted_at
                END
        ");

        Schema::table('lead_alternative_adds', function (Blueprint $table) {
            $table->string('natural_refrigerant')->nullable()->change();
            $table->string('has_pump_upgrade')->nullable()->change();
            $table->string('hydraulic_only')->nullable()->change();
            $table->string('solar_thermal')->nullable()->change();
            $table->string('solar_thermal_simulation')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lead_alternative_adds', function (Blueprint $table) {
            $table->boolean('natural_refrigerant')->nullable()->change();
            $table->boolean('has_pump_upgrade')->nullable()->change();
            $table->boolean('hydraulic_only')->nullable()->change();
            $table->boolean('solar_thermal')->nullable()->change();
            $table->boolean('solar_thermal_simulation')->nullable()->change();
        });
    }
};