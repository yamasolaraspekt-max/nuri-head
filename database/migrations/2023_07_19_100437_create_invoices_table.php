<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Optional multi-tenant
            $table->unsignedBigInteger('account_id')->nullable()->index();

            // Your "customers" table is new_leads
            $table->unsignedBigInteger('customer_id')->index();

            // Your "objects" table is lead_alternative_adds (object_name, address, etc.)
            $table->unsignedBigInteger('object_id')->nullable()->index();

            $table->string('invoice_no', 50)->index(); // Rechnung-Nummer

            $table->string('type', 60)->index(); // Rechnung, Auftrag, Angebot, etc.
            $table->string('status', 30)->default('draft')->index(); // draft|sent|paid|overdue|cancelled

            $table->date('issue_date')->index();
            $table->date('due_date')->nullable()->index();
            $table->date('service_from')->nullable()->index();
            $table->date('service_to')->nullable()->index();

            $table->string('currency', 3)->default('EUR');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_rate', 6, 3)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->text('notes')->nullable();

            // ✅ user tracking
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // IMPORTANT:
            // If account_id is NULL, MySQL allows duplicates on (NULL, invoice_no).
            // If you need global uniqueness when account_id is NULL, also add unique(invoice_no).
            $table->unique(['account_id', 'invoice_no'], 'invoices_account_invoice_no_unique');

            // FKs (adjust onDelete if you prefer restrict)
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('object_id')->references('id')->on('lead_alternative_adds')->nullOnDelete();

            $table->foreign('created_by')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
