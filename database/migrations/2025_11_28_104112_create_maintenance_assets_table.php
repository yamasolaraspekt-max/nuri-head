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
        if (!Schema::hasTable('maintenance_assets')) {
        Schema::create('maintenance_assets', function (Blueprint $table) {
            $table->id();

            // Kunde / Objekt
            $table->unsignedBigInteger('lead_id');              // new_leads.id
            $table->unsignedBigInteger('alternative_id');       // lead_alternative_adds.id (Objekt)

            // Bezug zu Verkaufs-/Projektposition (optional)
            $table->unsignedBigInteger('lead_product_list_id')->nullable(); // lead_product_lists.id
            $table->unsignedBigInteger('product_id')->nullable();           // article_groups.id

            // Vertrag (optional, 1 Vertrag -> viele Assets)
            $table->unsignedBigInteger('maintenance_contract_id')->nullable();

            // Default-Checkliste (z.B. "WP Wartung")
            $table->unsignedBigInteger('maintenance_checklist_id')->nullable();

            // Stammdaten der Anlage
            $table->string('asset_no')->nullable()->unique();   // interne Anlagennummer
            $table->string('title')->nullable();                            // z.B. "Wärmepumpe EG"
            $table->string('serial_no')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();

            // Status / Lebenszyklus
            $table->string('status')->default('active');        // active / inactive / decommissioned

            // Einbau-/Betriebsdaten
            $table->date('installation_date')->nullable();
            $table->date('warranty_until')->nullable();
            $table->string('location_name')->nullable();        // z.B. "Keller vorne", "Dachgeschoss"
            $table->text('location_note')->nullable();

            // Technische Kenndaten optional
            $table->decimal('power_kw', 10, 2)->nullable();
            $table->decimal('volume_liters', 10, 2)->nullable();
            $table->json('technical_data')->nullable();         // frei: z.B. JSON-Felder

            $table->json('meta')->nullable();                   // Sonstige Metadaten

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('new_leads')->cascadeOnDelete(); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->cascadeOnDelete(); 
            $table->foreign('lead_product_list_id')->references('id')->on('lead_product_lists')->nullOnDelete(); 
            $table->foreign('product_id')->references('id')->on('article_groups')->nullOnDelete(); 
            $table->foreign('maintenance_contract_id')->references('id')->on('maintenance_contracts')->nullOnDelete(); 
            $table->foreign('maintenance_checklist_id')->references('id')->on('maintenance_checklists')->nullOnDelete();

            $table->index(['lead_id', 'alternative_id']);
            $table->index(['status']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_assets');
    }
};
