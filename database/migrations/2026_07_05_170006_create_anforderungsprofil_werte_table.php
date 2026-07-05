<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2a-2 — Anforderungsprofil-Werte (EAV): jeder Bedarfswert trägt seine Datenlage-Stufe.
 * Bewusst EAV (kein Widerspruch zur typisierten-Spec-Regel des Katalogs): hier ist die Metadaten-Zeile
 * JE WERT (datenlage/quelle/erfassungsweg) das Kern-Feature; feste Spalten wären Spalten-Explosion.
 * Gezähmt durch die Schlüssel-Registry (App\Services\Anforderungsprofil\SchluesselRegistry) — der Vertrag,
 * gegen den geschrieben wird (unbekannter schluessel = Fehler) und den der B2a-3-Adapter liest.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anforderungsprofil_werte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anforderungsprofil_id')->constrained('anforderungsprofile')->cascadeOnDelete();
            $table->string('schluessel');                       // Registry-geprüft
            $table->string('wert');                             // Rohwert (Konsument castet je Registry-Cast)
            $table->decimal('wert_num', 12, 4)->nullable();     // numerische Spiegelung (Filter/Rechnung)
            $table->string('einheit')->nullable();              // kW | °C | 1/h … (Einheit-im-Wert wie Spec-Standard)
            $table->string('datenlage');                        // gemessen | berechnet | geschaetzt
            $table->string('quelle')->nullable();               // "HeizlastRechner" | "klima_plz 01067" | "Kundenangabe"
            $table->string('erfassungsweg')->nullable();        // manuell | import | berechnet | default
            $table->timestamps();

            $table->unique(['anforderungsprofil_id', 'schluessel']); // ein Wert je Schlüssel je Version
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anforderungsprofil_werte');
    }
};
