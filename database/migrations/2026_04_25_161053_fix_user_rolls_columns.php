<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Convert old "on/off" values to 1/0
        |--------------------------------------------------------------------------
        */
        DB::table('user_rolls')->update([
            'is_read' => DB::raw("CASE WHEN is_read = 'on' THEN 1 ELSE 0 END"),
            'is_update' => DB::raw("CASE WHEN is_update = 'on' THEN 1 ELSE 0 END"),
            'is_delete' => DB::raw("CASE WHEN is_delete = 'on' THEN 1 ELSE 0 END"),
            'is_add' => DB::raw("CASE WHEN is_add = 'on' THEN 1 ELSE 0 END"),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Change permission columns to booleans
        |--------------------------------------------------------------------------
        */
        DB::statement("ALTER TABLE user_rolls MODIFY is_read TINYINT(1) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE user_rolls MODIFY is_update TINYINT(1) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE user_rolls MODIFY is_delete TINYINT(1) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE user_rolls MODIFY is_add TINYINT(1) NOT NULL DEFAULT 0");

        /*
        |--------------------------------------------------------------------------
        | Make item_id safer
        |--------------------------------------------------------------------------
        */
        DB::statement("ALTER TABLE user_rolls MODIFY item_id VARCHAR(100) NOT NULL");

        /*
        |--------------------------------------------------------------------------
        | Remove duplicate rows before adding unique index
        |--------------------------------------------------------------------------
        */
        DB::statement("
            DELETE ur1 FROM user_rolls ur1
            INNER JOIN user_rolls ur2
                ON ur1.user_id = ur2.user_id
                AND ur1.item_id = ur2.item_id
                AND ur1.id > ur2.id
        ");

        /*
        |--------------------------------------------------------------------------
        | Add unique index if missing
        |--------------------------------------------------------------------------
        */
        $indexes = collect(DB::select("SHOW INDEX FROM user_rolls"))
            ->pluck('Key_name')
            ->toArray();

        if (!in_array('user_rolls_user_item_unique', $indexes, true)) {
            Schema::table('user_rolls', function (Blueprint $table) {
                $table->unique(['user_id', 'item_id'], 'user_rolls_user_item_unique');
            });
        }
    }

    public function down(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM user_rolls"))
            ->pluck('Key_name')
            ->toArray();

        if (in_array('user_rolls_user_item_unique', $indexes, true)) {
            Schema::table('user_rolls', function (Blueprint $table) {
                $table->dropUnique('user_rolls_user_item_unique');
            });
        }

        DB::statement("ALTER TABLE user_rolls MODIFY is_read VARCHAR(255) NOT NULL DEFAULT 'off'");
        DB::statement("ALTER TABLE user_rolls MODIFY is_update VARCHAR(255) NOT NULL DEFAULT 'off'");
        DB::statement("ALTER TABLE user_rolls MODIFY is_delete VARCHAR(255) NOT NULL DEFAULT 'off'");
        DB::statement("ALTER TABLE user_rolls MODIFY is_add VARCHAR(255) NOT NULL DEFAULT 'off'");
    }
};