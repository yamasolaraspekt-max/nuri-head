<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('invoice_id')->index();

            // filenames
            $table->string('original_name', 255)->nullable();
            $table->string('stored_name', 255)->index();
            $table->string('stored_path', 500)->index();

            // metadata
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);

            // ✅ user tracking for uploads
            $table->unsignedBigInteger('uploaded_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_files');
    }
};
