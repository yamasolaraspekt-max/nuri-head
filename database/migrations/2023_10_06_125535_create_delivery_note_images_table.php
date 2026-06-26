<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_note_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('delivery_note_id')
                ->constrained('delivery_notes')
                ->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('image');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_note_images');
    }
};