<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_details', 'agb_title')) {
                $table->string('agb_title')->nullable()->after('cover_text_html');
            }

            if (!Schema::hasColumn('offer_details', 'agb_text')) {
                $table->longText('agb_text')->nullable()->after('agb_title');
            }

            if (!Schema::hasColumn('offer_details', 'print_attachments')) {
                $table->json('print_attachments')->nullable()->after('placed_images');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $drop = [];

            if (Schema::hasColumn('offer_details', 'agb_title')) {
                $drop[] = 'agb_title';
            }

            if (Schema::hasColumn('offer_details', 'agb_text')) {
                $drop[] = 'agb_text';
            }

            if (Schema::hasColumn('offer_details', 'print_attachments')) {
                $drop[] = 'print_attachments';
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};