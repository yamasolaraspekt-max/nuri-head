<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_templates', 'is_favorite')) {
                $table->boolean('is_favorite')
                    ->default(false)
                    ->after('description');
            }

            if (!Schema::hasColumn('offer_templates', 'stamp')) {
                $table->string('stamp')
                    ->nullable()
                    ->after('is_favorite');
            }

            if (!Schema::hasColumn('offer_templates', 'usage_count')) {
                $table->unsignedBigInteger('usage_count')
                    ->default(0)
                    ->after('stamp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('offer_templates', 'usage_count')) {
                $dropColumns[] = 'usage_count';
            }

            if (Schema::hasColumn('offer_templates', 'stamp')) {
                $dropColumns[] = 'stamp';
            }

            if (Schema::hasColumn('offer_templates', 'is_favorite')) {
                $dropColumns[] = 'is_favorite';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};