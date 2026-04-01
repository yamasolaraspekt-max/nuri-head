<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_folder_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_folder_id');
            $table->unsignedBigInteger('offer_id')->nullable();

            $table->string('title')->nullable();
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_url')->nullable();

            $table->string('mime_type', 150)->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);

            $table->enum('file_type', ['pdf', 'image', 'other'])->default('other');
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['offer_folder_id', 'sort_order']);
            $table->index(['offer_id']);
            $table->index(['file_type']);

            $table->foreign('offer_folder_id')
                ->references('id')
                ->on('offer_folders')
                ->onDelete('cascade');

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_folder_attachments');
    }
};