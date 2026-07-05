<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2a-3 — Gebäude-Geometrie am Anforderungsprofil (Entscheidung a2): der Rechen-Input des
 * HeizlastRechners (raeume[] + bauteile[] mit u_wert + u_wert_datenlage + quelle) als JSON-Spalte
 * am Profil-Kopf — reproduzierbar (Teil der eingefrorenen Version, Mitkopie bei neueVersion()),
 * kein EAV-Missbrauch (struktureller Blob, kein Bedarfswert-mit-Datenlage). Typisch wenige KB.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anforderungsprofile', function (Blueprint $table) {
            $table->json('gebaeude_geometrie')->nullable()->after('bezeichnung');
        });
    }

    public function down(): void
    {
        Schema::table('anforderungsprofile', function (Blueprint $table) {
            $table->dropColumn('gebaeude_geometrie');
        });
    }
};
