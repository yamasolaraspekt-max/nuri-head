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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_note');
            $table->string('from');
            $table->string('to');
            $table->string('order_by')->nullable();
            $table->string('order_no')->nullable();
            $table->string('comission')->nullable();
            $table->date('order_date')->nullable();
            $table->string('handover_by');
            $table->date('handover_date');
            $table->longText('description')->nullable();
            $table->string('status');
            $table->string('progress')->nullable();
            $table->string('pdf')->nullable();
            $table->string('linked')->nullable();
            $table->integer('level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
