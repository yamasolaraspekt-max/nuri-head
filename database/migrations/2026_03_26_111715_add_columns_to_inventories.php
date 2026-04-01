<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'inventory_category')) {
                $table->string('inventory_category')->nullable()->after('location');
            }

            if (!Schema::hasColumn('inventories', 'room_name')) {
                $table->string('room_name')->nullable()->after('inventory_category');
            }

            if (!Schema::hasColumn('inventories', 'room_number')) {
                $table->string('room_number')->nullable()->after('room_name');
            }

            if (!Schema::hasColumn('inventories', 'rack_name')) {
                $table->string('rack_name')->nullable()->after('room_number');
            }

            if (!Schema::hasColumn('inventories', 'column')) {
                $table->string('column')->nullable()->after('row');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $drop = [];

            foreach (['inventory_category', 'room_name', 'room_number', 'rack_name', 'column'] as $col) {
                if (Schema::hasColumn('inventories', $col)) {
                    $drop[] = $col;
                }
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};