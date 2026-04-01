<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    private function hasIndexOnColumn(string $table, string $column): bool
    {
        $db = DB::getDatabaseName();

        $rows = DB::select(
            "SELECT 1
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND column_name = ?
             LIMIT 1",
            [$db, $table, $column]
        );

        return !empty($rows);
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        $db = DB::getDatabaseName();

        $rows = DB::select(
            "SELECT 1
             FROM information_schema.key_column_usage
             WHERE table_schema = ?
               AND table_name = ?
               AND column_name = ?
               AND referenced_table_name IS NOT NULL
             LIMIT 1",
            [$db, $table, $column]
        );

        return !empty($rows);
    }

    public function up(): void
    {
        if (!Schema::hasTable('planner_items')) {
            return;
        }

        // 1) Add columns (NO “ensure index” inside same closure to avoid duplicates)
        Schema::table('planner_items', function (Blueprint $table) {

            if (!Schema::hasColumn('planner_items', 'meta')) {
                $table->json('meta')->nullable();
            }

            if (!Schema::hasColumn('planner_items', 'status')) {
                $table->string('status')->default('open'); // index added later safely
            }

            if (!Schema::hasColumn('planner_items', 'started_at')) {
                $table->timestamp('started_at')->nullable();
            }

            if (!Schema::hasColumn('planner_items', 'paused_at')) {
                $table->timestamp('paused_at')->nullable();
            }

            if (!Schema::hasColumn('planner_items', 'stopped_at')) {
                $table->timestamp('stopped_at')->nullable();
            }

            if (!Schema::hasColumn('planner_items', 'last_status_changed_at')) {
                $table->timestamp('last_status_changed_at')->nullable();
            }

            // legacy (keep)
            if (!Schema::hasColumn('planner_items', 'pause_reason')) {
                $table->text('pause_reason')->nullable();
            }

            // unified reason for play/pause/stop
            if (!Schema::hasColumn('planner_items', 'last_status_reason')) {
                $table->text('last_status_reason')->nullable();
            }

            // who changed status
            if (!Schema::hasColumn('planner_items', 'last_status_changed_by_employee_id')) {
                $table->unsignedBigInteger('last_status_changed_by_employee_id')->nullable();
            }
        });

        // 2) Add indexes safely (checks against information_schema)
        // status index safeguard (fixes your Duplicate key error)
        if (Schema::hasColumn('planner_items', 'status') && !$this->hasIndexOnColumn('planner_items', 'status')) {
            Schema::table('planner_items', function (Blueprint $table) {
                $table->index('status');
            });
        }

        foreach (['started_at','paused_at','stopped_at','last_status_changed_at','last_status_changed_by_employee_id'] as $col) {
            if (Schema::hasColumn('planner_items', $col) && !$this->hasIndexOnColumn('planner_items', $col)) {
                Schema::table('planner_items', function (Blueprint $table) use ($col) {
                    $table->index($col);
                });
            }
        }

        // 3) Add FK safely
        if (
            Schema::hasTable('employees') &&
            Schema::hasColumn('planner_items', 'last_status_changed_by_employee_id') &&
            !$this->hasForeignKey('planner_items', 'last_status_changed_by_employee_id')
        ) {
            Schema::table('planner_items', function (Blueprint $table) {
                $table->foreign('last_status_changed_by_employee_id')
                    ->references('id')->on('employees')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('planner_items')) {
            return;
        }

        // drop FK safely
        if (Schema::hasColumn('planner_items', 'last_status_changed_by_employee_id')) {
            try {
                Schema::table('planner_items', function (Blueprint $table) {
                    $table->dropForeign(['last_status_changed_by_employee_id']);
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }

        Schema::table('planner_items', function (Blueprint $table) {
            $cols = [
                'started_at',
                'paused_at',
                'stopped_at',
                'pause_reason',
                'last_status_changed_at',
                'last_status_reason',
                'last_status_changed_by_employee_id',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('planner_items', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Keep meta/status by default (commonly used elsewhere).
            // Uncomment if you want them removed too:
            // if (Schema::hasColumn('planner_items', 'meta')) $table->dropColumn('meta');
            // if (Schema::hasColumn('planner_items', 'status')) $table->dropColumn('status');
        });
    }
};
