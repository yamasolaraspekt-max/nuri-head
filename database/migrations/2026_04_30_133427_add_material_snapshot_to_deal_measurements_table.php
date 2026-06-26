<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deal_measurements', function (Blueprint $table) {
            if (!Schema::hasColumn('deal_measurements', 'materials_snapshot')) {
                $table->json('materials_snapshot')->nullable()->after('sections_snapshot');
            }

            if (!Schema::hasColumn('deal_measurements', 'materials_approved_count')) {
                $table->unsignedInteger('materials_approved_count')->default(0)->after('materials_snapshot');
            }

            if (!Schema::hasColumn('deal_measurements', 'materials_total_count')) {
                $table->unsignedInteger('materials_total_count')->default(0)->after('materials_approved_count');
            }

            if (!Schema::hasColumn('deal_measurements', 'materials_saved_at')) {
                $table->timestamp('materials_saved_at')->nullable()->after('materials_total_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deal_measurements', function (Blueprint $table) {
            $table->dropColumn([
                'materials_snapshot',
                'materials_approved_count',
                'materials_total_count',
                'materials_saved_at',
            ]);
        });
    }
};