<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_protocols', function (Blueprint $table) {
            // Drop old FK to maintenance_contracts
            $table->dropForeign(['maintenance_contract_id']);
        });

        Schema::table('maintenance_protocols', function (Blueprint $table) {
            // Rename column to reflect what it really stores
            $table->renameColumn('maintenance_contract_id', 'customer_maintenance_contract_id');
        });

        Schema::table('maintenance_protocols', function (Blueprint $table) {
            // New FK to customer_maintenance_contracts
            $table->foreign('customer_maintenance_contract_id')
                  ->references('id')
                  ->on('customer_maintenance_contracts')
                  ->nullOnDelete();  // or ->cascadeOnDelete() if you prefer
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_protocols', function (Blueprint $table) {
            $table->dropForeign(['customer_maintenance_contract_id']);
        });

        Schema::table('maintenance_protocols', function (Blueprint $table) {
            $table->renameColumn('customer_maintenance_contract_id', 'maintenance_contract_id');
        });

        Schema::table('maintenance_protocols', function (Blueprint $table) {
            $table->foreign('maintenance_contract_id')
                  ->references('id')
                  ->on('maintenance_contracts')
                  ->nullOnDelete();
        });
    }
};
