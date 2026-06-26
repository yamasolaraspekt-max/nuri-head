<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_product_lists', 'offer_team_access_mode')) {
                $table->string('offer_team_access_mode', 30)
                    ->default('all')
                    ->after('teams')
                    ->comment('all = all employees, team_only = only assigned offer team');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (Schema::hasColumn('lead_product_lists', 'offer_team_access_mode')) {
                $table->dropColumn('offer_team_access_mode');
            }
        });
    }
};
