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
        Schema::create('offer_employee_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_folder_id')->constrained('offer_folders')->cascadeOnDelete();
            $table->foreignId('master_set_id')->nullable()->constrained('product_master_sets')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();

            $table->string('role')->nullable();   // e.g. "Monteur"
            $table->decimal('rate', 12, 2)->default(0);  // €/h
            $table->integer('qty')->default(1);   // Anzahl
            $table->integer('hours_per_person')->default(0); // Std/P
            $table->integer('hours_total')->default(0);
            $table->decimal('sum_total', 12, 2)->default(0); // Summe (EK)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_employee_lists');
    }
};
