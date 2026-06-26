<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employee_sicks', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_sicks', 'start_time')) {
                $table->time('start_time')->nullable()->after('end_date');
            }

            if (!Schema::hasColumn('employee_sicks', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_sicks', function (Blueprint $table) {
            if (Schema::hasColumn('employee_sicks', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('employee_sicks', 'end_time')) {
                $table->dropColumn('end_time');
            }
        });
    }
};