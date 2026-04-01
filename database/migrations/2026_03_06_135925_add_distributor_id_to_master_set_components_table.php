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
            if (!Schema::hasColumn('master_set_components', 'distributor_article_no')) {
                $table->string('distributor_article_no')->nullable()->after('article_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('costing_sets', function (Blueprint $table) { 
         if (Schema::hasColumn('distributor_article_no'))  $table->dropColumn('distributor_article_no');
             });
    }
};
