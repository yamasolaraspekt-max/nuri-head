<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('planner_item_dependencies', function (Blueprint $table) {
            $table->string('reason', 255)->nullable()->after('depends_on_item_id');
            // if your table uses a different column name, change the `after(...)` target accordingly
        });
    }

    public function down(): void
    {
        Schema::table('planner_item_dependencies', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
