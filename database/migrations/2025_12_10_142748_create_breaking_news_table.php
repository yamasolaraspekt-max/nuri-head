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
        Schema::create('breaking_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // info, warning, danger, success
            $table->string('icon')->nullable();      // feather icon name, e.g. "alert-triangle"
            $table->boolean('is_active')->default(true);

            $table->dateTime('starts_at')->nullable(); // when to start showing
            $table->dateTime('ends_at')->nullable();   // when to auto-deactivate
            
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);

            $table->foreign('created_by')
                ->references('id')->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breaking_news');
    }
};
