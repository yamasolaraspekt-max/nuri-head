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
        Schema::create('installment_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installment_id');
            $table->foreign('installment_id')->references('id')->on('asset_installments')->onDelete('cascade');
            $table->double('payment_amount', 10, 2);
            $table->date('payment_date');
            $table->integer('paid_month_count');
            $table->double('payment_remained', 10, 2);
            $table->enum('payment_status', ['pending', 'completed', 'overdue'])->default('pending');
            $table->double('late_fee', 10, 2)->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_payments');
    }
};
