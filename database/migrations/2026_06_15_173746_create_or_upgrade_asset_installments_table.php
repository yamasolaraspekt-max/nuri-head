<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('asset_installments')) {
            Schema::create('asset_installments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id')->nullable()->index();
                $table->string('type')->default('machine')->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->string('installment_id')->nullable()->index();
                $table->string('purchased_from')->nullable();
                $table->decimal('price_per_month', 15, 2)->default(0);
                $table->decimal('fines', 15, 2)->default(0);
                $table->unsignedInteger('installment_duration')->default(1);
                $table->date('due_date')->nullable();
                $table->decimal('total', 15, 2)->default(0);
                $table->unsignedBigInteger('paid_by')->nullable()->index();
                $table->string('payment_method')->nullable();
                $table->string('insurance_provider')->nullable();
                $table->decimal('insurance_amount', 15, 2)->default(0);
                $table->string('insurance_payment_month')->nullable();
                $table->date('insurance_expiry_date')->nullable();
                $table->string('contract_document')->nullable();
                $table->string('status')->default('open')->index();
                $table->timestamps();
                $table->softDeletes();
            });
            return;
        }

        Schema::table('asset_installments', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_installments', 'asset_id'))
                $table->unsignedBigInteger('asset_id')->nullable()->index();
            if (!Schema::hasColumn('asset_installments', 'type'))
                $table->string('type')->default('machine')->index();
            if (!Schema::hasColumn('asset_installments', 'branch_id'))
                $table->unsignedBigInteger('branch_id')->nullable()->index();
            if (!Schema::hasColumn('asset_installments', 'installment_id'))
                $table->string('installment_id')->nullable()->index();
            if (!Schema::hasColumn('asset_installments', 'purchased_from'))
                $table->string('purchased_from')->nullable();
            if (!Schema::hasColumn('asset_installments', 'price_per_month'))
                $table->decimal('price_per_month', 15, 2)->default(0);
            if (!Schema::hasColumn('asset_installments', 'fines'))
                $table->decimal('fines', 15, 2)->default(0);
            if (!Schema::hasColumn('asset_installments', 'installment_duration'))
                $table->unsignedInteger('installment_duration')->default(1);
            if (!Schema::hasColumn('asset_installments', 'due_date'))
                $table->date('due_date')->nullable();
            if (!Schema::hasColumn('asset_installments', 'total'))
                $table->decimal('total', 15, 2)->default(0);
            if (!Schema::hasColumn('asset_installments', 'paid_by'))
                $table->unsignedBigInteger('paid_by')->nullable()->index();
            if (!Schema::hasColumn('asset_installments', 'payment_method'))
                $table->string('payment_method')->nullable();
            if (!Schema::hasColumn('asset_installments', 'insurance_provider'))
                $table->string('insurance_provider')->nullable();
            if (!Schema::hasColumn('asset_installments', 'insurance_amount'))
                $table->decimal('insurance_amount', 15, 2)->default(0);
            if (!Schema::hasColumn('asset_installments', 'insurance_payment_month'))
                $table->string('insurance_payment_month')->nullable();
            if (!Schema::hasColumn('asset_installments', 'insurance_expiry_date'))
                $table->date('insurance_expiry_date')->nullable();
            if (!Schema::hasColumn('asset_installments', 'contract_document'))
                $table->string('contract_document')->nullable();
            if (!Schema::hasColumn('asset_installments', 'status'))
                $table->string('status')->default('open')->index();
            if (!Schema::hasColumn('asset_installments', 'deleted_at'))
                $table->softDeletes();
        });
    }

    public function down(): void
    {
        // Safe upgrade migration: no destructive rollback.
    }
};
