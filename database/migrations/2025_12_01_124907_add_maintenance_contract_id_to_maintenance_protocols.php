<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_protocols', function (Blueprint $table) {
            if (! Schema::hasColumn('maintenance_protocols', 'maintenance_contract_id')) {
                $table->unsignedBigInteger('maintenance_contract_id')
                      ->nullable()
                      ->after('maintenance_asset_id');

                $table->foreign('maintenance_contract_id', 'mp_contract_fk')
                      ->references('id')
                      ->on('customer_maintenance_contracts')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_protocols', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_protocols', 'maintenance_contract_id')) {
                $table->dropForeign('mp_contract_fk');
                $table->dropColumn('maintenance_contract_id');
            }
        });
    }
};
