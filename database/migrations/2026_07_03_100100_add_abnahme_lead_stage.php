<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Leads-Kanban Stufe A: Phase "Abnahme" in die lead_stages-Taxonomie ergaenzen.
 *
 * Einordnung zwischen `project`/Montage (sort 60) und `completed`/Abschluss (sort 70)
 * -> sort_order 65. is_active = 0: die Zeile existiert (fuer FK-Aufloesung / spaetere
 * Nutzung), erscheint aber NICHT als Board-Spalte (leadStagesForUi filtert is_active=1)
 * -> Board bleibt in Stufe A visuell unveraendert. Aktivierung + Spalte kommen in Stufe B.
 * Key NICHT umbenennen bestehender Stufen; nur diese neue Zeile.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lead_stages')) {
            return;
        }

        if (!DB::table('lead_stages')->where('key', 'abnahme')->exists()) {
            DB::table('lead_stages')->insert([
                'key'          => 'abnahme',
                'name'         => 'Abnahme',
                'color'        => null,
                'icon'         => null,
                'sort_order'   => 65,
                'is_default'   => 0,
                'is_protected' => 0,
                'is_closed'    => 0,
                'is_active'    => 0, // in Taxonomie, aber (noch) keine Board-Spalte
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lead_stages')) {
            // Hard-Delete der Taxonomie-Zeile (SoftDeletes am Model umgangen via Query Builder).
            DB::table('lead_stages')->where('key', 'abnahme')->delete();
        }
    }
};
