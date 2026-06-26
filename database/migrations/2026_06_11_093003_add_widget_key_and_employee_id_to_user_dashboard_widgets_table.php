<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_dashboard_widgets', function (Blueprint $table) {
            if (!Schema::hasColumn('user_dashboard_widgets', 'widget_key')) {
                $table->string('widget_key', 120)->nullable()->after('dashboard_widget_id')->index();
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('user_id')->index();
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'instance_key')) {
                $table->string('instance_key', 120)->nullable()->after('widget_key')->index();
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'view')) {
                $table->string('view', 40)->default('personal')->after('instance_key')->index();
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'col_span')) {
                $table->unsignedTinyInteger('col_span')->default(4)->after('view');
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'row_span')) {
                $table->unsignedTinyInteger('row_span')->default(4)->after('col_span');
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('row_span');
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'is_visible')) {
                $table->boolean('is_visible')->default(true)->after('sort_order');
            }

            if (!Schema::hasColumn('user_dashboard_widgets', 'config')) {
                $table->json('config')->nullable()->after('is_visible');
            }
        });

        DB::table('user_dashboard_widgets as udw')
            ->join('dashboard_widgets as dw', 'udw.dashboard_widget_id', '=', 'dw.id')
            ->whereNull('udw.widget_key')
            ->update([
                'udw.widget_key' => DB::raw('dw.`key`'),
            ]);
    }

    public function down(): void
    {
        Schema::table('user_dashboard_widgets', function (Blueprint $table) {
            if (Schema::hasColumn('user_dashboard_widgets', 'widget_key')) {
                $table->dropColumn('widget_key');
            }

            if (Schema::hasColumn('user_dashboard_widgets', 'employee_id')) {
                $table->dropColumn('employee_id');
            }
        });
    }
};