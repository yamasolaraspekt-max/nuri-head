<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('main_appointments') && !Schema::hasColumn('main_appointments', 'planner_item_id')) {
            Schema::table('main_appointments', function (Blueprint $table) {
                $table->unsignedBigInteger('planner_item_id')->nullable()->index()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('main_appointments') && Schema::hasColumn('main_appointments', 'planner_item_id')) {
            Schema::table('main_appointments', function (Blueprint $table) {
                $table->dropColumn('planner_item_id');
            });
        }
    }
};
