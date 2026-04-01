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
        Schema::create('project_time_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('section_id')->nullable(); // phase_section / service_id

            $table->unsignedBigInteger('requested_by'); // employees.id (from auth()->user()->name)
            $table->unsignedBigInteger('approved_by')->nullable();

            // minutes requested as extension
            $table->integer('extra_minutes');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $table->text('reason')->nullable();        // why more time is needed
            $table->text('answer')->nullable();        // manager comment

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // FKs (adapt ON DELETE if you prefer)
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_time_requests');
    }
};
