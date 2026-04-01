<?php

// database/migrations/2026_02_10_000001_create_master_set_groups_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('master_set_groups', function (Blueprint $table) {
      $table->id();

      // folders are per article group (product)
      $table->unsignedBigInteger('article_group_id')->index();

      $table->string('name', 180);
      $table->longText('description')->nullable();

      // store as hex (e.g. #74b2d4)
      $table->string('color', 20)->nullable();

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('article_group_id')
        ->references('id')->on('article_groups')
        ->cascadeOnDelete();

      $table->index(['article_group_id','name'], 'msg_article_name_idx');
    });
  }

  public function down(): void {
    Schema::dropIfExists('master_set_groups');
  }
};

