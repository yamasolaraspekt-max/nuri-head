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
        Schema::create('maintenance_documents', function (Blueprint $table) {
            $table->id();

            // polymorph: kann an Asset, Protocol, Contract, Lead etc. hängen
            $table->unsignedBigInteger('documentable_id');
            $table->string('documentable_type');

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('category')->nullable();       // z.B. "photo", "protocol_pdf", "contract_pdf"
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // Bytes

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['category']);

            $table->foreign('uploaded_by')
                ->references('id')->on('employees')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_documents');
    }
};
