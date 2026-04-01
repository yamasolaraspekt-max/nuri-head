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
        Schema::create('inventory_request_outs', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('responsible_id');
            $table->integer('requester_id');
            $table->string('reason')->nullable();
            $table->integer('quantity');
            $table->string('add_by')->nullable();
            $table->date('add_date')->nullable();
            $table->string('delete_by')->nullable();
            $table->date('delete_date')->nullable();
            $table->string('edit_by')->nullable();
            $table->date('edit_date')->nullable();
            $table->string('status')->nullable();
            // Foreign keys

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_request_outs');
    }
};
