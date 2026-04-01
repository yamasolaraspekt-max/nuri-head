<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Pick a safe "after" column that exists on the table.
     * If none exists, return null (we won't use ->after()).
     */
    private function pickAfterColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col)) {
                return $col;
            }
        }
        return null;
    }

    public function up(): void
    {
        Schema::table('customer_maintenance_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_maintenance_contracts', 'responsible_employee_id')) {

                // choose a real existing column instead of maintenance_checklist_id
                $after = $this->pickAfterColumn('customer_maintenance_contracts', [
                    'asset_id',
                    'alternative_id',
                    'lead_id',
                    'contract_no',
                    'title',
                    'id',
                ]);

                $col = $table->unsignedBigInteger('responsible_employee_id')->nullable();
                if ($after) {
                    $col->after($after);
                }

                $table->foreign('responsible_employee_id')
                    ->references('id')
                    ->on('employees')
                    ->nullOnDelete();

                $table->index('responsible_employee_id');
            }
        });

        Schema::table('maintenance_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_assets', 'responsible_employee_id')) {

                // maintenance_assets likely DOES have maintenance_checklist_id, but keep it safe anyway
                $after = $this->pickAfterColumn('maintenance_assets', [
                    'maintenance_checklist_id',
                    'product_id',
                    'lead_product_list_id',
                    'alternative_id',
                    'lead_id',
                    'id',
                ]);

                $col = $table->unsignedBigInteger('responsible_employee_id')->nullable();
                if ($after) {
                    $col->after($after);
                }

                $table->foreign('responsible_employee_id')
                    ->references('id')
                    ->on('employees')
                    ->nullOnDelete();

                $table->index('responsible_employee_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_assets', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_assets', 'responsible_employee_id')) {
                $table->dropForeign(['responsible_employee_id']);
                $table->dropIndex(['responsible_employee_id']);
                $table->dropColumn('responsible_employee_id');
            }
        });

        Schema::table('customer_maintenance_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('customer_maintenance_contracts', 'responsible_employee_id')) {
                $table->dropForeign(['responsible_employee_id']);
                $table->dropIndex(['responsible_employee_id']);
                $table->dropColumn('responsible_employee_id');
            }
        });
    }
};
