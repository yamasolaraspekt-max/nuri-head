<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('new_leads', function (Blueprint $table) {
            $table->string('moser_id')->nullable()->after('purchase_date');
            $table->string('payment_type')->nullable()->after('moser_id');
            $table->string('payment_means')->nullable()->after('payment_type');
            $table->string('bank_name')->nullable()->after('payment_means');
            $table->string('iban')->nullable()->after('bank_name');
            $table->string('bank_holder')->nullable()->after('iban');
            $table->string('bic')->nullable()->after('bank_holder');
        });
    }

    public function down(): void
    {
        Schema::table('new_leads', function (Blueprint $table) {
            $table->dropColumn([
                'moser_id',
                'payment_type',
                'payment_means',
                'bank_name',
                'iban',
                'bank_holder',
                'bic',
            ]);
        });
    }
};
