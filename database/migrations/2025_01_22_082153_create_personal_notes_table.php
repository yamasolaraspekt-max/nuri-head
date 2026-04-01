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
       Schema::create('personal_notes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Neue Notiz');
            $table->longText('note')->nullable();
            $table->boolean('is_done')->default(false);
            $table->timestamp('done_date')->nullable();
            $table->boolean('add_calendar')->default(false);
            $table->date('add_calendar_date')->nullable(); 
            $table->integer('order_by')->nullable();
            $table->date('deadline')->nullable();
            $table->time('end_time')->nullable();
            $table->string('repeat')->nullable();
            $table->date('reminder_date')->nullable();
            $table->time('reminder_time')->nullable();
            $table->string('priority')->nullable();
            $table->string('color')->nullable();
            $table->string('is_notified')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('note_categories')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_notes');
    }
};
