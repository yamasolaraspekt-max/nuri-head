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
        Schema::table('master_sets', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->integer('count_copy')->nullable();
            $table->integer('count_offer')->nullable();
            $table->integer('is_locked')->default(1); 

            $table->foreign('creator_id')
                            ->references('id')
                            ->on('employees')
                            ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sets');

    }
};
