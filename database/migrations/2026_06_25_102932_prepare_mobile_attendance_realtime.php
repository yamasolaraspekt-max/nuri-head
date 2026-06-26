<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    public function up(): void
    {
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('attendances', 'planner_plan_id')) {
                    $table->unsignedBigInteger('planner_plan_id')->nullable()->index()->after('employee_id');
                }

                foreach ([
                    'customer_id',
                    'lead_product_list_id',
                    'alternative_id',
                    'product_id',
                    'article_group',
                    'created_by',
                ] as $column) {
                    if (!Schema::hasColumn('attendances', $column)) {
                        $table->unsignedBigInteger($column)->nullable()->index();
                    }
                }

                if (!Schema::hasColumn('attendances', 'status')) {
                    $table->string('status', 80)->nullable()->index();
                }

                foreach ([
                    'check_in',
                    'check_out',
                    'travel_started_at',
                    'arrived_at',
                    'work_started_at',
                    'work_ended_at',
                    'pause_started_at',
                    'last_location_at',
                ] as $column) {
                    if (!Schema::hasColumn('attendances', $column)) {
                        $table->timestamp($column)->nullable();
                    }
                }

                if (!Schema::hasColumn('attendances', 'pause_type')) {
                    $table->string('pause_type', 80)->nullable();
                }

                if (!Schema::hasColumn('attendances', 'destination')) {
                    $table->string('destination', 1000)->nullable();
                }

                foreach ([
                    'destination_lat',
                    'destination_lng',
                    'current_lat',
                    'current_lng',
                ] as $column) {
                    if (!Schema::hasColumn('attendances', $column)) {
                        $table->decimal($column, 11, 8)->nullable();
                    }
                }

                foreach ([
                    'travel_total_seconds',
                    'pause_total_seconds',
                    'work_total_seconds',
                ] as $column) {
                    if (!Schema::hasColumn('attendances', $column)) {
                        $table->unsignedInteger($column)->default(0);
                    }
                }

                if (!Schema::hasColumn('attendances', 'meta')) {
                    $table->json('meta')->nullable();
                }
            });
        }

        if (!Schema::hasTable('attendance_events')) {
            Schema::create('attendance_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attendance_id')->nullable()->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->unsignedBigInteger('planner_plan_id')->nullable()->index();
                $table->unsignedBigInteger('planner_item_id')->nullable()->index();
                $table->string('event_type', 80)->index();
                $table->string('type', 80)->nullable()->index();
                $table->timestamp('event_at')->nullable()->index();
                $table->decimal('lat', 11, 8)->nullable();
                $table->decimal('lng', 11, 8)->nullable();
                $table->string('destination', 1000)->nullable();
                $table->text('note')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('attendance_locations')) {
            Schema::create('attendance_locations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attendance_id')->nullable()->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->unsignedBigInteger('planner_plan_id')->nullable()->index();
                $table->decimal('lat', 11, 8);
                $table->decimal('lng', 11, 8);
                $table->decimal('accuracy', 10, 2)->nullable();
                $table->decimal('speed', 10, 2)->nullable();
                $table->decimal('heading', 10, 2)->nullable();
                $table->string('destination', 1000)->nullable();
                $table->timestamp('recorded_at')->nullable()->index();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Do not drop production attendance data automatically.
        // Roll back manually only if needed.
    }
};
