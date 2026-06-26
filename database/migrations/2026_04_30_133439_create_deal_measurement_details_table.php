<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deal_measurement_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deal_measurement_id')
                ->constrained('deal_measurements')
                ->cascadeOnDelete();

            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete();
            $table->foreignId('offer_detail_id')->nullable()->constrained('offer_details')->nullOnDelete();

            $table->string('type', 20)->nullable(); // PV / WP / OTHER

            $table->json('form_data')->nullable();
            $table->json('roof_data')->nullable();
            $table->json('pv_data')->nullable();
            $table->json('wp_data')->nullable();
            $table->json('raw_snapshot')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('saved_at')->nullable();

            $table->timestamps();

            $table->unique('deal_measurement_id', 'dm_detail_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_measurement_details');
    }
};