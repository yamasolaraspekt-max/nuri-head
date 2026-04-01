<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('master_sets', function (Blueprint $table) {

            if (!Schema::hasColumn('master_sets', 'costing_set_id')) {
                $table->unsignedBigInteger('costing_set_id')->nullable()->after('article_group_id');
                $table->index('costing_set_id', 'ms_costing_set_idx');
            }

            // Optional: store how to calculate when roles missing
            if (!Schema::hasColumn('master_sets', 'costing_rate_mode')) {
                $table->string('costing_rate_mode')->default('full_cost')->after('costing_set_id');
                // full_cost | sell_rate | wage_only
            }

            if (!Schema::hasColumn('master_sets', 'costing_fallback')) {
                $table->string('costing_fallback')->default('task_rate')->after('costing_rate_mode');
                // task_rate | qualification_default | zero
            }
        });

        // Add FK in separate statement (safer for existing DBs)
        Schema::table('master_sets', function (Blueprint $table) {
            // avoid duplicate FK when re-running in some envs
            // if your DB already has it, remove this block
            $table->foreign('costing_set_id', 'ms_costing_set_fk')
                ->references('id')->on('costing_sets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('master_sets', function (Blueprint $table) {
            if (Schema::hasColumn('master_sets', 'costing_set_id')) {
                // drop FK + index + column
                try { $table->dropForeign('ms_costing_set_fk'); } catch (\Throwable $e) {}
                try { $table->dropIndex('ms_costing_set_idx'); } catch (\Throwable $e) {}
                $table->dropColumn('costing_set_id');
            }

            if (Schema::hasColumn('master_sets', 'costing_rate_mode')) {
                $table->dropColumn('costing_rate_mode');
            }

            if (Schema::hasColumn('master_sets', 'costing_fallback')) {
                $table->dropColumn('costing_fallback');
            }
        });
    }
};