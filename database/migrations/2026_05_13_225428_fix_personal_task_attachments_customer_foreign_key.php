<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    private function dropForeignIfExists(string $table, string $column): void
    {
        $database = DB::getDatabaseName();

        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$database, $table, $column]);

        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
    }

    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Drop old customer_id foreign key safely
        |--------------------------------------------------------------------------
        */
        $this->dropForeignIfExists('personal_task_attachments', 'customer_id');

        /*
        |--------------------------------------------------------------------------
        | Cleanup invalid customer_id values
        |--------------------------------------------------------------------------
        | Keep only IDs that exist in new_leads.
        |--------------------------------------------------------------------------
        */
        DB::table('personal_task_attachments')
            ->whereNotNull('customer_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('new_leads')
                    ->whereColumn('new_leads.id', 'personal_task_attachments.customer_id');
            })
            ->update(['customer_id' => null]);

        /*
        |--------------------------------------------------------------------------
        | Add new FK to new_leads
        |--------------------------------------------------------------------------
        */
        Schema::table('personal_task_attachments', function (Blueprint $table) {
            $table->foreign('customer_id', 'pta_customer_id_new_leads_fk')
                ->references('id')
                ->on('new_leads')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Drop current FK safely
        |--------------------------------------------------------------------------
        */
        $this->dropForeignIfExists('personal_task_attachments', 'customer_id');

        /*
        |--------------------------------------------------------------------------
        | Optional cleanup before restoring customers FK
        |--------------------------------------------------------------------------
        */
        DB::table('personal_task_attachments')
            ->whereNotNull('customer_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('customers')
                    ->whereColumn('customers.id', 'personal_task_attachments.customer_id');
            })
            ->update(['customer_id' => null]);

        /*
        |--------------------------------------------------------------------------
        | Restore old FK to customers
        |--------------------------------------------------------------------------
        */
        Schema::table('personal_task_attachments', function (Blueprint $table) {
            $table->foreign('customer_id', 'pta_customer_id_customers_fk')
                ->references('id')
                ->on('customers')
                ->nullOnDelete();
        });
    }
};