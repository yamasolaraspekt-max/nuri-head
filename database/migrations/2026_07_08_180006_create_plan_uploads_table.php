<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Plan-Import-Fundament (Transplantat aus wberechnung, A-3a). Hochgeladener Plan
     * (DWG/DXF/PDF/Bild) als Quelle für den Grundriss-Editor. KONSOLIDIERT: create +
     * user_id (Besitzer-Bindung) in einer additiven Migration. Rein additiv — keine
     * Bestandsdaten berührt (Daten-/Ketten-Schutz, CLAUDE.md).
     */
    public function up(): void
    {
        Schema::create('plan_uploads', function (Blueprint $table) {
            $table->id();
            // Besitzer-Bindung (Sicherheitsschuld A-3d): jeder Upload gehört seinem Ersteller.
            // nullable + nullOnDelete: User-Löschung vernichtet keine Fachdaten.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('heizlast_projekt_id')->nullable()->constrained('heizlast_projekte')->nullOnDelete();
            $table->string('original_name');
            $table->string('pfad');                       // Storage-Pfad
            $table->string('mime');
            $table->unsignedBigInteger('groesse_bytes');
            $table->string('typ')->nullable();            // grob: dwg|dxf|pdf|bild
            $table->string('status')->default('neu');     // neu|klassifiziert|verarbeitet|fehler
            $table->float('massstab_mm_pro_einheit')->nullable(); // Kalibrierung (A-3c)
            $table->json('kandidat_geometrie')->nullable(); // extrahierte Geometrie vor Editor-Seed
            $table->string('konfidenz')->nullable();        // hoch|mittel|niedrig je Importweg
            $table->json('meta')->nullable();             // Klassifikations-Details / Fehlertext
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_uploads');
    }
};
