<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_details', 'offer_folder_id')) {
                $table->foreignId('offer_folder_id')
                    ->nullable()
                    ->after('offer_id')
                    ->constrained('offer_folders')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_details', 'brand_logo_url')) {
                $table->string('brand_logo_url')->nullable()->after('brand_mode');
            }

            if (!Schema::hasColumn('offer_details', 'cover_text_html')) {
                $table->longText('cover_text_html')->nullable()->after('company_name');
            }

            if (!Schema::hasColumn('offer_details', 'placed_images')) {
                $table->json('placed_images')->nullable()->after('sections');
            }
        });

        // copy old data if old columns exist
        if (Schema::hasColumn('offer_details', 'cover_text') && Schema::hasColumn('offer_details', 'cover_text_html')) {
            DB::statement('UPDATE offer_details SET cover_text_html = cover_text WHERE cover_text_html IS NULL');
        }

        if (Schema::hasColumn('offer_details', 'canvas_images') && Schema::hasColumn('offer_details', 'placed_images')) {
            DB::statement('UPDATE offer_details SET placed_images = canvas_images WHERE placed_images IS NULL');
        }
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            if (Schema::hasColumn('offer_details', 'offer_folder_id')) {
                $table->dropConstrainedForeignId('offer_folder_id');
            }

            if (Schema::hasColumn('offer_details', 'brand_logo_url')) {
                $table->dropColumn('brand_logo_url');
            }

            if (Schema::hasColumn('offer_details', 'cover_text_html')) {
                $table->dropColumn('cover_text_html');
            }

            if (Schema::hasColumn('offer_details', 'placed_images')) {
                $table->dropColumn('placed_images');
            }
        });
    }
};