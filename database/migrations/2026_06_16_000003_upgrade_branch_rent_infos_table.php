<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branch_rent_infos')) {
            Schema::create('branch_rent_infos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('expense_details_id')->nullable()->constrained('branch_expenses')->nullOnDelete();
                $table->unsignedBigInteger('object_id')->nullable()->index();
                $table->unsignedBigInteger('apartment_id')->nullable()->index();
                $table->decimal('cold_rent', 14, 2)->default(0);
                $table->decimal('extra_cost', 14, 2)->default(0);
                $table->decimal('electricity_cost', 14, 2)->default(0);
                $table->decimal('heating_cost', 14, 2)->default(0);
                $table->decimal('repair_cost', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->date('payment_date')->nullable()->index();
                $table->date('due_date')->nullable()->index();
                $table->string('payee')->nullable();
                $table->string('status')->default('open')->index();
                $table->text('notes')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('branch_rent_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_rent_infos', 'due_date')) {
                $table->date('due_date')->nullable()->after('payment_date')->index();
            }
            if (!Schema::hasColumn('branch_rent_infos', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('branch_rent_infos', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // Safe upgrade migration: no destructive rollback.
    }
};
