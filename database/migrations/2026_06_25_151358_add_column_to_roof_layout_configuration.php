<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offer_roof_layout_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_roof_layout_configurations', 'canvas_layout')) {
                $table->longText('canvas_layout')->nullable()->after('compass_image_path');
            }

            if (!Schema::hasColumn('offer_roof_layout_configurations', 'canvas_design_width')) {
                $table->unsignedInteger('canvas_design_width')->default(1000)->after('canvas_layout');
            }

            if (!Schema::hasColumn('offer_roof_layout_configurations', 'canvas_design_height')) {
                $table->unsignedInteger('canvas_design_height')->default(700)->after('canvas_design_width');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_roof_layout_configurations', function (Blueprint $table) {
            if (Schema::hasColumn('offer_roof_layout_configurations', 'canvas_design_height')) {
                $table->dropColumn('canvas_design_height');
            }

            if (Schema::hasColumn('offer_roof_layout_configurations', 'canvas_design_width')) {
                $table->dropColumn('canvas_design_width');
            }

            if (Schema::hasColumn('offer_roof_layout_configurations', 'canvas_layout')) {
                $table->dropColumn('canvas_layout');
            }
        });
    }
};
