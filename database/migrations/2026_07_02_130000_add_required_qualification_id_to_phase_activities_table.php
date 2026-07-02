<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Qualifikations-Fundament B1: Mindest-Qualifikation je Taetigkeitsart.
 *
 * phase_activities.required_qualification_id -> position_qualifications (rang-tragend
 * via sort_order). nullable = nicht jede Taetigkeit braucht eine Mindeststufe
 * (null = keine Anforderung). MIT FK nullOnDelete: stilkonform zu den bestehenden
 * FKs von phase_activities, position_qualifications ist eine stabile Lookup-Tabelle
 * (SoftDeletes -> nullOnDelete feuert nur bei Hard-Delete, Soft-Delete laesst die
 * Zeile + FK gueltig).
 *
 * NUR das Feld - KEINE Pruef-/Schwellen-Logik (das ist B3, spaeter nach Yamas
 * Design-Entscheidung).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('phase_activities') && !Schema::hasColumn('phase_activities', 'required_qualification_id')) {
            Schema::table('phase_activities', function (Blueprint $table) {
                $table->unsignedBigInteger('required_qualification_id')->nullable()->after('answered_by');
                $table->foreign('required_qualification_id')
                      ->references('id')->on('position_qualifications')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('phase_activities') && Schema::hasColumn('phase_activities', 'required_qualification_id')) {
            Schema::table('phase_activities', function (Blueprint $table) {
                $table->dropForeign(['required_qualification_id']);
                $table->dropColumn('required_qualification_id');
            });
        }
    }
};
