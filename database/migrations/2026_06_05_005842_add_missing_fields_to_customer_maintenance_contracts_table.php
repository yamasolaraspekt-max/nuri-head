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
        Schema::table('customer_maintenance_contracts', function (Blueprint $table) {

            if (!Schema::hasColumn('customer_maintenance_contracts', 'billing_mode')) {
                $table->string('billing_mode')->nullable()->after('contract_type');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'next_service_date')) {
                $table->date('next_service_date')->nullable()->after('billing_mode');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'maintenance_contract_id')) {
                $table->unsignedBigInteger('maintenance_contract_id')->nullable()->index()->after('responsible_employee_id');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'asset_id')) {
                $table->unsignedBigInteger('asset_id')->nullable()->index()->after('maintenance_contract_id');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'status_overall')) {
                $table->string('status_overall')->nullable()->after('status');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'contract_duration_months')) {
                $table->unsignedInteger('contract_duration_months')->nullable()->after('status_overall');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'termination_notice_days')) {
                $table->unsignedInteger('termination_notice_days')->nullable()->after('contract_duration_months');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'recommended_interval_months')) {
                $table->unsignedInteger('recommended_interval_months')->nullable()->after('termination_notice_days');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('terms');
            }

            if (!Schema::hasColumn('customer_maintenance_contracts', 'payload')) {
                $table->json('payload')->nullable()->after('internal_notes');
            }
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_maintenance_contracts', function (Blueprint $table) {
            //
        });
    }
};
