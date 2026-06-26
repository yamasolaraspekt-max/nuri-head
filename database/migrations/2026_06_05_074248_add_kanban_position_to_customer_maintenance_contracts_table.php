<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_maintenance_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_maintenance_contracts', 'kanban_position')) {
                $table->unsignedInteger('kanban_position')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_maintenance_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('customer_maintenance_contracts', 'kanban_position')) {
                $table->dropColumn('kanban_position');
            }
        });
    }
};
