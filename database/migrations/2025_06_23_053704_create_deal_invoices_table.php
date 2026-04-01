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
        Schema::create('deal_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();

            $table->string('invoice_number')->nullable();
            $table->string('invoice_type')->nullable(); // Abschlagrechnung, Schlussrechnung, etc.
            $table->decimal('invoice_amount', 12, 2)->nullable();
            $table->decimal('paid_amount', 12, 2)->nullable()->default(0);
            $table->decimal('open_amount', 12, 2)->nullable();

            $table->date('issued_at')->nullable();
            $table->date('due_date')->nullable();
            $table->date('paid_at')->nullable();

            $table->string('status')->default('open'); // open, due, paid, canceled
            $table->boolean('is_storno')->default(false);
            $table->tinyInteger('reminder_level')->default(0); // 0 = none, up to 3
            $table->date('last_reminder_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('checked_by')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_invoices');
    }
};
