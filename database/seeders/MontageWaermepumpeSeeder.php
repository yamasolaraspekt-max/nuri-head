<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MontageWaermepumpeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            $productId   = 16;
            $sectionId   = 2;
            $sectionName = 'Montage';
            $stageId     = 239;
            $stageName   = 'montage';
            $version     = '1';
            $status      = 'Published';

            $phases = [
                [
                    'order' => 1,
                    'phase_name' => 'Ankunft, Baustelleneinrichtung, Abstimmung',
                    'ziel' => 'Arbeitsbereiche sicher einrichten und letzte Details abstimmen',
                    'dauer' => '30–60 Minuten',
                    'abhaengigkeiten' => [],
                    'parallel_moeglich' => false,
                    'parallel_hinweis' => null,
                    'steps' => [
                        'Anmeldung beim Kunden, kurzer Ablauf- und Standort-Check (Heizraum, Außenaufstellort, Leitungswege)',
                        'Arbeitszonen markieren/absichern, Laufwege im Haus schützen (Abdeckung)',
                        'Materialablage festlegen, Sicherheitsunterweisung im Kurzformat',
                    ],
                ],
                [
                    'order' => 2,
                    'phase_name' => 'Außenarbeiten: Aufstellplatz/Fundament und Montage Außeneinheit',
                    'ziel' => 'Außeneinheit standsicher montieren und Leitungswege vorbereiten',
                    'dauer' => '0,5–1,5 Tage',
                    'abhaengigkeiten' => [1],
                    'parallel_moeglich' => true,
                    'parallel_hinweis' => 'Kann je nach Objekt parallel zu Innenarbeiten laufen, sofern Zugänge und Leitungswege getrennt sind.',
                    'steps' => [
                        'Aufstellort prüfen (Abstände, Schallschutz, Servicezugang)',
                        'Fundament/Podest erstellen oder vorbereiten (je nach Vereinbarung)',
                        'Außeneinheit montieren und ausrichten',
                        'Leitungswege ins Gebäude herstellen, Durchführungen fachgerecht abdichten',
                    ],
                ],
                [
                    'order' => 3,
                    'phase_name' => 'Innenarbeiten: Altanlage außer Betrieb, Demontage, Montage Innenkomponenten',
                    'ziel' => 'Bestandsanlage ersetzen und neue Innenkomponenten installieren',
                    'dauer' => '1–2 Tage',
                    'abhaengigkeiten' => [1],
                    'parallel_moeglich' => true,
                    'parallel_hinweis' => 'Kann parallel zu Phase 2 erfolgen, wenn Außen- und Innenarbeiten organisatorisch getrennt laufen.',
                    'steps' => [
                        'Altanlage sicher außer Betrieb nehmen',
                        'Demontage der alten Heizung (Entsorgung nach Vereinbarung)',
                        'Montage Inneneinheit und Komponenten (z. B. Speicher, Sicherheitsgruppe)',
                        'Anschluss an Heizsystem (Vorlauf/Rücklauf), Absperrungen und Sicherheitseinrichtungen setzen',
                    ],
                ],
                [
                    'order' => 4,
                    'phase_name' => 'Hydraulik: Spülen, Befüllen, Entlüften, Dichtheit/Druck prüfen',
                    'ziel' => 'Heizkreis fachgerecht betriebsbereit machen',
                    'dauer' => '2–6 Stunden',
                    'abhaengigkeiten' => [3],
                    'parallel_moeglich' => false,
                    'parallel_hinweis' => null,
                    'steps' => [
                        'Heizsystem spülen (falls erforderlich/vereinbart)',
                        'Anlage befüllen und entlüften',
                        'Druck- und Dichtheitsprüfung durchführen',
                        'Erste Durchfluss-/Temperaturkontrolle',
                    ],
                ],
                [
                    'order' => 5,
                    'phase_name' => 'Elektroinstallation Wärmepumpe',
                    'ziel' => 'Stromversorgung, Absicherung und Steuerung normgerecht herstellen',
                    'dauer' => '2–6 Stunden',
                    'abhaengigkeiten' => [3],
                    'parallel_moeglich' => true,
                    'parallel_hinweis' => 'Teilweise parallel zu Phase 4 möglich, abhängig vom Objekt und Arbeitsfortschritt.',
                    'steps' => [
                        'Stromversorgung/Absicherung herstellen (Leitungswege, Schutzorgane)',
                        'Anschluss Wärmepumpe, Regelung, Sensoren/Fühler',
                        'Schutz- und Funktionsprüfungen (elektrische Sicherheit)',
                        'Beschriftung und Dokumentation der Stromkreise',
                    ],
                ],
                [
                    'order' => 6,
                    'phase_name' => 'Inbetriebnahme und Parametrierung',
                    'ziel' => 'Anlage starten, prüfen und sinnvoll einstellen',
                    'dauer' => '2–5 Stunden',
                    'abhaengigkeiten' => [2, 4, 5],
                    'parallel_moeglich' => false,
                    'parallel_hinweis' => null,
                    'steps' => [
                        'Inbetriebnahme starten, Funktionsprüfung aller Betriebsarten',
                        'Regelung einstellen (Heizkurve, Warmwasser, Zeitprogramme)',
                        'Kontrolle auf Geräusche, Dichtheit, Durchfluss, Wärmeabgabe',
                        'Abschlussprotokolle/Prüfnachweise erstellen (je nach Umfang)',
                    ],
                ],
                [
                    'order' => 7,
                    'phase_name' => 'Einweisung und Übergabe',
                    'ziel' => 'Kunde kann die Anlage sicher bedienen und versteht die wichtigsten Punkte',
                    'dauer' => '30–60 Minuten',
                    'abhaengigkeiten' => [6],
                    'parallel_moeglich' => false,
                    'parallel_hinweis' => null,
                    'steps' => [
                        'Bedienung erklären (Temperaturen, Programme, Warmwasser, Sparbetrieb)',
                        'Hinweise zum effizienten Betrieb geben',
                        'Störungsleitfaden kurz erklären (was tun, wen anrufen)',
                        'Unterlagen/Protokolle übergeben',
                    ],
                ],
                [
                    'order' => 8,
                    'phase_name' => 'Aufräumen und Abschluss',
                    'ziel' => 'Saubere Übergabe der Arbeitsbereiche',
                    'dauer' => '30–60 Minuten',
                    'abhaengigkeiten' => [7],
                    'parallel_moeglich' => false,
                    'parallel_hinweis' => null,
                    'steps' => [
                        'Verpackungen/Restmaterial entfernen',
                        'Arbeitsbereiche ordentlich hinterlassen',
                        'Kurzer Abschlussrundgang mit dem Kunden',
                    ],
                ],
            ];

            $createdPhaseIds = [];

            foreach ($phases as $phaseData) {
                $phaseId = DB::table('task_phases')->insertGetId([
                    'product_id'   => $productId,
                    'section_id'   => $sectionId,
                    'section_name' => $sectionName,
                    'phase_name'   => $phaseData['phase_name'],
                    'stage'        => $stageName,
                    'stage_id'     => $stageId,
                    'version'      => $version,
                    'status'       => $status,
                    'count'        => count($phaseData['steps']),
                    'order'        => $phaseData['order'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                $createdPhaseIds[$phaseData['order']] = $phaseId;

                $parentActivityId = DB::table('phase_activities')->insertGetId([
                    'phase_id'       => $phaseId,
                    'product_id'     => $productId,
                    'section_id'     => $sectionId,
                    'parent_id'      => null,
                    'copy_from'      => null,
                    'stage_id'       => $stageId,
                    'version'        => $version,
                    'section_name'   => $sectionName,
                    'initial'        => 'M' . $phaseData['order'],
                    'title'          => $phaseData['phase_name'],
                    'duration'       => null,
                    'duration_type'  => $phaseData['dauer'],
                    'description'    => $phaseData['ziel'],
                    'notes'          => $this->buildNotes($phaseData),
                    'status'         => $status,
                    'photo'          => null,
                    'link'           => null,
                    'priority'       => 'Normal',
                    'percent'        => 0,
                    'usage_count'    => 0,
                    'rating'         => 0,
                    'answered_by'    => 2,
                    'sort_order'     => 1,
                    'copy_count'     => 0,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);

                foreach ($phaseData['steps'] as $index => $step) {
                    DB::table('phase_activities')->insert([
                        'phase_id'       => $phaseId,
                        'product_id'     => $productId,
                        'section_id'     => $sectionId,
                        'parent_id'      => $parentActivityId,
                        'copy_from'      => null,
                        'stage_id'       => $stageId,
                        'version'        => $version,
                        'section_name'   => $sectionName,
                        'initial'        => 'M' . $phaseData['order'] . '.' . ($index + 1),
                        'title'          => $step,
                        'duration'       => null,
                        'duration_type'  => null,
                        'description'    => null,
                        'notes'          => null,
                        'status'         => $status,
                        'photo'          => null,
                        'link'           => null,
                        'priority'       => 'Normal',
                        'percent'        => 0,
                        'usage_count'    => 0,
                        'rating'         => 0,
                        'answered_by'    => 2,
                        'sort_order'     => $index + 1,
                        'copy_count'     => 0,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }

            // Optional summary/info phase
            $summaryPhaseId = DB::table('task_phases')->insertGetId([
                'product_id'   => $productId,
                'section_id'   => $sectionId,
                'section_name' => $sectionName,
                'phase_name'   => 'Projektinformationen / Hinweise',
                'stage'        => $stageName,
                'stage_id'     => $stageId,
                'version'      => $version,
                'status'       => $status,
                'count'        => 6,
                'order'        => 99,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $summaryParentId = DB::table('phase_activities')->insertGetId([
                'phase_id'       => $summaryPhaseId,
                'product_id'     => $productId,
                'section_id'     => $sectionId,
                'parent_id'      => null,
                'copy_from'      => null,
                'stage_id'       => $stageId,
                'version'        => $version,
                'section_name'   => $sectionName,
                'initial'        => 'INFO',
                'title'          => 'Allgemeine Projektinformationen',
                'duration'       => null,
                'duration_type'  => null,
                'description'    => 'Zusammenfassung, Checklisten, Sicherheit und optionale Bausteine.',
                'notes'          => 'Gültig für: Luft/Wasser-Wärmepumpe; Sole/Wasser-Wärmepumpe (sofern vereinbart). Hinweis: Der genaue Ablauf kann je nach Objekt, Bestandstechnik und Vereinbarungen leicht abweichen.',
                'status'         => $status,
                'photo'          => null,
                'link'           => null,
                'priority'       => 'Normal',
                'percent'        => 0,
                'usage_count'    => 0,
                'rating'         => 0,
                'answered_by'    => 2,
                'sort_order'     => 1,
                'copy_count'     => 0,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            $infoActivities = [
                'Kundenvorbereitung: Zufahrt und Stellfläche für Montagefahrzeuge freihalten',
                'Kundenvorbereitung: Wege zum Heizraum/Technikraum freimachen (Flur/Treppe/Keller)',
                'Kundenvorbereitung: Heizraum zugänglich machen und im Arbeitsbereich 1–2 m Platz schaffen',
                'Kundensicherheit: Arbeitsbereiche werden markiert/abgesichert und Laufwege geschützt',
                'Gesamtdauer Standard: 2–3 Arbeitstage',
                'Gesamtdauer erweitert: 3–5 Arbeitstage',
            ];

            foreach ($infoActivities as $index => $item) {
                DB::table('phase_activities')->insert([
                    'phase_id'       => $summaryPhaseId,
                    'product_id'     => $productId,
                    'section_id'     => $sectionId,
                    'parent_id'      => $summaryParentId,
                    'copy_from'      => null,
                    'stage_id'       => $stageId,
                    'version'        => $version,
                    'section_name'   => $sectionName,
                    'initial'        => 'INFO.' . ($index + 1),
                    'title'          => $item,
                    'duration'       => null,
                    'duration_type'  => null,
                    'description'    => null,
                    'notes'          => null,
                    'status'         => $status,
                    'photo'          => null,
                    'link'           => null,
                    'priority'       => 'Normal',
                    'percent'        => 0,
                    'usage_count'    => 0,
                    'rating'         => 0,
                    'answered_by'    => 2,
                    'sort_order'     => $index + 1,
                    'copy_count'     => 0,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        });
    }

    private function buildNotes(array $phaseData): string
    {
        $notes = [];

        if (!empty($phaseData['abhaengigkeiten'])) {
            $notes[] = 'Abhängigkeiten: Phase ' . implode(', Phase ', $phaseData['abhaengigkeiten']);
        } else {
            $notes[] = 'Abhängigkeiten: keine';
        }

        $notes[] = 'Parallel möglich: ' . ($phaseData['parallel_moeglich'] ? 'Ja' : 'Nein');

        if (!empty($phaseData['parallel_hinweis'])) {
            $notes[] = 'Parallel-Hinweis: ' . $phaseData['parallel_hinweis'];
        }

        $notes[] = 'Dauer Richtwert: ' . $phaseData['dauer'];

        return implode("\n", $notes);
    }
}