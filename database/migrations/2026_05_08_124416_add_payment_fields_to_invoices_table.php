<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)->after('total_amount');
            }

            if (!Schema::hasColumn('invoices', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_amount');
            }

            if (!Schema::hasColumn('invoices', 'payment_note')) {
                $table->text('payment_note')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'payment_note')) {
                $table->dropColumn('payment_note');
            }

            if (Schema::hasColumn('invoices', 'paid_at')) {
                $table->dropColumn('paid_at');
            }

            if (Schema::hasColumn('invoices', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
