<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activity_log_reads', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lead_activity_log_id');
            $table->unsignedBigInteger('user_id');

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->unique(['lead_activity_log_id', 'user_id'], 'activity_log_user_unique');
            $table->index(['user_id', 'read_at'], 'activity_reads_user_read_index');
            $table->index('lead_activity_log_id', 'activity_reads_log_index');

            $table->foreign('lead_activity_log_id')
                ->references('id')
                ->on('lead_activity_logs')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activity_log_reads');
    }
};