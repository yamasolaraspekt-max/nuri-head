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
        Schema::create('asset_installments', function (Blueprint $table) {
            $table->id();
    
            $table->string('installment_id')->nullable();
            $table->string('type')->nullable();
            $table->string('purchased_from')->nullable();
            $table->double('price_per_month', 10, 2);
            $table->double('fines', 10, 2);
            $table->double('installment_duration', 10, 2);
            $table->double('total', 10, 2);
            $table->date('due_date');
            $table->integer('paid_by');
            $table->string('status')->nullable();
            $table->string('payment_method');
            $table->string('payment_status')->nullable();
            $table->string('contract_document')->nullable(); // Document path or URL
            $table->text('notes')->nullable(); // Additional notes
            $table->string('insurance_provider')->nullable(); // Insurance provider
            $table->string('insurance_payment_month')->nullable(); // Insurance provider
            $table->double('insurance_amount', 10, 2)->nullable(); // Insurance amount
            $table->date('insurance_expiry_date')->nullable(); // Insurance expiry date
            $table->boolean('is_completed')->default(false); // Whether the installment is completed
         
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');

            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_installments');
    }
};
