<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Verteilsystem/Heizkreis je Kunde/Objekt. STRUKTUR jetzt; Einrohr-Kaskadenlogik (B6) am Cut-over
 * (Spec in docs/heizkoerper-bauplan.md §6). meta-JSON hält später die Kaskaden-Ergebnisse.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heating_circuits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('new_leads')->nullOnDelete();
            $table->foreignId('alternative_id')->nullable()->constrained('lead_alternative_adds')->nullOnDelete();
            $table->string('name');
            $table->enum('typ', ['zweirohr', 'einrohr'])->default('zweirohr');
            $table->decimal('ziel_vorlauf_c', 4, 1)->nullable();
            $table->decimal('spreizung_k', 3, 1)->nullable();
            $table->unsignedInteger('reihenfolge')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heating_circuits');
    }
};
