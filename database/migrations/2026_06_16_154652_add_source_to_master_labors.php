<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('master_set_labors')) {
            return;
        }

        Schema::table('master_set_labors', function (Blueprint $table) {
            if (!Schema::hasColumn('master_set_labors', 'source')) {
                $table->string('source', 30)->nullable()->after('employee_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('master_set_labors')) {
            return;
        }

        Schema::table('master_set_labors', function (Blueprint $table) {
            if (Schema::hasColumn('master_set_labors', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
