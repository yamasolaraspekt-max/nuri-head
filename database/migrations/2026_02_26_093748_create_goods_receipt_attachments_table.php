<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('goods_receipt_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('goods_receipt_id');

            // inbound = Wareneingang / outbound = Warenausgang
            $table->string('scope', 20)->default('inbound');

            // image / document
            $table->string('kind', 20)->default('document');

            // optional label like "Lieferschein", "Schadenfoto", "Ausgabebeleg"
            $table->string('label')->nullable();

            $table->string('original_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path');
            $table->string('disk')->default('public');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->unsignedBigInteger('uploaded_by_employee_id')->nullable();

            $table->timestamps();

            $table->foreign('goods_receipt_id')
                ->references('id')
                ->on('goods_receipts')
                ->onDelete('cascade');

            $table->foreign('uploaded_by_employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_attachments');
    }
};