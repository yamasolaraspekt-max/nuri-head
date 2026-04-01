<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        $row = DB::selectOne("
            SELECT 1
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = ?
              AND index_name = ?
            LIMIT 1
        ", [$table, $indexName]);

        return (bool) $row;
    }

    private function foreignKeyExists(string $table, string $fkName): bool
    {
        $row = DB::selectOne("
            SELECT 1
            FROM information_schema.table_constraints
            WHERE constraint_schema = DATABASE()
              AND table_name = ?
              AND constraint_name = ?
              AND constraint_type = 'FOREIGN KEY'
            LIMIT 1
        ", [$table, $fkName]);

        return (bool) $row;
    }

    public function up(): void
    {
        // ----------------------------------------------------
        // ✅ Cleanup invalid zero-dates / zero-datetimes first
        // ----------------------------------------------------
        if (Schema::hasTable('personal_tasks')) {
            DB::table('personal_tasks')->where('reminder_date', '0000-00-00')->update(['reminder_date' => null]);
            DB::table('personal_tasks')->where('start_date',    '0000-00-00')->update(['start_date'    => null]);
            DB::table('personal_tasks')->where('due_date',      '0000-00-00')->update(['due_date'      => null]);

            DB::table('personal_tasks')->where('created_at', '0000-00-00 00:00:00')->update(['created_at' => DB::raw('NOW()')]);
            DB::table('personal_tasks')->where('updated_at', '0000-00-00 00:00:00')->update(['updated_at' => DB::raw('NOW()')]);
            DB::table('personal_tasks')->where('deleted_at', '0000-00-00 00:00:00')->update(['deleted_at' => null]);
        }

        if (Schema::hasTable('problems')) {
            DB::table('problems')->where('installation_date', '0000-00-00')->update(['installation_date' => null]);
            DB::table('problems')->where('date',              '0000-00-00')->update(['date'              => null]);
            DB::table('problems')->where('progress_date',     '0000-00-00')->update(['progress_date'     => null]);
            DB::table('problems')->where('end_date',          '0000-00-00')->update(['end_date'          => null]);
            DB::table('problems')->where('edit_date',         '0000-00-00')->update(['edit_date'         => null]);

            DB::table('problems')->where('created_at', '0000-00-00 00:00:00')->update(['created_at' => DB::raw('NOW()')]);
            DB::table('problems')->where('updated_at', '0000-00-00 00:00:00')->update(['updated_at' => DB::raw('NOW()')]);
            DB::table('problems')->where('deleted_at', '0000-00-00 00:00:00')->update(['deleted_at' => null]);
        }

        // ----------------------------------------------------
        // personal_tasks: add planner_id if missing
        // ----------------------------------------------------
        Schema::table('personal_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_tasks', 'planner_id')) {
                $table->unsignedBigInteger('planner_id')->nullable()->after('id');
            }
        });

        // Add index only if missing
        if (!$this->indexExists('personal_tasks', 'personal_tasks_planner_id_idx')) {
            Schema::table('personal_tasks', function (Blueprint $table) {
                $table->index('planner_id', 'personal_tasks_planner_id_idx');
            });
        }

        // Add FK only if missing
        if (!$this->foreignKeyExists('personal_tasks', 'personal_tasks_planner_id_fk')) {
            Schema::table('personal_tasks', function (Blueprint $table) {
                $table->foreign('planner_id', 'personal_tasks_planner_id_fk')
                    ->references('id')
                    ->on('planner_plans')
                    ->nullOnDelete();
            });
        }

        // ----------------------------------------------------
        // problems: add planner_id if missing
        // ----------------------------------------------------
        Schema::table('problems', function (Blueprint $table) {
            if (!Schema::hasColumn('problems', 'planner_id')) {
                $table->unsignedBigInteger('planner_id')->nullable()->after('id');
            }
        });

        // Add index only if missing
        if (!$this->indexExists('problems', 'problems_planner_id_idx')) {
            Schema::table('problems', function (Blueprint $table) {
                $table->index('planner_id', 'problems_planner_id_idx');
            });
        }

        // Add FK only if missing
        if (!$this->foreignKeyExists('problems', 'problems_planner_id_fk')) {
            Schema::table('problems', function (Blueprint $table) {
                $table->foreign('planner_id', 'problems_planner_id_fk')
                    ->references('id')
                    ->on('planner_plans')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('problems') && Schema::hasColumn('problems', 'planner_id')) {
            if ($this->foreignKeyExists('problems', 'problems_planner_id_fk')) {
                Schema::table('problems', function (Blueprint $table) {
                    $table->dropForeign('problems_planner_id_fk');
                });
            }
            if ($this->indexExists('problems', 'problems_planner_id_idx')) {
                Schema::table('problems', function (Blueprint $table) {
                    $table->dropIndex('problems_planner_id_idx');
                });
            }
            Schema::table('problems', function (Blueprint $table) {
                $table->dropColumn('planner_id');
            });
        }

        if (Schema::hasTable('personal_tasks') && Schema::hasColumn('personal_tasks', 'planner_id')) {
            if ($this->foreignKeyExists('personal_tasks', 'personal_tasks_planner_id_fk')) {
                Schema::table('personal_tasks', function (Blueprint $table) {
                    $table->dropForeign('personal_tasks_planner_id_fk');
                });
            }
            if ($this->indexExists('personal_tasks', 'personal_tasks_planner_id_idx')) {
                Schema::table('personal_tasks', function (Blueprint $table) {
                    $table->dropIndex('personal_tasks_planner_id_idx');
                });
            }
            Schema::table('personal_tasks', function (Blueprint $table) {
                $table->dropColumn('planner_id');
            });
        }
    }
};
