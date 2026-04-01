<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_templates', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('departments')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_templates', 'article_group_id')) {
                $table->foreignId('article_group_id')
                    ->nullable()
                    ->after('department_id')
                    ->constrained('article_groups')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_templates', 'brand_id')) {
                $table->foreignId('brand_id')
                    ->nullable()
                    ->after('article_group_id')
                    ->constrained('brands')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_templates', 'distributor_id')) {
                $table->foreignId('distributor_id')
                    ->nullable()
                    ->after('brand_id')
                    ->constrained('distributors')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_templates', 'leistung')) {
                $table->decimal('leistung', 10, 2)
                    ->nullable()
                    ->after('distributor_id');
            }

            if (!Schema::hasColumn('offer_templates', 'description')) {
                $table->longText('description')
                    ->nullable()
                    ->after('leistung');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            if (Schema::hasColumn('offer_templates', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('offer_templates', 'leistung')) {
                $table->dropColumn('leistung');
            }

            if (Schema::hasColumn('offer_templates', 'distributor_id')) {
                $table->dropConstrainedForeignId('distributor_id');
            }

            if (Schema::hasColumn('offer_templates', 'brand_id')) {
                $table->dropConstrainedForeignId('brand_id');
            }

            if (Schema::hasColumn('offer_templates', 'article_group_id')) {
                $table->dropConstrainedForeignId('article_group_id');
            }

            if (Schema::hasColumn('offer_templates', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }
        });
    }
};