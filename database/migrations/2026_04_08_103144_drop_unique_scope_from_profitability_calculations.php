<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function dropForeignIfExists(string $table, string $column): void
    {
        $database = DB::getDatabaseName();

        $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($foreignKey) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$foreignKey}`");
        }
    }

    public function up(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM profitability_calculations"))
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->all();

        if (in_array('profitability_calc_unique_scope', $indexes, true)) {
            Schema::table('profitability_calculations', function (Blueprint $table) {
                $table->dropUnique('profitability_calc_unique_scope');
            });
        }
    }

    public function down(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM profitability_calculations"))
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->all();

        if (! in_array('profitability_calc_unique_scope', $indexes, true)) {
            Schema::table('profitability_calculations', function (Blueprint $table) {
                $table->unique(
                    ['customer_id', 'alternative_id', 'product_id', 'service_id'],
                    'profitability_calc_unique_scope'
                );
            });
        }
    }
};