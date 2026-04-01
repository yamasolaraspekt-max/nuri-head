<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('master_set_component_descriptions', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('master_set_component_id')->index();

      // "occasion" / context: angebot, auftrag, project, invoice, internal, etc.
      $table->string('context', 50)->default('angebot')->index();

      // allow multiple descriptions per context
      $table->string('title', 140)->nullable(); // e.g. "Standard", "Kurz", "Technisch", ...
      $table->integer('sort_order')->default(0)->index();

      // Quill content
      $table->json('delta')->nullable();     // quill delta JSON
      $table->longText('html')->nullable();  // rendered html
      $table->longText('text')->nullable();  // plain text (optional for search/export)

      $table->timestamps();

      $table->foreign('master_set_component_id', 'mscd_component_fk')
        ->references('id')->on('master_set_components')
        ->onDelete('cascade');

      // Helpful index for ordering
      $table->index(['master_set_component_id','context','sort_order'], 'mscd_component_context_sort_idx');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('master_set_component_descriptions');
  }
};
