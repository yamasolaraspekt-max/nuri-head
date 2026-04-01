<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('maintenance_assets')) {
            return;
        }

        // 1) Drop old FK if old column exists
        if (Schema::hasColumn('maintenance_assets', 'maintenance_contract_id')) {
            try {
                Schema::table('maintenance_assets', function (Blueprint $table) {
                    $table->dropForeign(['maintenance_contract_id']);
                });
            } catch (\Throwable $e) {
                // ignore if foreign key does not exist
            }
        }

        // 2) Rename old column to new column
        if (
            Schema::hasColumn('maintenance_assets', 'maintenance_contract_id') &&
            !Schema::hasColumn('maintenance_assets', 'customer_maintenance_contract_id')
        ) {
            Schema::table('maintenance_assets', function (Blueprint $table) {
                $table->renameColumn('maintenance_contract_id', 'customer_maintenance_contract_id');
            });
        }

        // 3) Add new FK only if target table exists
        if (
            Schema::hasColumn('maintenance_assets', 'customer_maintenance_contract_id') &&
            Schema::hasTable('customer_maintenance_contracts')
        ) {
            try {
                Schema::table('maintenance_assets', function (Blueprint $table) {
                    $table->foreign('customer_maintenance_contract_id')
                        ->references('id')
                        ->on('customer_maintenance_contracts')
                        ->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // ignore if foreign key already exists
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('maintenance_assets')) {
            return;
        }

        // 1) Drop new FK
        if (Schema::hasColumn('maintenance_assets', 'customer_maintenance_contract_id')) {
            try {
                Schema::table('maintenance_assets', function (Blueprint $table) {
                    $table->dropForeign(['customer_maintenance_contract_id']);
                });
            } catch (\Throwable $e) {
                // ignore if foreign key does not exist
            }
        }

        // 2) Rename column back
        if (
            Schema::hasColumn('maintenance_assets', 'customer_maintenance_contract_id') &&
            !Schema::hasColumn('maintenance_assets', 'maintenance_contract_id')
        ) {
            Schema::table('maintenance_assets', function (Blueprint $table) {
                $table->renameColumn('customer_maintenance_contract_id', 'maintenance_contract_id');
            });
        }

        // 3) Restore old FK only if target table exists
        if (
            Schema::hasColumn('maintenance_assets', 'maintenance_contract_id') &&
            Schema::hasTable('maintenance_contracts')
        ) {
            try {
                Schema::table('maintenance_assets', function (Blueprint $table) {
                    $table->foreign('maintenance_contract_id')
                        ->references('id')
                        ->on('maintenance_contracts')
                        ->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // ignore if foreign key already exists
            }
        }
    }
};