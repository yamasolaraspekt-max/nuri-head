<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function addColumnIfMissing(Blueprint $table, string $tableName, string $column, callable $callback): void
    {
        if (!Schema::hasColumn($tableName, $column)) {
            $callback($table);
        }
    }

    public function up(): void
    {
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('date')->index();
                $table->string('status')->default('absent')->index();
                $table->dateTime('check_in')->nullable();
                $table->dateTime('check_out')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('attendances', function (Blueprint $table) {
            $tableName = 'attendances';

            $this->addColumnIfMissing($table, $tableName, 'employee_id', fn($t) => $t->unsignedBigInteger('employee_id')->nullable()->index()->after('id'));
            $this->addColumnIfMissing($table, $tableName, 'planner_plan_id', fn($t) => $t->unsignedBigInteger('planner_plan_id')->nullable()->index()->after('employee_id'));
            $this->addColumnIfMissing($table, $tableName, 'customer_id', fn($t) => $t->unsignedBigInteger('customer_id')->nullable()->index()->after('planner_plan_id'));
            $this->addColumnIfMissing($table, $tableName, 'lead_product_list_id', fn($t) => $t->unsignedBigInteger('lead_product_list_id')->nullable()->index()->after('customer_id'));
            $this->addColumnIfMissing($table, $tableName, 'alternative_id', fn($t) => $t->unsignedBigInteger('alternative_id')->nullable()->index()->after('lead_product_list_id'));
            $this->addColumnIfMissing($table, $tableName, 'product_id', fn($t) => $t->unsignedBigInteger('product_id')->nullable()->index()->after('alternative_id'));
            $this->addColumnIfMissing($table, $tableName, 'article_group', fn($t) => $t->unsignedBigInteger('article_group')->nullable()->index()->after('product_id'));
            $this->addColumnIfMissing($table, $tableName, 'date', fn($t) => $t->date('date')->nullable()->index()->after('article_group'));
            $this->addColumnIfMissing($table, $tableName, 'status', fn($t) => $t->string('status')->default('absent')->index()->after('date'));
            $this->addColumnIfMissing($table, $tableName, 'check_in', fn($t) => $t->dateTime('check_in')->nullable()->after('status'));
            $this->addColumnIfMissing($table, $tableName, 'check_out', fn($t) => $t->dateTime('check_out')->nullable()->after('check_in'));
            $this->addColumnIfMissing($table, $tableName, 'travel_started_at', fn($t) => $t->dateTime('travel_started_at')->nullable()->after('check_out'));
            $this->addColumnIfMissing($table, $tableName, 'arrived_at', fn($t) => $t->dateTime('arrived_at')->nullable()->after('travel_started_at'));
            $this->addColumnIfMissing($table, $tableName, 'work_started_at', fn($t) => $t->dateTime('work_started_at')->nullable()->after('arrived_at'));
            $this->addColumnIfMissing($table, $tableName, 'work_ended_at', fn($t) => $t->dateTime('work_ended_at')->nullable()->after('work_started_at'));
            $this->addColumnIfMissing($table, $tableName, 'pause_started_at', fn($t) => $t->dateTime('pause_started_at')->nullable()->after('work_ended_at'));
            $this->addColumnIfMissing($table, $tableName, 'pause_type', fn($t) => $t->string('pause_type')->nullable()->after('pause_started_at'));
            $this->addColumnIfMissing($table, $tableName, 'travel_total_seconds', fn($t) => $t->unsignedInteger('travel_total_seconds')->default(0)->after('pause_type'));
            $this->addColumnIfMissing($table, $tableName, 'pause_total_seconds', fn($t) => $t->unsignedInteger('pause_total_seconds')->default(0)->after('travel_total_seconds'));
            $this->addColumnIfMissing($table, $tableName, 'work_total_seconds', fn($t) => $t->unsignedInteger('work_total_seconds')->default(0)->after('pause_total_seconds'));
            $this->addColumnIfMissing($table, $tableName, 'destination', fn($t) => $t->text('destination')->nullable()->after('work_total_seconds'));
            $this->addColumnIfMissing($table, $tableName, 'destination_lat', fn($t) => $t->decimal('destination_lat', 11, 8)->nullable()->after('destination'));
            $this->addColumnIfMissing($table, $tableName, 'destination_lng', fn($t) => $t->decimal('destination_lng', 11, 8)->nullable()->after('destination_lat'));
            $this->addColumnIfMissing($table, $tableName, 'current_lat', fn($t) => $t->decimal('current_lat', 11, 8)->nullable()->after('destination_lng'));
            $this->addColumnIfMissing($table, $tableName, 'current_lng', fn($t) => $t->decimal('current_lng', 11, 8)->nullable()->after('current_lat'));
            $this->addColumnIfMissing($table, $tableName, 'last_location_at', fn($t) => $t->dateTime('last_location_at')->nullable()->after('current_lng'));
            $this->addColumnIfMissing($table, $tableName, 'created_by', fn($t) => $t->unsignedBigInteger('created_by')->nullable()->after('last_location_at'));
            $this->addColumnIfMissing($table, $tableName, 'updated_by', fn($t) => $t->unsignedBigInteger('updated_by')->nullable()->after('created_by'));
            $this->addColumnIfMissing($table, $tableName, 'meta', fn($t) => $t->json('meta')->nullable()->after('updated_by'));
        });

        if (!Schema::hasTable('attendance_events')) {
            Schema::create('attendance_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attendance_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->unsignedBigInteger('planner_plan_id')->nullable()->index();
                $table->unsignedBigInteger('planner_item_id')->nullable()->index();
                $table->string('event_type')->index();
                $table->dateTime('event_at')->index();
                $table->decimal('lat', 11, 8)->nullable();
                $table->decimal('lng', 11, 8)->nullable();
                $table->text('destination')->nullable();
                $table->text('note')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('attendance_locations')) {
            Schema::create('attendance_locations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attendance_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->unsignedBigInteger('planner_plan_id')->nullable()->index();
                $table->decimal('lat', 11, 8);
                $table->decimal('lng', 11, 8);
                $table->decimal('accuracy', 10, 2)->nullable();
                $table->decimal('speed', 10, 2)->nullable();
                $table->decimal('heading', 10, 2)->nullable();
                $table->text('destination')->nullable();
                $table->dateTime('recorded_at')->index();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_locations');
        Schema::dropIfExists('attendance_events');
    }
};
