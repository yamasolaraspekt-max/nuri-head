<?php

// database/migrations/2026_02_10_000002_create_master_set_group_master_set_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('master_set_group_master_set', function (Blueprint $table) {
      $table->unsignedBigInteger('master_set_group_id');
      $table->unsignedBigInteger('master_set_id');

      $table->timestamps();

      $table->unique(['master_set_group_id','master_set_id'], 'msg_ms_unique');

      $table->foreign('master_set_group_id', 'msg_ms_group_fk')
        ->references('id')->on('master_set_groups')
        ->cascadeOnDelete();

      $table->foreign('master_set_id', 'msg_ms_set_fk')
        ->references('id')->on('master_sets')
        ->cascadeOnDelete();

      $table->index(['master_set_group_id'], 'msg_group_idx');
      $table->index(['master_set_id'], 'msg_set_idx');
    });
  }

  public function down(): void {
    Schema::dropIfExists('master_set_group_master_set');
  }
};
