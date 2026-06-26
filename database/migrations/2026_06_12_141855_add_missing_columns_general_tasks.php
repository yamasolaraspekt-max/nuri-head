<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('general_tasks') && !Schema::hasColumn('general_tasks', 'sort_order')) {
            Schema::table('general_tasks', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(1)->after('status')->index();
            });
        }

        if (Schema::hasTable('general_tasks') && Schema::hasColumn('general_tasks', 'sort_order')) {
            DB::statement("\n                UPDATE general_tasks gt\n                JOIN (\n                    SELECT id, ROW_NUMBER() OVER (PARTITION BY status ORDER BY created_at DESC, id DESC) AS row_no\n                    FROM general_tasks\n                ) ordered ON ordered.id = gt.id\n                SET gt.sort_order = ordered.row_no\n                WHERE gt.sort_order IS NULL OR gt.sort_order = 0\n            ");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('general_tasks') && Schema::hasColumn('general_tasks', 'sort_order')) {
            Schema::table('general_tasks', function (Blueprint $table) {
                $table->dropIndex(['sort_order']);
                $table->dropColumn('sort_order');
            });
        }
    }
};
