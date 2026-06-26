<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('installment_payments')) {
            Schema::create('installment_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('installment_id')->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->decimal('payment_amount', 15, 2)->default(0);
                $table->date('payment_date')->nullable()->index();
                $table->unsignedInteger('paid_month_count')->default(1);
                $table->decimal('payment_remained', 15, 2)->default(0);
                $table->string('payment_status')->default('Bezahlt')->index();
                $table->decimal('late_fee', 15, 2)->default(0);
                $table->string('payment_method')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
            return;
        }

        Schema::table('installment_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('installment_payments', 'installment_id'))
                $table->unsignedBigInteger('installment_id')->index();
            if (!Schema::hasColumn('installment_payments', 'branch_id'))
                $table->unsignedBigInteger('branch_id')->nullable()->index();
            if (!Schema::hasColumn('installment_payments', 'payment_amount'))
                $table->decimal('payment_amount', 15, 2)->default(0);
            if (!Schema::hasColumn('installment_payments', 'payment_date'))
                $table->date('payment_date')->nullable()->index();
            if (!Schema::hasColumn('installment_payments', 'paid_month_count'))
                $table->unsignedInteger('paid_month_count')->default(1);
            if (!Schema::hasColumn('installment_payments', 'payment_remained'))
                $table->decimal('payment_remained', 15, 2)->default(0);
            if (!Schema::hasColumn('installment_payments', 'payment_status'))
                $table->string('payment_status')->default('Bezahlt')->index();
            if (!Schema::hasColumn('installment_payments', 'late_fee'))
                $table->decimal('late_fee', 15, 2)->default(0);
            if (!Schema::hasColumn('installment_payments', 'payment_method'))
                $table->string('payment_method')->nullable();
            if (!Schema::hasColumn('installment_payments', 'notes'))
                $table->text('notes')->nullable();
            if (!Schema::hasColumn('installment_payments', 'deleted_at'))
                $table->softDeletes();
        });
    }

    public function down(): void
    {
        // Safe upgrade migration: no destructive rollback.
    }
};
