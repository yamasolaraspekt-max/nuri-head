<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();

            $table->string('delivery_note')->unique();
            $table->string('delivered_from');

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('handover_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('order_by')->nullable();
            $table->string('order_no')->nullable();
            $table->string('comission')->nullable();
            $table->date('order_date')->nullable();

            $table->date('handover_date')->nullable();
            $table->longText('description')->nullable();

            $table->string('status')->default('Verfügbar');
            $table->unsignedTinyInteger('progress')->default(0);

            $table->string('pdf')->nullable();
            $table->string('image')->nullable();

            $table->string('linked')->nullable();

            $table->foreignId('linked_delivery_note_id')
                ->nullable()
                ->constrained('delivery_notes')
                ->nullOnDelete();

            $table->unsignedTinyInteger('level')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};