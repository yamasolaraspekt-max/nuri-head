<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();

            // Main code: WE-2026-001
            $table->string('code')->unique();

            // Core links
            $table->unsignedBigInteger('customer_id')->nullable();          // new_leads.id
            $table->unsignedBigInteger('object_id')->nullable();            // lead_alternative_adds.id
            $table->unsignedBigInteger('lead_product_list_id')->nullable(); // lead_product_lists.id
            $table->unsignedBigInteger('article_group_id')->nullable();     // article_groups.id
            $table->unsignedBigInteger('department_id')->nullable();        // departments.id

            // Employees
            $table->unsignedBigInteger('accepted_by_employee_id')->nullable();
            $table->unsignedBigInteger('orderer_employee_id')->nullable();
            $table->unsignedBigInteger('created_by_employee_id')->nullable();
            $table->unsignedBigInteger('updated_by_employee_id')->nullable();
            $table->unsignedBigInteger('issued_by_employee_id')->nullable();

            // Goods entry data
            $table->dateTime('received_at')->index();
            $table->string('description');
            $table->longText('note')->nullable();

            // Workflow
            $table->enum('status', ['pending', 'processing', 'completed', 'issued'])->default('pending')->index();
            $table->enum('inspection_status', ['pending', 'ok', 'issue'])->default('pending')->index();
            $table->longText('issue_description')->nullable();

            // Destination / target
            $table->enum('destination', ['lager', 'kommission'])->nullable()->index();
            $table->string('commission_details')->nullable();

            // Optional quantity / pricing
            $table->decimal('qty', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();

            // Warenausgang
            $table->dateTime('outbound_at')->nullable()->index();
            $table->string('outbound_recipient')->nullable();
            $table->string('outbound_project')->nullable();
            $table->unsignedBigInteger('outbound_customer_id')->nullable(); // optional: recipient customer
            $table->unsignedBigInteger('outbound_object_id')->nullable();   // optional: recipient object

            // Flexible data
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('set null');
            $table->foreign('object_id')->references('id')->on('lead_alternative_adds')->onDelete('set null');
            $table->foreign('lead_product_list_id')->references('id')->on('lead_product_lists')->onDelete('set null');
            $table->foreign('article_group_id')->references('id')->on('article_groups')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');

            $table->foreign('accepted_by_employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('orderer_employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('created_by_employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('updated_by_employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('issued_by_employee_id')->references('id')->on('employees')->onDelete('set null');

            $table->foreign('outbound_customer_id')->references('id')->on('new_leads')->onDelete('set null');
            $table->foreign('outbound_object_id')->references('id')->on('lead_alternative_adds')->onDelete('set null');

            // Helpful indexes
            $table->index(['status', 'received_at']);
            $table->index(['department_id', 'status']);
            $table->index(['customer_id', 'object_id']);
            $table->index(['article_group_id', 'lead_product_list_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};