<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offer_folders', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_folders', 'history')) {
                $table->json('history')->nullable()->after('color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_folders', function (Blueprint $table) {
            if (Schema::hasColumn('offer_folders', 'history')) {
                $table->dropColumn('history');
            }
        });
    }
};