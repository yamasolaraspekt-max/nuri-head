<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_stage_sub_stages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_stage_id')
                ->constrained('lead_stages')
                ->cascadeOnDelete();

            $table->string('key')->nullable();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('icon')->nullable();

            $table->unsignedInteger('sort_order')->default(10);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['lead_stage_id', 'key'], 'lead_stage_sub_stage_unique_key');
        });

        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                $table->foreignId('lead_stage_sub_stage_id')
                    ->nullable()
                    ->after('stage')
                    ->constrained('lead_stage_sub_stages')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                $table->dropConstrainedForeignId('lead_stage_sub_stage_id');
            }
        });

        Schema::dropIfExists('lead_stage_sub_stages');
    }
};