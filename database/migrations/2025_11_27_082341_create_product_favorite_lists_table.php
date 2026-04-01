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
       Schema::create('product_favorite_lists', function (Blueprint $table) {
            $table->id();

            // Owner = employee
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();   // e.g. #f97316
            $table->string('icon')->nullable();    // optional icon name
            $table->boolean('is_shared')->default(false);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_favorite_lists');
    }
};
