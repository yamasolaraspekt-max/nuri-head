<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('linked_deliveries', function (Blueprint $table) {
            $table->id();
            $table->integer('delivery_note');
            $table->integer('linked_to');
            $table->longText('reason')->nullable();
            $table->string('linked_by');
            $table->string('linked_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_deliveries');
    }
};
