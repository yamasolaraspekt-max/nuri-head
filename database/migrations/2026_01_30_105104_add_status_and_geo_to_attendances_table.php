<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // what is this check-in/out for?
            $table->string('check_in_status')->nullable()->after('check_in');   // e.g. login, work_start, break_end
            $table->string('check_out_status')->nullable()->after('check_out'); // e.g. logout, work_end, break_start

            // geo for check-in
            $table->decimal('check_in_lat', 10, 7)->nullable()->after('check_in_status');
            $table->decimal('check_in_lng', 10, 7)->nullable()->after('check_in_lat');
            $table->string('check_in_location', 255)->nullable()->after('check_in_lng'); // reverse-geocoded address or label
            $table->unsignedInteger('check_in_accuracy')->nullable()->after('check_in_location'); // meters (optional)

            // geo for check-out
            $table->decimal('check_out_lat', 10, 7)->nullable()->after('check_out_status');
            $table->decimal('check_out_lng', 10, 7)->nullable()->after('check_out_lat');
            $table->string('check_out_location', 255)->nullable()->after('check_out_lng');
            $table->unsignedInteger('check_out_accuracy')->nullable()->after('check_out_location');

            // optional metadata (device, ip, ua, etc.)
            $table->json('meta')->nullable()->after('check_out_accuracy');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_status','check_out_status',
                'check_in_lat','check_in_lng','check_in_location','check_in_accuracy',
                'check_out_lat','check_out_lng','check_out_location','check_out_accuracy',
                'meta',
            ]);
        });
    }
};
