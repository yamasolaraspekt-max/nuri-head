<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Stufe B: Abnahme-Phase im Kanban aktivieren. Die lead_stages-Zeile (key='abnahme')
     * existiert bereits, war aber is_active=0. Erscheint danach als (heute leere) 6. Spalte —
     * korrekt, es gibt noch keine Abnahme-Karten. Kein Schema-Change, reine Daten-Aktivierung.
     */
    public function up(): void
    {
        DB::table('lead_stages')->where('key', 'abnahme')->update(['is_active' => 1]);
    }

    public function down(): void
    {
        DB::table('lead_stages')->where('key', 'abnahme')->update(['is_active' => 0]);
    }
};
