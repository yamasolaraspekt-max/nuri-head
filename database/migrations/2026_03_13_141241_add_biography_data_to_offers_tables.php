<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the main offer details table
        Schema::table('offer_details', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_details', 'biography_data')) {
                $table->json('biography_data')->nullable()->after('placed_images');
            }
        });

        // Update the templates table so templates can store history too
        Schema::table('offer_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_templates', 'biography_data')) {
                $table->json('biography_data')->nullable()->after('placed_images');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropColumn('biography_data');
        });

        Schema::table('offer_templates', function (Blueprint $table) {
            $table->dropColumn('biography_data');
        });
    }
};