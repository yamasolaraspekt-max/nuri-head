<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('lead_product_lists') && !Schema::hasColumn('lead_product_lists', 'deleted_at')) {
            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lead_product_lists') && Schema::hasColumn('lead_product_lists', 'deleted_at')) {
            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
