<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            if (!Schema::hasColumn('distributors', 'cash_discount')) {
                $table->decimal('cash_discount', 5, 2)->nullable()->after('account_number');
            }

            if (!Schema::hasColumn('distributors', 'payment_terms')) {
                $table->string('payment_terms', 255)->nullable()->after('cash_discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            if (Schema::hasColumn('distributors', 'payment_terms')) {
                $table->dropColumn('payment_terms');
            }

            if (Schema::hasColumn('distributors', 'cash_discount')) {
                $table->dropColumn('cash_discount');
            }
        });
    }
};