<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use RuntimeException;

class WaermepumpeKomplettloesungSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            // Fixed from your UI wrapper
            $productId = 16;
            $sectionId = 1;
            $version   = '1';

            // Safety checks
            if (!DB::table('article_groups')->where('id', $productId)->exists()) {
                throw new RuntimeException("article_groups id {$productId} does not exist.");
            }

            if (!DB::table('phase_sections')->where('id', $sectionId)->exists()) {
                throw new RuntimeException("phase_sections id {$sectionId} does not exist.");
            }

            $stageMap = [
                'st_01' => 158, // Bedarfsanalyse
                'st_02' => 1,   // Angebot
                'st_03' => 2,   // Auftrag
                'st_04' => 159, // Planung -> Verkauf bucket
                'st_05' => 3,   // Ausführung -> Montage
                'st_06' => 4,   // Inbetriebnahme -> Abschluss bucket
                'st_07' => 4,   // Abnahme -> Abschluss bucket
                'st_08' => 5,   // Nachoptimierung -> Auswertung
                'st_09' => 5,   // Service -> Auswertung
                'st_10' => 6,   // Archiv
            ];

            foreach ($stageMap as $tempRef => $stageId) {
                if (!DB::table('stages')->where('id', $stageId)->exists()) {
                    throw new RuntimeException("Mapped stage id {$stageId} for {$tempRef} does not exist.");
                }
            }

            /*
             |------------------------------------------------------------
             | TASK PHASES
             |------------------------------------------------------------
             | section_id is ALWAYS 1
             | product_id is ALWAYS 16
             | version is ALWAYS '1'
             */
            $taskPhases = [
                ['temp_id' => 'ph_0101', 'section_name' => 'Vertrieb',                    'phase_name' => 'Erstkontakt & Bedarfsanalyse',                   'stage_ref' => 'st_01', 'status' => 'Published', 'order' => 10],
                ['temp_id' => 'ph_0102', 'section_name' => 'Vertrieb',                    'phase_name' => 'Datenerfassung & Unterlagen',                    'stage_ref' => 'st_01', 'status' => 'Published', 'order' => 20],
                ['temp_id' => 'ph_0103', 'section_name' => 'Vertrieb',                    'phase_name' => 'Vor-Ort-Termin / Aufmaß',                        'stage_ref' => 'st_01', 'status' => 'Published', 'order' => 30],

                ['temp_id' => 'ph_0201', 'section_name' => 'Technik & Auslegung',         'phase_name' => 'Heizlast & Auslegung',                            'stage_ref' => 'st_04', 'status' => 'Published', 'order' => 40],
                ['temp_id' => 'ph_0202', 'section_name' => 'Technik & Auslegung',         'phase_name' => 'Systemkonzept & Hydraulik',                       'stage_ref' => 'st_04', 'status' => 'Published', 'order' => 50],
                ['temp_id' => 'ph_0203', 'section_name' => 'Technik & Auslegung',         'phase_name' => 'Aufstellort, Schall & Leitungswege',             'stage_ref' => 'st_04', 'status' => 'Published', 'order' => 60],

                ['temp_id' => 'ph_0301', 'section_name' => 'Fördermittel & Formalien',    'phase_name' => 'Fördercheck & Voraussetzungen',                   'stage_ref' => 'st_04', 'status' => 'Published', 'order' => 70],
                ['temp_id' => 'ph_0302', 'section_name' => 'Fördermittel & Formalien',    'phase_name' => 'Formalien, Nachweise, Fristen',                   'stage_ref' => 'st_04', 'status' => 'Published', 'order' => 80],

                ['temp_id' => 'ph_0401', 'section_name' => 'Vertrieb',                    'phase_name' => 'Angebotserstellung',                              'stage_ref' => 'st_02', 'status' => 'Published', 'order' => 90],
                ['temp_id' => 'ph_0402', 'section_name' => 'Vertrieb',                    'phase_name' => 'Angebotsbesprechung & Anpassungen',               'stage_ref' => 'st_02', 'status' => 'Published', 'order' => 100],

                ['temp_id' => 'ph_0501', 'section_name' => 'Planung & Disposition',       'phase_name' => 'Auftrag, Terminfixierung, Einsatzplanung',        'stage_ref' => 'st_03', 'status' => 'Published', 'order' => 110],
                ['temp_id' => 'ph_0601', 'section_name' => 'Planung & Disposition',       'phase_name' => 'Materialdisposition & Lieferkoordination',        'stage_ref' => 'st_04', 'status' => 'Published', 'order' => 120],
                ['temp_id' => 'ph_0602', 'section_name' => 'Planung & Disposition',       'phase_name' => 'Baustellenvorbereitung (intern)',                 'stage_ref' => 'st_04', 'status' => 'Published', 'order' => 130],

                ['temp_id' => 'ph_0701', 'section_name' => 'Baustelle Außen',             'phase_name' => 'Fundament/Podest & Montage Außeneinheit',         'stage_ref' => 'st_05', 'status' => 'Published', 'order' => 140],
                ['temp_id' => 'ph_0702', 'section_name' => 'Baustelle Außen',             'phase_name' => 'Außenleitungen, Durchführungen, Abdichtung',      'stage_ref' => 'st_05', 'status' => 'Published', 'order' => 150],

                ['temp_id' => 'ph_0801', 'section_name' => 'Baustelle Innen',             'phase_name' => 'Demontage Altanlage',                             'stage_ref' => 'st_05', 'status' => 'Published', 'order' => 160],
                ['temp_id' => 'ph_0802', 'section_name' => 'Baustelle Innen',             'phase_name' => 'Montage Inneneinheit & Komponenten',              'stage_ref' => 'st_05', 'status' => 'Published', 'order' => 170],
                ['temp_id' => 'ph_0901', 'section_name' => 'Baustelle Innen',             'phase_name' => 'Hydraulik: Spülen, Befüllen, Entlüften, Prüfung', 'stage_ref' => 'st_05', 'status' => 'Published', 'order' => 180],

                ['temp_id' => 'ph_1001', 'section_name' => 'Elektro',                     'phase_name' => 'Elektroinstallation Wärmepumpe',                  'stage_ref' => 'st_05', 'status' => 'Published', 'order' => 190],

                ['temp_id' => 'ph_1101', 'section_name' => 'Inbetriebnahme & Einweisung', 'phase_name' => 'Inbetriebnahme & Parametrierung',                 'stage_ref' => 'st_06', 'status' => 'Published', 'order' => 200],
                ['temp_id' => 'ph_1102', 'section_name' => 'Inbetriebnahme & Einweisung', 'phase_name' => 'Einweisung & Übergabe',                           'stage_ref' => 'st_06', 'status' => 'Published', 'order' => 210],

                ['temp_id' => 'ph_1201', 'section_name' => 'Dokumentation & Abnahme',     'phase_name' => 'Dokumentation & Abnahmeprotokoll',                'stage_ref' => 'st_07', 'status' => 'Published', 'order' => 220],

                ['temp_id' => 'ph_1301', 'section_name' => 'Nachlauf & Archiv',           'phase_name' => 'Nachoptimierung (Kontrolltermin)',                'stage_ref' => 'st_08', 'status' => 'Published', 'order' => 230],
                ['temp_id' => 'ph_1401', 'section_name' => 'Nachlauf & Archiv',           'phase_name' => 'Serviceübergabe / Wartungsoption',                'stage_ref' => 'st_09', 'status' => 'Published', 'order' => 240],
                ['temp_id' => 'ph_1501', 'section_name' => 'Nachlauf & Archiv',           'phase_name' => 'Archivierung & Abschluss',                        'stage_ref' => 'st_10', 'status' => 'Published', 'order' => 250],
            ];

            $activities = [
                ['phase_ref' => 'ph_0101', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Erstkontakt: Wunsch, Zeitrahmen, Ziele klären',                              'duration' => '00:20:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0101', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Bedarfsanalyse: Heizsystem, Verbrauch, Wohnfläche, Warmwasser',                'duration' => '00:40:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_0101', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Termin & nächster Schritt bestätigen',                                   'duration' => '00:10:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_0102', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Fotos/Unterlagen anfordern (Heizraum, Außenseite, Zählerschrank falls nötig)', 'duration' => '00:20:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0102', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Daten erfassen: Baujahr, Dämmung, Heizflächen, Vorlauftemperatur',          'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_0102', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Risiken/Offene Punkte dokumentieren',                                     'duration' => '00:20:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_0103', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Vor-Ort: Leitungswege, Aufstellort, Geräuschthemen prüfen',               'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0103', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Vor-Ort: Heizraum-Aufmaß & Anschlusspunkte aufnehmen',                     'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_0103', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_01', 'initial' => '•', 'title' => 'Fotodokumentation & Protokoll',                                             'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_0201', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Heizlast ermitteln (Daten prüfen, Ansatz dokumentieren)',                 'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0201', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Betriebspunkt prüfen: Vorlauf/Heizflächen/Abtaureserve',                  'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_0201', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Wärmepumpe dimensionieren (Modell/Leistung) und begründen',               'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_0202', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Hydraulikkonzept: Speicher ja/nein, Trennung, Sicherheitsgruppe',         'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0202', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Bestand prüfen: Heizkreis, Mischer, Warmwasser, Zirkulation',            'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_0202', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Empfehlungen: Heizflächen/Abgleich/Optimierung',                          'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_0203', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Aufstellort finalisieren: Abstände, Servicezugang, Luftführung',         'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0203', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Schallbewertung: Aufstellung, Entkopplung, Maßnahmen',                   'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_0203', 'section_name' => 'Technik & Auslegung',         'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Leitungswege: Durchführungen, Abdichtung, Kondensatführung',             'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_0301', 'section_name' => 'Fördermittel & Formalien',    'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Förderfähigkeit prüfen (Maßnahmen, Voraussetzungen, Nachweise)',          'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0301', 'section_name' => 'Fördermittel & Formalien',    'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Kundenhinweise: Fristen, benötigte Unterlagen, Ablauf',                   'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0302', 'section_name' => 'Fördermittel & Formalien',    'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Nachweise sammeln: Datenblätter, Fachunternehmererklärung (Plan)',      'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0302', 'section_name' => 'Fördermittel & Formalien',    'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Dokumentationsstruktur vorbereiten (Ordner, Belege, Fotos)',             'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0401', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_02', 'initial' => '•', 'title' => 'Kalkulation: Material, Montage, Zusatzarbeiten, Optionen',              'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0401', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_02', 'initial' => '•', 'title' => 'Angebot erstellen: Positionen, Varianten, Zeitplan, Hinweise',          'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_0401', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_02', 'initial' => '•', 'title' => 'Interne Qualitätsprüfung (Vollständigkeit/Logik)',                      'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_0402', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_02', 'initial' => '•', 'title' => 'Angebot erläutern (Technik, Nutzen, Kosten, Ablauf)',                  'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0402', 'section_name' => 'Vertrieb',                    'stage_ref' => 'st_02', 'initial' => '•', 'title' => 'Anpassungen/Optionen abstimmen und dokumentieren',                      'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0501', 'section_name' => 'Planung & Disposition',       'stage_ref' => 'st_03', 'initial' => '•', 'title' => 'Auftrag prüfen: Umfang, Termine, Zugänge, Zuständigkeiten',             'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0501', 'section_name' => 'Planung & Disposition',       'stage_ref' => 'st_03', 'initial' => '•', 'title' => 'Terminfixierung, Teamplanung, Meilensteine festlegen',                  'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0601', 'section_name' => 'Planung & Disposition',       'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Material bestellen/abrufen, Liefertermine sichern',                     'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0601', 'section_name' => 'Planung & Disposition',       'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Kommissionierung: Kleinteile, Fittings, Dämmung, Befestigung',          'duration' => '00:45:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0602', 'section_name' => 'Planung & Disposition',       'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Baustelleneinrichtung planen (Schutz, Abdeckung, Abstellflächen)',     'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0602', 'section_name' => 'Planung & Disposition',       'stage_ref' => 'st_04', 'initial' => '•', 'title' => 'Kundeninfo: Ablauf, kurze Unterbrechungen, Sicherheitszonen',           'duration' => '00:20:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0701', 'section_name' => 'Baustelle Außen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Fundament/Podest herstellen oder prüfen (Entkopplung)',                 'duration' => '02:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0701', 'section_name' => 'Baustelle Außen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Außeneinheit montieren, ausrichten, sichern',                            'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0702', 'section_name' => 'Baustelle Außen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Durchführungen herstellen, abdichten, mechanisch schützen',            'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0702', 'section_name' => 'Baustelle Außen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Außenleitungen führen, befestigen, Schutzrohre/Dämmung',               'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0801', 'section_name' => 'Baustelle Innen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Altanlage außer Betrieb, Entleerung, Sicherung',                        'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0801', 'section_name' => 'Baustelle Innen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Demontage Altgerät, Abtransport/Entsorgung nach Vereinbarung',         'duration' => '02:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0802', 'section_name' => 'Baustelle Innen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Inneneinheit montieren, Speicher/Komponenten setzen',                   'duration' => '02:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0802', 'section_name' => 'Baustelle Innen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Anschluss Vorlauf/Rücklauf, Armaturen, Sicherheitsgruppe',              'duration' => '02:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_0901', 'section_name' => 'Baustelle Innen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Spülen (falls erforderlich), Anlage befüllen, entlüften',               'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_0901', 'section_name' => 'Baustelle Innen',             'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Druck-/Dichtheitsprüfung, erste Durchflusskontrolle',                    'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_1001', 'section_name' => 'Elektro',                     'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Stromversorgung/Absicherung herstellen',                                 'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_1001', 'section_name' => 'Elektro',                     'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Anschluss Wärmepumpe, Regelung, Fühler, Kommunikation',                 'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_1001', 'section_name' => 'Elektro',                     'stage_ref' => 'st_05', 'initial' => '•', 'title' => 'Schutzprüfung, Beschriftung, Dokumentation',                             'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_1101', 'section_name' => 'Inbetriebnahme & Einweisung', 'stage_ref' => 'st_06', 'initial' => '•', 'title' => 'Erstinbetriebnahme, Funktionsprüfung, Geräuschkontrolle',               'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_1101', 'section_name' => 'Inbetriebnahme & Einweisung', 'stage_ref' => 'st_06', 'initial' => '•', 'title' => 'Parametrierung: Heizkurve, Warmwasser, Zeiten, Sperren',                 'duration' => '01:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
                ['phase_ref' => 'ph_1101', 'section_name' => 'Inbetriebnahme & Einweisung', 'stage_ref' => 'st_06', 'initial' => '•', 'title' => 'Protokollierung der Einstellungen und Messwerte',                         'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 30],

                ['phase_ref' => 'ph_1102', 'section_name' => 'Inbetriebnahme & Einweisung', 'stage_ref' => 'st_06', 'initial' => '•', 'title' => 'Kundeneinweisung: Bedienung, Programme, Störungsanzeige',               'duration' => '00:40:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_1102', 'section_name' => 'Inbetriebnahme & Einweisung', 'stage_ref' => 'st_06', 'initial' => '•', 'title' => 'Hinweise zum effizienten Betrieb und Verhalten im Winter',               'duration' => '00:20:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_1201', 'section_name' => 'Dokumentation & Abnahme',     'stage_ref' => 'st_07', 'initial' => '•', 'title' => 'Dokumentation vollständig machen (Fotos, Protokolle, Datenblätter)',   'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_1201', 'section_name' => 'Dokumentation & Abnahme',     'stage_ref' => 'st_07', 'initial' => '•', 'title' => 'Abnahmeprotokoll erstellen und unterschreiben lassen',                  'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_1301', 'section_name' => 'Nachlauf & Archiv',           'stage_ref' => 'st_08', 'initial' => '•', 'title' => 'Kontrolltermin: Werte prüfen, Heizkurve feinjustieren',                 'duration' => '01:00:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_1301', 'section_name' => 'Nachlauf & Archiv',           'stage_ref' => 'st_08', 'initial' => '•', 'title' => 'Kurzbericht an Kunden, Empfehlung für Einstellungen',                    'duration' => '00:20:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_1401', 'section_name' => 'Nachlauf & Archiv',           'stage_ref' => 'st_09', 'initial' => '•', 'title' => 'Serviceübergabe: Ansprechpartner, Reaktionswege, Wartungsoption',      'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_1401', 'section_name' => 'Nachlauf & Archiv',           'stage_ref' => 'st_09', 'initial' => '•', 'title' => 'Wartungsplan und Erinnerungslogik hinterlegen (falls genutzt)',        'duration' => '00:20:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],

                ['phase_ref' => 'ph_1501', 'section_name' => 'Nachlauf & Archiv',           'stage_ref' => 'st_10', 'initial' => '•', 'title' => 'Projektabschluss: Vollständigkeit prüfen (Unterlagen/Medien)',         'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 10],
                ['phase_ref' => 'ph_1501', 'section_name' => 'Nachlauf & Archiv',           'stage_ref' => 'st_10', 'initial' => '•', 'title' => 'Archivierung: Ablage, Versionierung, Abschlussstatus setzen',          'duration' => '00:30:00', 'duration_type' => 'Richtwert', 'status' => 'Geplant', 'priority' => 'Normal', 'percent' => 0.0, 'sort_order' => 20],
            ];

            // Count activities per phase
            $activityCountPerPhase = [];
            foreach ($activities as $activity) {
                $phaseRef = $activity['phase_ref'];
                $activityCountPerPhase[$phaseRef] = ($activityCountPerPhase[$phaseRef] ?? 0) + 1;
            }

            // Insert task phases
            $phaseIdMap = [];

            foreach ($taskPhases as $phase) {
                $stageId = $stageMap[$phase['stage_ref']];

                $existingId = DB::table('task_phases')
                    ->where('product_id', $productId)
                    ->where('section_id', $sectionId)
                    ->where('stage_id', $stageId)
                    ->where('version', $version)
                    ->where('phase_name', $phase['phase_name'])
                    ->value('id');

                if ($existingId) {
                    DB::table('task_phases')
                        ->where('id', $existingId)
                        ->update([
                            'section_name' => $phase['section_name'],
                            'stage'        => 'project',
                            'status'       => $phase['status'],
                            'count'        => $activityCountPerPhase[$phase['temp_id']] ?? 0,
                            'order'        => $phase['order'],
                            'updated_at'   => $now,
                        ]);

                    $phaseIdMap[$phase['temp_id']] = (int) $existingId;
                    continue;
                }

                $newId = DB::table('task_phases')->insertGetId([
                    'product_id'   => $productId,
                    'section_id'   => $sectionId,
                    'section_name' => $phase['section_name'],
                    'phase_name'   => $phase['phase_name'],
                    'stage'        => 'project',
                    'stage_id'     => $stageId,
                    'version'      => $version,
                    'status'       => $phase['status'],
                    'count'        => $activityCountPerPhase[$phase['temp_id']] ?? 0,
                    'order'        => $phase['order'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                $phaseIdMap[$phase['temp_id']] = $newId;
            }

            // Insert activities
            foreach ($activities as $activity) {
                $phaseId = $phaseIdMap[$activity['phase_ref']];
                $stageId = $stageMap[$activity['stage_ref']];

                $exists = DB::table('phase_activities')
                    ->where('phase_id', $phaseId)
                    ->where('product_id', $productId)
                    ->where('section_id', $sectionId)
                    ->where('stage_id', $stageId)
                    ->where('version', $version)
                    ->where('title', $activity['title'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('phase_activities')->insert([
                    'phase_id'       => $phaseId,
                    'product_id'     => $productId,
                    'section_id'     => $sectionId,
                    'parent_id'      => null,
                    'copy_from'      => null,
                    'stage_id'       => $stageId,
                    'version'        => $version,
                    'section_name'   => $activity['section_name'],
                    'initial'        => $activity['initial'],
                    'title'          => $activity['title'],
                    'duration'       => $activity['duration'],
                    'duration_type'  => $activity['duration_type'],
                    'description'    => null,
                    'notes'          => null,
                    'status'         => $activity['status'],
                    'photo'          => null,
                    'link'           => null,
                    'priority'       => $activity['priority'],
                    'percent'        => $activity['percent'],
                    'usage_count'    => null,
                    'rating'         => null,
                    'answered_by'    => 2,
                    'sort_order'     => $activity['sort_order'],
                    'copy_count'     => null,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        });
    }
}