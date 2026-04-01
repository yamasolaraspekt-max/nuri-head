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
        Schema::create('maintenance_checklist_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('maintenance_checklist_id');

            $table->string('label');
            $table->string('field_name'); // unique per checklist (e.g. "pipes_status")
            $table->string('field_type'); // text, textarea, select, checkbox, radio, file_image, file_document

            // JSON encoded options for select / radio / checkbox
            $table->json('options')->nullable();

            $table->boolean('is_required')->default(false);

            // UI config
            $table->text('help_text')->nullable();
            $table->string('placeholder')->nullable();

            // MIME accept for file_* types (image/*, application/pdf, etc.)
            $table->string('file_accept')->nullable();

            // sort order for drag & drop
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('maintenance_checklist_id', 'mcl_items_mcl_fk')
                ->references('id')
                ->on('maintenance_checklists')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_checklist_items');
    }
};
