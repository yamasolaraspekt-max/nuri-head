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
       Schema::create('learning_topic_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_topic_id')
                ->constrained('learning_topics')
                ->onDelete('cascade');

            // Polymorphic target: Department / Position / Employee
            $table->string('assignable_type'); // e.g. App\Models\Department
            $table->unsignedBigInteger('assignable_id');

            $table->timestamps();

            $table->index(['assignable_type', 'assignable_id'], 'learning_assignable_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_topic_assignments');
    }
};
