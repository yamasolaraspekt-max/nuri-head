<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            // Adding the column after offer_folder_id to keep things organized
            $table->string('offer_no')->nullable()->after('offer_folder_id');
        });
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropColumn('offer_no');
        });
    }
};