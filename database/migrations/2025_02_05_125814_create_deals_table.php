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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('service');
            $table->unsignedBigInteger('employee_id'); 
            $table->longText('info')->nullable();
            $table->decimal('price', 10,2)->nullable();
            $table->date('sign_date')->nullable();

            $table->string('location')->nullable(); // Ort
            $table->string('order_number')->nullable(); // Auftragsnummer
            $table->string('offer_number')->nullable(); // Angebotsnummer, if applicable
            $table->date('confirmed_at')->nullable(); // Bestätigt am
            $table->date('delivered_at')->nullable(); // Zugestellt am
            $table->unsignedBigInteger('checked_by')->nullable(); // Geprüft durch
            $table->unsignedBigInteger('reviewer_id')->nullable(); // Prüfer

            $table->string('deal_status')->nullable();
            $table->string('status')->nullable();
            $table->string('status_msg')->nullable();
            $table->string('project_status')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->foreign('service_id')->references('id')->on('phase_sections')->onDelete('cascade'); 
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade'); 
         
            $table->foreign('checked_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
