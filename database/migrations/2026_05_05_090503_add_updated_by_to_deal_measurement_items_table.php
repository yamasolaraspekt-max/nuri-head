<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deal_measurement_items', function (Blueprint $table) {
            if (!Schema::hasColumn('deal_measurement_items', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deal_measurement_items', function (Blueprint $table) {
            if (Schema::hasColumn('deal_measurement_items', 'updated_by')) {
                $table->dropColumn('updated_by');
            }
        });
    }
};