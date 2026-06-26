<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employee_recurring_leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_recurring_leaves', 'event_kind')) {
                $table->string('event_kind', 40)->default('absence')->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_recurring_leaves', function (Blueprint $table) {
            if (Schema::hasColumn('employee_recurring_leaves', 'event_kind')) {
                $table->dropColumn('event_kind');
            }
        });
    }
};
