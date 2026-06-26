<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('machines')) {
            Schema::create('machines', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('model')->nullable();
                $table->unsignedSmallInteger('year')->nullable();
                $table->string('color')->nullable();
                $table->string('engine_type')->nullable();
                $table->unsignedInteger('mileage')->nullable();
                $table->string('serial_no')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('department_id')->nullable()->index();
                $table->unsignedBigInteger('article_group')->nullable()->index();
                $table->string('purchase_type')->default('Barzahlung')->index();
                $table->decimal('purchase_price', 12, 2)->nullable();
                $table->date('purchase_date')->nullable();
                $table->string('leasing_from')->nullable();
                $table->date('leasing_start_date')->nullable();
                $table->date('leasing_end_date')->nullable();
                $table->decimal('leasing_price', 12, 2)->nullable();
                $table->date('last_service_date')->nullable();
                $table->boolean('technical_inspection')->default(false);
                $table->date('technical_inspection_date')->nullable();
                $table->string('owner_name')->nullable();
                $table->string('owner_contact')->nullable();
                $table->string('status')->default('active')->index();
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            return;
        }

        Schema::table('machines', function (Blueprint $table) {
            if (!Schema::hasColumn('machines', 'serial_no')) {
                $table->string('serial_no')->nullable()->after('mileage')->index();
            }
            if (!Schema::hasColumn('machines', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('branch_id')->index();
            }
            if (!Schema::hasColumn('machines', 'article_group')) {
                $table->unsignedBigInteger('article_group')->nullable()->after('department_id')->index();
            }
            if (!Schema::hasColumn('machines', 'purchase_price')) {
                $table->decimal('purchase_price', 12, 2)->nullable()->after('purchase_type');
            }
            if (!Schema::hasColumn('machines', 'purchase_date')) {
                $table->date('purchase_date')->nullable()->after('purchase_price');
            }
            if (!Schema::hasColumn('machines', 'leasing_price')) {
                $table->decimal('leasing_price', 12, 2)->nullable()->after('leasing_end_date');
            }
            if (!Schema::hasColumn('machines', 'technical_inspection')) {
                $table->boolean('technical_inspection')->default(false)->after('last_service_date');
            }
            if (!Schema::hasColumn('machines', 'technical_inspection_date')) {
                $table->date('technical_inspection_date')->nullable()->after('technical_inspection');
            }
            if (!Schema::hasColumn('machines', 'status')) {
                $table->string('status')->default('active')->after('owner_contact')->index();
            }
            if (!Schema::hasColumn('machines', 'description')) {
                $table->text('description')->nullable()->after('image');
            }
            if (!Schema::hasColumn('machines', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        // Safe rollback for existing projects: do not drop the machines table.
        Schema::table('machines', function (Blueprint $table) {
            foreach (['serial_no', 'department_id'] as $column) {
                if (Schema::hasColumn('machines', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
