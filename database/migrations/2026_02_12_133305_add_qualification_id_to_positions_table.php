<?php
// database/migrations/2026_02_12_000002_add_qualification_id_to_positions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('positions')) {
            return;
        }

        if (!Schema::hasTable('position_qualifications')) {
            return;
        }

        // Fix zero dates only if table exists
        DB::statement("
            UPDATE positions
            SET
              created_at = COALESCE(NULLIF(created_at, '0000-00-00 00:00:00'), NOW()),
              updated_at = COALESCE(NULLIF(updated_at, '0000-00-00 00:00:00'), NOW()),
              deleted_at = NULLIF(deleted_at, '0000-00-00 00:00:00')
        ");

        // 1) Add column first
        Schema::table('positions', function (Blueprint $table) {
            if (!Schema::hasColumn('positions', 'qualification_id')) {
                $table->unsignedBigInteger('qualification_id')->nullable()->after('id');
            }
        });

        // 2) Backfill qualification table + assign ids
        if (Schema::hasColumn('positions', 'qualification')) {
            $existing = DB::table('positions')
                ->select('qualification', 'price')
                ->whereNotNull('qualification')
                ->where('qualification', '!=', '')
                ->groupBy('qualification', 'price')
                ->get();

            foreach ($existing as $row) {
                $qid = DB::table('position_qualifications')
                    ->where('name', $row->qualification)
                    ->value('id');

                if (!$qid) {
                    $qid = DB::table('position_qualifications')->insertGetId([
                        'name' => $row->qualification,
                        'default_price' => $row->price ?? 0,
                        'sort_order' => 0,
                        'status' => 'Published',
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]);
                }

                DB::table('positions')
                    ->where('qualification', $row->qualification)
                    ->whereNull('qualification_id')
                    ->update(['qualification_id' => $qid]);
            }
        }

        // 3) Add FK constraint safely
        if (Schema::hasColumn('positions', 'qualification_id')) {
            try {
                Schema::table('positions', function (Blueprint $table) {
                    $table->dropForeign('positions_qualification_id_foreign');
                });
            } catch (\Throwable $e) {
                // ignore if missing
            }

            try {
                Schema::table('positions', function (Blueprint $table) {
                    $table->foreign('qualification_id', 'positions_qualification_id_foreign')
                        ->references('id')
                        ->on('position_qualifications')
                        ->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // ignore if already exists or cannot be created
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('positions')) {
            return;
        }

        if (Schema::hasColumn('positions', 'qualification_id')) {
            try {
                Schema::table('positions', function (Blueprint $table) {
                    $table->dropForeign('positions_qualification_id_foreign');
                });
            } catch (\Throwable $e) {
                // ignore if missing
            }

            Schema::table('positions', function (Blueprint $table) {
                $table->dropColumn('qualification_id');
            });
        }
    }
};