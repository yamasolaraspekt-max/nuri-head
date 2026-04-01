<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('location');
            }

            if (!Schema::hasColumn('inventories', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }

            if (!Schema::hasColumn('inventories', 'location_label')) {
                $table->string('location_label')->nullable()->after('longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories', 'location_label')) {
                $table->dropColumn('location_label');
            }

            if (Schema::hasColumn('inventories', 'longitude')) {
                $table->dropColumn('longitude');
            }

            if (Schema::hasColumn('inventories', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });
    }
};