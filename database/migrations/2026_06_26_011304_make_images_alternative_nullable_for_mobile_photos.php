<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('images')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile/customer photos can be customer-level only
        |--------------------------------------------------------------------------
        | Nuriva may send photos for a customer task where no lead alternative is
        | selected yet. The existing controller already treats alternative_id as
        | nullable in some upload paths, so the DB must allow NULL here.
        */
        if (Schema::hasColumn('images', 'alternative_id')) {
            try {
                Schema::table('images', function (Blueprint $table) {
                    $table->dropForeign(['alternative_id']);
                });
            } catch (\Throwable $e) {
                // FK may not exist or may have a custom name.
            }

            try {
                DB::statement('ALTER TABLE images MODIFY alternative_id BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE images MODIFY alternative_id INT UNSIGNED NULL');
                } catch (\Throwable $e2) {
                    // Leave unchanged if this DB uses a different column type.
                }
            }
        }

        if (Schema::hasColumn('images', 'article_group')) {
            try {
                DB::statement('ALTER TABLE images MODIFY article_group BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE images MODIFY article_group INT UNSIGNED NULL');
                } catch (\Throwable $e2) {
                    // Leave unchanged.
                }
            }
        }
    }

    public function down(): void
    {
        // Do not force alternative_id back to NOT NULL because existing mobile
        // customer-only photos may already have NULL alternative_id.
    }
};
