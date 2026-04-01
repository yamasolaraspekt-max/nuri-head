<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_pdf_prints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('offer_folder_id')->constrained('offer_folders')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            
            $table->string('file_name');
            $table->string('file_path')->nullable(); // Nullable because we delete the physical file on update
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active'); // 'active' or 'overwritten_by_update'
            
            $table->timestamps(); // created_at tracks when the user saved it
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_pdf_prints');
    }
};