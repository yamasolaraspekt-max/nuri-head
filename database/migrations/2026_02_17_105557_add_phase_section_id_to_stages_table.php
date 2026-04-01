<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            // section relation (phase_sections.id)
            if (!Schema::hasColumn('stages', 'phase_section_id')) {
                $table->unsignedBigInteger('phase_section_id')->nullable()->after('product_id');
            }

            // indexes for fast loading by product/version/section
            $table->index(['product_id', 'version', 'phase_section_id'], 'stages_prod_ver_sec_idx');
            $table->index(['phase_section_id', 'sort_order'], 'stages_sec_sort_idx');

            // FK
            $table->foreign('phase_section_id', 'stages_phase_section_fk')
                ->references('id')->on('phase_sections')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            // drop FK + indexes + column
            try { $table->dropForeign('stages_phase_section_fk'); } catch (\Throwable $e) {}
            try { $table->dropIndex('stages_prod_ver_sec_idx'); } catch (\Throwable $e) {}
            try { $table->dropIndex('stages_sec_sort_idx'); } catch (\Throwable $e) {}

            if (Schema::hasColumn('stages', 'phase_section_id')) {
                $table->dropColumn('phase_section_id');
            }
        });
    }
};
