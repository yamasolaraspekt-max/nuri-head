<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offer_delete_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable();
            $table->string('offer_no')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();

            $table->string('delete_type', 30); // soft or complete
            $table->string('reason')->nullable();

            $table->json('snapshot')->nullable();

            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index(['offer_id', 'delete_type']);
            $table->index(['user_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_delete_logs');
    }
};