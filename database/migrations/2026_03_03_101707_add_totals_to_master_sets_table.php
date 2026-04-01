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
        Schema::table('master_sets', function (Blueprint $table) {
            $table->decimal('main_total', 12, 2)->default(0)->after('status');
            $table->decimal('sub_total', 12, 2)->default(0)->after('main_total');
            $table->decimal('labor_total', 12, 2)->default(0)->after('sub_total');
            $table->decimal('total', 12, 2)->default(0)->after('labor_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('master_sets', function (Blueprint $table) {
            if (Schema::hasColumn('main_total', 'sub_total', 'labor_total', 'total')) {
                $table->dropColumn('main_total', 'sub_total', 'labor_total', 'total');
            }
        });
    }
};
