<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Konfig-Tabelle für die Anschlussart-Korrekturfaktoren (B5). STRUKTUR jetzt; Faktor-DATEN
 * am Cut-over aus Norm/Herstellerangabe (Spec docs/heizkoerper-bauplan.md §6). Keine erfundenen Werte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiator_connection_factors', function (Blueprint $table) {
            $table->id();
            $table->enum('anschluss_position', ['seitlich', 'unten', 'mittel', 'wechselseitig']);
            $table->enum('anschluss_fuehrung', ['zweirohr', 'einrohr']);
            $table->string('bauart')->nullable();
            $table->decimal('faktor', 4, 3);
            $table->string('quelle');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiator_connection_factors');
    }
};
