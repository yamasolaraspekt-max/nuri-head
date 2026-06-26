<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p_v_roofs', function (Blueprint $table) {
            if (!Schema::hasColumn('p_v_roofs', 'shading')) {
                $table->string('shading')->nullable()->after('roof_orientation');
            }

            if (!Schema::hasColumn('p_v_roofs', 'dc_cable_route')) {
                $table->string('dc_cable_route')->nullable()->after('shading');
            }

            if (!Schema::hasColumn('p_v_roofs', 'storage_preference')) {
                $table->string('storage_preference')->nullable()->after('pv_existing');
            }

            if (!Schema::hasColumn('p_v_roofs', 'backup_power')) {
                $table->string('backup_power')->nullable()->after('storage_preference');
            }

            if (!Schema::hasColumn('p_v_roofs', 'pv_investment_costs')) {
                $table->decimal('pv_investment_costs', 12, 2)->nullable()->after('backup_power');
            }
        });
    }

    public function down(): void
    {
        Schema::table('p_v_roofs', function (Blueprint $table) {
            $table->dropColumn([
                'shading',
                'dc_cable_route',
                'storage_preference',
                'backup_power',
                'pv_investment_costs',
            ]);
        });
    }
};