<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            // Check if column doesn't exist to prevent duplicate errors
            if (!Schema::hasColumn('offer_details', 'brand_logo_url')) {
                $table->string('brand_logo_url')->nullable()->after('brand_mode');
            }
        });
        
        // Also ensure it is in offer_templates just in case it missed it there too
        Schema::table('offer_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_templates', 'brand_logo_url')) {
                $table->string('brand_logo_url')->nullable()->after('brand_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropColumn('brand_logo_url');
        });
        
        Schema::table('offer_templates', function (Blueprint $table) {
            $table->dropColumn('brand_logo_url');
        });
    }
};