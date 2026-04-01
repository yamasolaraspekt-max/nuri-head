<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->json('material_history')->nullable()->after('sections');
        });
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropColumn('material_history');
        });
    }
};