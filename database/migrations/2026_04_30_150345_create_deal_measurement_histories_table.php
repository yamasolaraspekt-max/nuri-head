<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deal_measurement_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deal_measurement_id')
                ->constrained('deal_measurements')
                ->cascadeOnDelete();

            $table->string('action')->nullable(); // form_saved, material_changed, image_uploaded...
            $table->string('section')->nullable(); // PV, WP, Material, Fotos, Dachflächen...
            $table->string('field')->nullable();

            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            $table->json('changes')->nullable();

            $table->string('created_by')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();

            $table->timestamps();

            $table->index(['deal_measurement_id', 'created_at']);
            $table->index('action');
            $table->index('section');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_measurement_histories');
    }
};