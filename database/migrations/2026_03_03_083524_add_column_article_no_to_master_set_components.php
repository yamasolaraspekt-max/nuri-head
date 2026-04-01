<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_set_components', function (Blueprint $table) {
            if (!Schema::hasColumn('master_set_components', 'article_no')) {
                $table->string('article_no')->nullable()->after('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_set_components', function (Blueprint $table) {
            if (Schema::hasColumn('master_set_components', 'article_no')) {
                $table->dropColumn('article_no');
            }
        });
    }
};