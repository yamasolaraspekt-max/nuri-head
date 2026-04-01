<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_set_components', function (Blueprint $table) {
            $table->string('price_unit')->default('stk')->after('vpe');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_sets', function (Blueprint $table) {
            if (Schema::hasColumn('price_unit')) {
                $table->dropColumn('price_unit');
            }
        });
    }
};
