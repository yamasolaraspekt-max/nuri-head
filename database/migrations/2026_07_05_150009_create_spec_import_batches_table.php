<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spec-Standard (Baustufe 2): Lauf-Metadaten je spec:import-Batch — NUR Modus/Zählung, KEINE Zeilen-Vorwerte
 * (keine Schatten-Historie). Ermöglicht dem Batch-Rückbau, Update-Läufe ehrlich abzulehnen (überschriebene
 * Vorwerte sind nicht wiederherstellbar). Nur ticket_testing; main = M5-Deploy-Paket.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spec_import_batches', function (Blueprint $table) {
            $table->uuid('id')->primary(); // = import_batch_id
            $table->string('geraetetyp');
            $table->string('modus')->comment('insert | update');
            $table->unsignedInteger('anzahl_angelegt')->default(0);
            $table->unsignedInteger('anzahl_aktualisiert')->default(0);
            $table->string('quelle')->nullable()->comment('imported_from');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spec_import_batches');
    }
};
