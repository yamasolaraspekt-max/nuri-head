<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_object_rooms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('alternative_id')->nullable();

            $table->string('name')->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->string('heating')->nullable();
            $table->unsignedInteger('windows')->nullable();
            $table->string('outer_wall')->nullable();
            $table->decimal('target_temp', 10, 2)->nullable();
            $table->string('door')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('lead_id');
            $table->index('alternative_id');

            $table->foreign('lead_id')
                ->references('id')
                ->on('new_leads')
                ->nullOnDelete();

            $table->foreign('alternative_id')
                ->references('id')
                ->on('lead_alternative_adds')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_object_rooms');
    }
};