<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('machine_services')) {
            Schema::create('machine_services', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('machine_id')->index();
                $table->string('service_type')->index();
                $table->date('service_date')->nullable()->index();
                $table->unsignedBigInteger('service_by')->nullable()->index();
                $table->decimal('price', 12, 2)->default(0);
                $table->string('paid_by')->nullable();
                $table->string('service_station')->nullable();
                $table->string('technician')->nullable();
                $table->string('location')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('service_report')->nullable();
                $table->unsignedInteger('maintenance_interval')->nullable();
                $table->text('fault_description')->nullable();
                $table->text('repair_description')->nullable();
                $table->dateTime('fault_detected_at')->nullable();
                $table->unsignedBigInteger('fault_detected_by')->nullable()->index();
                $table->string('fault_detected_location')->nullable();
                $table->string('status')->default('open')->index();
                $table->timestamps();
                $table->softDeletes();
            });

            return;
        }

        Schema::table('machine_services', function (Blueprint $table) {
            if (!Schema::hasColumn('machine_services', 'paid_by')) {
                $table->string('paid_by')->nullable()->after('price');
            }
            if (!Schema::hasColumn('machine_services', 'service_report')) {
                $table->string('service_report')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('machine_services', 'maintenance_interval')) {
                $table->unsignedInteger('maintenance_interval')->nullable()->after('service_report');
            }
            if (!Schema::hasColumn('machine_services', 'status')) {
                $table->string('status')->default('open')->after('paid_by')->index();
            }
            if (!Schema::hasColumn('machine_services', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        // Safe rollback for existing projects: do not drop service history.
        Schema::table('machine_services', function (Blueprint $table) {
            foreach (['maintenance_interval'] as $column) {
                if (Schema::hasColumn('machine_services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
