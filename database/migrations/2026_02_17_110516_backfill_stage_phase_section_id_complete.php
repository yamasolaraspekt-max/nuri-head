<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            // 1) Ensure every product has a "complete" phase_section
            $productIds = DB::table('stages')
                ->whereNull('deleted_at')
                ->whereNotNull('product_id')
                ->distinct()
                ->pluck('product_id');

            foreach ($productIds as $pid) {
                $complete = DB::table('phase_sections')
                    ->whereNull('deleted_at')
                    ->where('product_id', $pid)
                    ->where('phase_section', 'complete')
                    ->first();

                if (!$complete) {
                    $id = DB::table('phase_sections')->insertGetId([
                        'product_id'     => $pid,
                        'phase_section'  => 'complete',
                        'status'         => 'Published',
                        'sort_order'     => 0,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                    $completeId = $id;
                } else {
                    $completeId = $complete->id;
                }

                // 2) Backfill stages (old data) => set to complete if null
                DB::table('stages')
                    ->whereNull('deleted_at')
                    ->where('product_id', $pid)
                    ->whereNull('phase_section_id')
                    ->update([
                        'phase_section_id' => $completeId,
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    public function down(): void
    {
        // no safe down (data backfill)
    }
};
