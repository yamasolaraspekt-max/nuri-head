<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncOfferDetails();
        $this->syncOfferTemplates();
    }

    protected function syncOfferDetails(): void
    {
        if (!Schema::hasTable('offer_details')) {
            return;
        }

        $hasCanvasImages   = Schema::hasColumn('offer_details', 'canvas_images');
        $hasPlacedImages   = Schema::hasColumn('offer_details', 'placed_images');
        $hasBrandLogoUrl   = Schema::hasColumn('offer_details', 'brand_logo_url');
        $hasCoverTextHtml  = Schema::hasColumn('offer_details', 'cover_text_html');

        // Rename only if source exists and target does not exist
        if ($hasCanvasImages && !$hasPlacedImages) {
            Schema::table('offer_details', function (Blueprint $table) {
                $table->renameColumn('canvas_images', 'placed_images');
            });

            $hasPlacedImages = true;
            $hasCanvasImages = false;
        }

        // If both exist, optionally merge data then drop old column manually later
        // For now, ignore canvas_images to avoid collisions

        Schema::table('offer_details', function (Blueprint $table) use (
            $hasBrandLogoUrl,
            $hasCoverTextHtml,
            $hasPlacedImages
        ) {
            if (!$hasBrandLogoUrl) {
                $table->string('brand_logo_url')->nullable()->after('brand_mode');
            }

            if (!$hasCoverTextHtml) {
                $table->longText('cover_text_html')->nullable()->after('company_name');
            }

            if (!$hasPlacedImages) {
                $table->json('placed_images')->nullable()->after('sections');
            }
        });

        // Only run change() if the column exists and doctrine/dbal is installed
        if (Schema::hasColumn('offer_details', 'placed_images')) {
            try {
                Schema::table('offer_details', function (Blueprint $table) {
                    $table->json('placed_images')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Ignore if change() is not supported in current environment
            }
        }
    }

    protected function syncOfferTemplates(): void
    {
        if (!Schema::hasTable('offer_templates')) {
            return;
        }

        $hasCanvasImages   = Schema::hasColumn('offer_templates', 'canvas_images');
        $hasPlacedImages   = Schema::hasColumn('offer_templates', 'placed_images');
        $hasBrandLogoUrl   = Schema::hasColumn('offer_templates', 'brand_logo_url');
        $hasCoverTextHtml  = Schema::hasColumn('offer_templates', 'cover_text_html');

        if ($hasCanvasImages && !$hasPlacedImages) {
            Schema::table('offer_templates', function (Blueprint $table) {
                $table->renameColumn('canvas_images', 'placed_images');
            });

            $hasPlacedImages = true;
            $hasCanvasImages = false;
        }

        Schema::table('offer_templates', function (Blueprint $table) use (
            $hasBrandLogoUrl,
            $hasCoverTextHtml,
            $hasPlacedImages
        ) {
            if (!$hasBrandLogoUrl) {
                $table->string('brand_logo_url')->nullable()->after('brand_mode');
            }

            if (!$hasCoverTextHtml) {
                $table->longText('cover_text_html')->nullable()->after('company_name');
            }

            if (!$hasPlacedImages) {
                $table->json('placed_images')->nullable()->after('sections');
            }
        });

        if (Schema::hasColumn('offer_templates', 'placed_images')) {
            try {
                Schema::table('offer_templates', function (Blueprint $table) {
                    $table->json('placed_images')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Ignore if change() is not supported
            }
        }
    }

    public function down(): void
    {
        //
    }
};