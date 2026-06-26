<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dashboard_live_activities', function (Blueprint $table) {
            $table->id();

            // IMPORTANT: this is employees.id, stored in users.name
            $table->string('employee_id')->index();

            $table->unsignedBigInteger('actor_user_id')->nullable()->index();
            $table->string('actor_employee_id')->nullable()->index();

            $table->string('type')->index();
            // task, appointment, lead, inquiry, offer, deal, ticket

            $table->string('action')->default('created')->index();

            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable()->index();

            $table->string('title');
            $table->text('message')->nullable();
            $table->string('url')->nullable();

            $table->json('payload')->nullable();

            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();

            $table->index(['employee_id', 'read_at']);
            $table->index(['employee_id', 'type']);
            $table->index(['employee_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_live_activities');
    }
};