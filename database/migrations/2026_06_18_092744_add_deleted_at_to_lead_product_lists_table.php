<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_product_lists', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (Schema::hasColumn('lead_product_lists', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};