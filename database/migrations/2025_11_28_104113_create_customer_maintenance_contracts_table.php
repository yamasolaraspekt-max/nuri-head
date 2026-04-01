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
        Schema::create('customer_maintenance_contracts', function (Blueprint $table) {
            $table->id();

            // Basic links
            $table->unsignedBigInteger('lead_id');                 // new_leads.id
            $table->unsignedBigInteger('alternative_id')->nullable(); // lead_alternative_adds.id

            // Optional direct links (keep if you want to link to separate tables)
            $table->unsignedBigInteger('maintenance_contract_id')->nullable(); // maintenance_contracts.id (optional)
            $table->unsignedBigInteger('asset_id')->nullable();                // maintenance_assets.id

            // Contract meta
            $table->string('contract_no')->nullable()->unique();
            $table->string('title');

            $table->string('contract_type')->nullable();   // z.B. Wartungsvertrag, Servicevertrag
            $table->string('billing_mode')->nullable();    // z.B. Pauschale, nach Aufwand

            // Dates
            $table->date('next_service_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('cancelled_at')->nullable();

            // Interval
            $table->string('interval_type')->default('yearly'); // yearly / monthly / custom
            $table->unsignedInteger('interval_months')->nullable();

            // Status
            $table->string('status')->default('active');      // draft / active / inactive / cancelled
            $table->string('status_overall')->nullable();     // aus Wizard summary.statusOverall

            // Extra contract parameters from Wizard
            $table->unsignedInteger('contract_duration_months')->nullable();
            $table->unsignedInteger('termination_notice_days')->nullable();
            $table->unsignedInteger('recommended_interval_months')->nullable();

            // Pricing
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->default('EUR');

            // Text fields
            $table->text('description')->nullable();      // summary.summaryForCustomer
            $table->text('terms')->nullable();            // summary.notesInternal (oder AGB)
            $table->text('internal_notes')->nullable();   // contract.internalNotes

            // JSON payloads
            $table->json('payload')->nullable();          // kompletter Wizard-Payload (Block 5.2)
            $table->json('meta')->nullable();             // flexible Zusatzinfos (Block 5.3)

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // FKs
            $table->foreign('lead_id')
                ->references('id')
                ->on('new_leads')
                ->cascadeOnDelete();

            $table->foreign('alternative_id')
                ->references('id')
                ->on('lead_alternative_adds')
                ->nullOnDelete();

            $table->foreign('asset_id')
                ->references('id')
                ->on('maintenance_assets')
                ->nullOnDelete();

            // Optional, only if you really have maintenance_contracts table:
            $table->foreign('maintenance_contract_id')
                ->references('id')
                ->on('maintenance_contracts')
                ->nullOnDelete();

            // Indexes
            $table->index(['lead_id', 'alternative_id']);
            $table->index(['status', 'interval_type']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_maintenance_contracts');
    }
};
