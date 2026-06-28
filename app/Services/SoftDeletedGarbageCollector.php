<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SoftDeletedGarbageCollector
{
    protected int $batchSize = 1000;

    public function preview(?Carbon $olderThan = null, bool $all = false, array $onlyTables = []): array
    {
        $tables = $this->tablesWithDeletedAt();

        if (!empty($onlyTables)) {
            $tables = array_values(array_intersect($tables, $onlyTables));
        }

        $tables = $this->orderTablesForSafeDelete($tables);

        $result = [
            'all' => $all,
            'older_than' => $olderThan?->toDateTimeString(),
            'tables' => [],
            'total' => 0,
            'errors' => [],
        ];

        foreach ($tables as $table) {
            try {
                $stats = $this->tableStats($table, $olderThan, $all);

                $result['tables'][$table] = $stats;
                $result['total'] += $stats['count'];
            } catch (Throwable $e) {
                $result['errors'][$table] = $e->getMessage();
            }
        }

        return $result;
    }

    public function purge(?Carbon $olderThan = null, bool $all = false, array $onlyTables = []): array
    {
        $tables = $this->tablesWithDeletedAt();

        if (!empty($onlyTables)) {
            $tables = array_values(array_intersect($tables, $onlyTables));
        }

        $tables = $this->orderTablesForSafeDelete($tables);

        $result = [
            'all' => $all,
            'older_than' => $olderThan?->toDateTimeString(),
            'tables' => [],
            'total' => 0,
            'errors' => [],
        ];

        foreach ($tables as $table) {
            try {
                $deleted = $this->deleteSoftDeleted($table, $olderThan, $all);

                $result['tables'][$table] = $deleted;
                $result['total'] += $deleted;
            } catch (Throwable $e) {
                $result['errors'][$table] = $e->getMessage();
            }
        }

        return $result;
    }

    protected function tablesWithDeletedAt(): array
    {
        $database = DB::getDatabaseName();

        return collect(DB::select("
            SELECT TABLE_NAME AS table_name
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND COLUMN_NAME = 'deleted_at'
        ", [$database]))
            ->pluck('table_name')
            ->filter(fn($table) => Schema::hasTable($table))
            ->values()
            ->all();
    }

    protected function tableStats(string $table, ?Carbon $olderThan, bool $all): array
    {
        $query = DB::table($table)->whereNotNull('deleted_at');

        if (!$all && $olderThan) {
            $query->where('deleted_at', '<=', $olderThan);
        }

        return [
            'count' => (clone $query)->count(),
            'oldest_deleted_at' => (clone $query)->min('deleted_at'),
            'newest_deleted_at' => (clone $query)->max('deleted_at'),
        ];
    }

    protected function deleteSoftDeleted(string $table, ?Carbon $olderThan, bool $all): int
    {
        $totalDeleted = 0;

        do {
            $query = DB::table($table)->whereNotNull('deleted_at');

            if (!$all && $olderThan) {
                $query->where('deleted_at', '<=', $olderThan);
            }

            $deleted = $query->limit($this->batchSize)->delete();

            $totalDeleted += $deleted;
        } while ($deleted > 0);

        return $totalDeleted;
    }

    protected function orderTablesForSafeDelete(array $tables): array
    {
        $database = DB::getDatabaseName();

        $foreignKeys = DB::select("
            SELECT 
                TABLE_NAME AS child_table,
                REFERENCED_TABLE_NAME AS parent_table
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$database]);

        $tableSet = array_flip($tables);
        $childrenByParent = [];

        foreach ($foreignKeys as $fk) {
            $child = $fk->child_table;
            $parent = $fk->parent_table;

            if (isset($tableSet[$child], $tableSet[$parent])) {
                $childrenByParent[$parent][] = $child;
            }
        }

        $visited = [];
        $visiting = [];
        $ordered = [];

        $visit = function (string $table) use (&$visit, &$visited, &$visiting, &$ordered, $childrenByParent) {
            if (isset($visited[$table])) {
                return;
            }

            if (isset($visiting[$table])) {
                return;
            }

            $visiting[$table] = true;

            foreach ($childrenByParent[$table] ?? [] as $child) {
                $visit($child);
            }

            $visited[$table] = true;
            unset($visiting[$table]);

            $ordered[] = $table;
        };

        foreach ($tables as $table) {
            $visit($table);
        }

        return array_values(array_unique($ordered));
    }
}