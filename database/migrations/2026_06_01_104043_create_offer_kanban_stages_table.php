<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offer_kanban_stages', function (Blueprint $table) {
            $table->id();
            $table->string('document_status', 30)->index(); // offer | deal/auftrag
            $table->string('key', 120);
            $table->string('label', 255);
            $table->string('icon', 80)->nullable();
            $table->string('color', 30)->default('#93c21c');
            $table->unsignedInteger('position')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['document_status', 'key'], 'offer_kanban_stage_status_key_unique');
        });

        $now = now();

        $offerStages = [
            ['lead_anfrage', 'Lead / Anfrage', 'users'],
            ['erstkontakt', 'Erstkontakt', 'phone'],
            ['beratung_geplant', 'Beratung geplant', 'calendar'],
            ['beratung_durchgefuehrt', 'Beratung durchgeführt', 'check'],
            ['daten_unterlagen_fehlen', 'Daten / Unterlagen fehlen', 'alert'],
            ['technische_pruefung', 'Technische Prüfung', 'tools'],
            ['angebot_in_erstellung', 'Angebot in Erstellung', 'edit'],
            ['angebot_versendet', 'Angebot versendet', 'send'],
            ['rueckfrage_nachbearbeitung', 'Rückfrage / Nachbearbeitung', 'message'],
            ['warten_auf_entscheidung', 'Warten auf Entscheidung', 'clock'],
            ['angebot_angenommen', 'Angebot angenommen', 'check-circle'],
            ['angebot_abgelehnt', 'Angebot abgelehnt', 'x-circle'],
            ['angebot_pausiert', 'Angebot pausiert', 'pause'],
        ];

        $dealStages = [
            ['auftrag_erhalten', 'Auftrag erhalten', 'briefcase'],
            ['auftragspruefung', 'Auftragsprüfung', 'search'],
            ['vertrag_bestaetigung_versendet', 'Vertrag / Bestätigung versendet', 'file-check'],
            ['anzahlung_offen', 'Anzahlung offen', 'euro'],
            ['anzahlung_erhalten', 'Anzahlung erhalten', 'wallet'],
            ['materialplanung', 'Materialplanung', 'package'],
            ['material_bestellt', 'Material bestellt', 'truck'],
            ['material_vollstaendig_verfuegbar', 'Material vollständig verfügbar', 'boxes'],
            ['montage_terminplanung', 'Montage / Terminplanung', 'calendar'],
            ['montagetermin_bestaetigt', 'Montagetermin bestätigt', 'calendar-check'],
            ['in_ausfuehrung', 'In Ausführung', 'hammer'],
            ['montage_abgeschlossen', 'Montage abgeschlossen', 'check'],
            ['abnahme_qualitaetskontrolle', 'Abnahme / Qualitätskontrolle', 'shield-check'],
            ['rechnung_erstellt', 'Rechnung erstellt', 'receipt'],
            ['rechnung_bezahlt', 'Rechnung bezahlt', 'credit-card'],
            ['abgeschlossen', 'Abgeschlossen', 'flag'],
            ['problem_reklamation', 'Problem / Reklamation', 'alert-triangle'],
        ];

        foreach ($offerStages as $i => [$key, $label, $icon]) {
            DB::table('offer_kanban_stages')->insert([
                'document_status' => 'offer',
                'key' => $key,
                'label' => $label,
                'icon' => $icon,
                'color' => '#93c21c',
                'position' => $i + 1,
                'is_active' => true,
                'is_default' => $i === 0,
                'is_system' => in_array($key, ['lead_anfrage', 'angebot_angenommen', 'angebot_abgelehnt'], true),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($dealStages as $i => [$key, $label, $icon]) {
            DB::table('offer_kanban_stages')->insert([
                'document_status' => 'deal',
                'key' => $key,
                'label' => $label,
                'icon' => $icon,
                'color' => '#93c21c',
                'position' => $i + 1,
                'is_active' => true,
                'is_default' => $i === 0,
                'is_system' => in_array($key, ['auftrag_erhalten', 'abgeschlossen', 'problem_reklamation'], true),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_kanban_stages');
    }
};
