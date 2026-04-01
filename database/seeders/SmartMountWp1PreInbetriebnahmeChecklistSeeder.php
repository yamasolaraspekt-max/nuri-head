<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SmartMountWp1PreInbetriebnahmeChecklistSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ✅ IMPORTANT:
            // Your UI expects item.options to be an ARRAY OF STRINGS (not objects).
            // So we store options like ["integriert","separat","nicht vorhanden"]
            // NOT like [{"value":"x","label":"y"}]

            $baseTitle = 'Vor-Inbetriebnahme Checkliste Wärmepumpe (SmartMount WP1)';

            /**
             * Create or update a checklist by slug, then hard-reset items.
             */
            $upsertChecklist = function (
                string $code,              // "0", "A", "B", ...
                string $title,
                string $description,
                string $type = 'acceptance',
                string $status = 'active'
            ) {
                $slug = Str::slug($title);

                $existingId = DB::table('maintenance_checklists')->where('slug', $slug)->value('id');

                $data = [
                    'title'       => $title,
                    'slug'        => $slug,
                    'description' => $description,
                    'logo_path'   => null,
                    'type'        => $type,
                    'status'      => $status,
                    'is_global'   => true,
                    'created_by'  => null,
                    'updated_by'  => null,
                    'updated_at'  => now(),
                ];

                if (!$existingId) {
                    $data['created_at'] = now();
                    $checklistId = DB::table('maintenance_checklists')->insertGetId($data);
                } else {
                    DB::table('maintenance_checklists')->where('id', $existingId)->update($data);
                    $checklistId = $existingId;

                    DB::table('maintenance_checklist_items')
                        ->where('maintenance_checklist_id', $checklistId)
                        ->delete();
                }

                return $checklistId;
            };

            /**
             * Item builder (per-checklist sort order)
             */
            $makeItemBuilder = function (int $checklistId) {
                $order = 10;

                return function (
                    string $label,
                    string $fieldName,
                    string $fieldType,
                    ?array $options = null,
                    bool $required = false,
                    ?string $help = null,
                    ?string $placeholder = null,
                    ?string $fileAccept = null
                ) use (&$order, $checklistId) {
                    $row = [
                        'maintenance_checklist_id' => $checklistId,
                        'label'       => $label,
                        'field_name'  => $fieldName,
                        'field_type'  => $fieldType,
                        'options'     => $options ? json_encode(array_values($options), JSON_UNESCAPED_UNICODE) : null,
                        'is_required' => $required,
                        'help_text'   => $help,
                        'placeholder' => $placeholder,
                        'file_accept' => $fileAccept,
                        'sort_order'  => $order,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                    $order += 10;
                    return $row;
                };
            };

            /**
             * Insert items
             */
            $insertItems = function (array $items) {
                if (!empty($items)) {
                    DB::table('maintenance_checklist_items')->insert($items);
                }
            };

            // ==========================================================
            // 0) Anlagen-Konstellation (separate checklist)
            // ==========================================================
            $id0 = $upsertChecklist(
                '0',
                $baseTitle . ' — 0) Anlagen-Konstellation',
                'Anlagen-Konstellation (Aufbau, Speicher, Heizkreise) inkl. Ausfüller-Daten.'
            );
            $item0 = $makeItemBuilder($id0);

            $items0 = [];

            // Header fields
            $items0[] = $item0('Ausgefüllt von (Name)', 'cfg_filled_by_name', 'text', null, true, null, 'Name');
            $items0[] = $item0('Datum/Uhrzeit', 'cfg_filled_at', 'text', null, false, null, 'TT.MM.JJJJ HH:MM');
            $items0[] = $item0('Unterschrift (Foto)', 'cfg_signature', 'file_image', null, false, null, null, 'image/*');

            // 0.1 Aufbau - Kompakteinheit
            $items0[] = $item0('0.1 Aufbau — Variante', 'cfg_build_variant', 'radio', [
                'Kompakteinheit',
                'Einzelkomponenten (Hydraulikmodul + Speicher getrennt)',
            ], true);

            // Kompakteinheit: Trinkwasserspeicher
            $items0[] = $item0('0.1 Kompakteinheit — Trinkwasserspeicher', 'cfg_compact_tw_storage', 'radio', [
                'integriert',
                'separat',
                'nicht vorhanden',
            ], false, 'Nur relevant, wenn „Kompakteinheit“ gewählt wurde.');

            // Kompakteinheit: Pufferspeicher
            $items0[] = $item0('0.1 Kompakteinheit — Pufferspeicher', 'cfg_compact_buffer', 'radio', [
                'nicht vorhanden',
                'vorhanden',
            ], false, 'Wenn „vorhanden“: Volumen in Liter eintragen.');
            $items0[] = $item0('0.1 Kompakteinheit — Pufferspeicher Volumen (L)', 'cfg_compact_buffer_liters', 'text', null, false, null, 'z.B. 100');

            // Einzelkomponenten: Trinkwasserspeicher
            $items0[] = $item0('0.1 Einzelkomponenten — Trinkwasserspeicher', 'cfg_split_tw_storage', 'radio', [
                'vorhanden',
                'nicht vorhanden',
            ], false, 'Nur relevant, wenn „Einzelkomponenten“ gewählt wurde.');
            $items0[] = $item0('0.1 Einzelkomponenten — Trinkwasserspeicher Volumen (L)', 'cfg_split_tw_liters', 'text', null, false, null, 'z.B. 200');

            // Einzelkomponenten: Pufferspeicher
            $items0[] = $item0('0.1 Einzelkomponenten — Pufferspeicher', 'cfg_split_buffer', 'radio', [
                'vorhanden',
                'nicht vorhanden',
            ], false);
            $items0[] = $item0('0.1 Einzelkomponenten — Pufferspeicher Volumen (L)', 'cfg_split_buffer_liters', 'text', null, false, null, 'z.B. 100');

            // 0.2 Heizkreise
            $items0[] = $item0('0.2 Heizkreise — Anzahl', 'cfg_hk_count', 'radio', [
                '1 Heizkreis',
                '2 Heizkreise',
            ], true);

            $items0[] = $item0('0.2 Heizkreis 1 — Art', 'cfg_hk1_type', 'radio', [
                'Heizkörper',
                'Fußbodenheizung',
                'gemischt',
            ], true);

            $items0[] = $item0('0.2 Heizkreis 2 — Art', 'cfg_hk2_type', 'radio', [
                'Heizkörper',
                'Fußbodenheizung',
                'gemischt',
            ], false, 'Nur ausfüllen, wenn „2 Heizkreise“ gewählt wurde.');

            $insertItems($items0);

            // ==========================================================
            // A) Ladeleitung (separate checklist)
            // ==========================================================
            $idA = $upsertChecklist(
                'A',
                $baseTitle . ' — A) Ladeleitung (Außen ↔ Innen)',
                'Ladeleitung – Vorlauf & Rücklauf: Sicht-/Montagekontrolle, Dichtheit, Druckprüfung.'
            );
            $itemA = $makeItemBuilder($idA);

            $itemsA = [];

            // Responsible header
            $itemsA[] = $itemA('Verantwortlich (Name)', 'a_responsible_name', 'text', null, true, null, 'Name');
            $itemsA[] = $itemA('Datum/Uhrzeit', 'a_checked_at', 'text', null, false, null, 'TT.MM.JJJJ HH:MM');
            $itemsA[] = $itemA('Unterschrift (Foto)', 'a_signature', 'file_image', null, false, null, null, 'image/*');
            $itemsA[] = $itemA('Teilabschnitte getrennt geprüft? (A1/A2/A3 Namen)', 'a_subsections_names', 'text', null, false, 'Optional: A1 Name / A2 Name / A3 Name', 'A1 … / A2 … / A3 …');

            // A1
            $itemsA[] = $itemA('A1 Sicht- & Montagekontrolle — Geprüft von (Name)', 'a1_checked_by', 'text', null, false, null, 'Name');
            $itemsA[] = $itemA('A1 Sicht- & Montagekontrolle — Unterschrift (Foto)', 'a1_signature', 'file_image', null, false, null, null, 'image/*');

            $itemsA[] = $itemA('A1 Leitungsführung spannungsfrei, keine Knicke/Quetschungen, keine Scheuerstellen', 'a1_route_ok', 'checkbox');
            $itemsA[] = $itemA('A1 Befestigungen/Schellen korrekt, Abstände passend, keine Schallbrücken (wenn relevant)', 'a1_clamps_ok', 'checkbox');
            $itemsA[] = $itemA('A1 Wand-/Deckendurchführungen sauber, Schutz gegen Kanten (Kantenschutz/Schutzrohr)', 'a1_penetrations_ok', 'checkbox');
            $itemsA[] = $itemA('A1 Abdichtung innen/außen vollständig (Dichteinsatz/Manschetten), keine offenen Fugen', 'a1_sealing_ok', 'checkbox');
            $itemsA[] = $itemA('A1 Dämmung innen vollständig, Stöße geschlossen', 'a1_insulation_inside_ok', 'checkbox');
            $itemsA[] = $itemA('A1 Dämmung außen UV-/witterungsbeständig, Stöße/Enden dicht', 'a1_insulation_outside_ok', 'checkbox');
            $itemsA[] = $itemA('A1 Vorlauf/Rücklauf eindeutig gekennzeichnet', 'a1_flow_labeled', 'checkbox');

            // A2
            $itemsA[] = $itemA('A2 Press- und Gewindeverbindungen — Geprüft von (Name)', 'a2_checked_by', 'text', null, false, null, 'Name');
            $itemsA[] = $itemA('A2 Press- und Gewindeverbindungen — Unterschrift (Foto)', 'a2_signature', 'file_image', null, false, null, null, 'image/*');

            $itemsA[] = $itemA('A2 Pressstellen vollständig (gezählt/abgehakt) — Anzahl Pressungen gesamt', 'a2_press_count', 'text', null, false, null, 'z.B. 24');
            $itemsA[] = $itemA('A2 Presskontur korrekt, Pressmarken sichtbar, keine „vergessenen“ Pressungen', 'a2_press_profile_ok', 'checkbox');
            $itemsA[] = $itemA('A2 Übergänge (Press ↔ Gewinde / Press ↔ Bestand) korrekt montiert, spannungsfrei', 'a2_transitions_ok', 'checkbox');
            $itemsA[] = $itemA('A2 Gewindedichtmittel fachgerecht, keine Haarrisse/Versatz, keine Überbelastung', 'a2_threadseal_ok', 'checkbox');
            $itemsA[] = $itemA('A2 Sicht-/Wischprüfung: alle Verbindungen trocken (auch verdeckte Stellen)', 'a2_wipe_test_ok', 'checkbox');

            // A3
            $itemsA[] = $itemA('A3 Druck-/Dichtheitsprüfung — Geprüft von (Name)', 'a3_checked_by', 'text', null, false, null, 'Name');
            $itemsA[] = $itemA('A3 Druck-/Dichtheitsprüfung — Unterschrift (Foto)', 'a3_signature', 'file_image', null, false, null, null, 'image/*');

            $itemsA[] = $itemA('A3 Prüfdruck gemäß Vorgabe (bar)', 'a3_test_pressure_bar', 'text', null, false, null, 'z.B. 3,0');
            $itemsA[] = $itemA('A3 Beginn (Uhrzeit)', 'a3_start_time', 'text', null, false, null, 'HH:MM');
            $itemsA[] = $itemA('A3 Ende (Uhrzeit)', 'a3_end_time', 'text', null, false, null, 'HH:MM');
            $itemsA[] = $itemA('A3 Dauer (min)', 'a3_duration_min', 'text', null, false, null, 'z.B. 30');

            $itemsA[] = $itemA('A3 Ergebnis', 'a3_result', 'radio', [
                'in Ordnung',
                'nicht in Ordnung',
            ], false);

            $itemsA[] = $itemA('A3 Auffälligkeiten/Mängel notiert (siehe Sammelfeld am Ende)', 'a3_issues_noted', 'checkbox');

            $insertItems($itemsA);

            // ==========================================================
            // B) Heizungsseite (separate checklist)
            // ==========================================================
            $idB = $upsertChecklist(
                'B',
                $baseTitle . ' — B) Heizungsseite',
                'Heizungsseite: Armaturen, Abscheider, Speicher/MAG, Heizkreise, Spülen/Befüllen, Druckprüfung.'
            );
            $itemB = $makeItemBuilder($idB);

            $itemsB = [];
            $itemsB[] = $itemB('Verantwortlich (Name)', 'b_responsible_name', 'text', null, true, null, 'Name');
            $itemsB[] = $itemB('Datum/Uhrzeit', 'b_checked_at', 'text', null, false, null, 'TT.MM.JJJJ HH:MM');
            $itemsB[] = $itemB('Unterschrift (Foto)', 'b_signature', 'file_image', null, false, null, null, 'image/*');

            $itemsB[] = $itemB('B1 Absperrungen VL/RL vorhanden, zugänglich, beschriftet', 'b1_isolations_ok', 'checkbox');
            $itemsB[] = $itemB('B1 KFE/Füll- und Entleerstellen vorhanden, zugänglich, Kappen dicht', 'b1_kfe_ok', 'checkbox');
            $itemsB[] = $itemB('B1 Flussrichtungen an Armaturen/Abscheidern beachtet (Pfeile)', 'b1_flow_direction_ok', 'checkbox');
            $itemsB[] = $itemB('B1 Übergänge auf Bestand korrekt, spannungsfrei', 'b1_existing_transitions_ok', 'checkbox');
            $itemsB[] = $itemB('B1 Dämmung im Heizraum vollständig, keine offenen Heizungsleitungen', 'b1_insulation_room_ok', 'checkbox');

            $itemsB[] = $itemB('B2 Schlamm-/Magnetitabscheider korrekt, zugänglich', 'b2_dirt_separator_ok', 'checkbox');
            $itemsB[] = $itemB('B2 Mikroblasenabscheider korrekt (falls vorgesehen)', 'b2_microbubble_ok', 'checkbox');
            $itemsB[] = $itemB('B2 Automatische Entlüfter an Hochpunkten (falls vorgesehen)', 'b2_auto_vents_ok', 'checkbox');
            $itemsB[] = $itemB('B2 Entlüfterkappen korrekt/betriebsbereit, trocken', 'b2_vent_caps_ok', 'checkbox');

            $itemsB[] = $itemB('B3 Pufferspeicher angeschlossen (falls vorhanden), VL/RL korrekt', 'b3_buffer_connected', 'checkbox');
            $itemsB[] = $itemB('B3 Pufferspeicher entlüftbar, Absperrungen vorhanden', 'b3_buffer_service_ok', 'checkbox');
            $itemsB[] = $itemB('B3 Pufferspeicher Fühler/Fühlerhülsen montiert (falls vorgesehen)', 'b3_buffer_sensors_ok', 'checkbox');

            $itemsB[] = $itemB('B3 Trinkwasserspeicher Anschlüsse korrekt (KW rein/WW raus/Zirk)', 'b3_tw_connections_ok', 'checkbox');
            $itemsB[] = $itemB('B3 Trinkwasserspeicher Absperrungen vorhanden und zugänglich', 'b3_tw_isolations_ok', 'checkbox');
            $itemsB[] = $itemB('B3 Trinkwasserspeicher Dämmung WW/Zirk vollständig (falls vorhanden)', 'b3_tw_insulation_ok', 'checkbox');

            $itemsB[] = $itemB('B3 MAG montiert, Halterung stabil', 'b3_mag_mounted', 'checkbox');
            $itemsB[] = $itemB('B3 MAG Kappenventil vorhanden', 'b3_mag_cap_valve', 'checkbox');
            $itemsB[] = $itemB('B3 MAG Vordruck (bar)', 'b3_mag_precharge_bar', 'text', null, false, null, 'z.B. 1,0');
            $itemsB[] = $itemB('B3 Anlagendruck kalt (bar)', 'b3_system_pressure_cold_bar', 'text', null, false, null, 'z.B. 1,5');
            $itemsB[] = $itemB('B3 Sicherheitsventil korrekt angeschlossen (Ablauf geführt), trocken', 'b3_safety_valve_ok', 'checkbox');
            $itemsB[] = $itemB('B3 Manometer vorhanden/funktioniert', 'b3_manometer_ok', 'checkbox');

            $itemsB[] = $itemB('B4 Heizkreis 1 Anschluss korrekt (VL/RL nicht vertauscht), Absperrungen vorhanden', 'b4_hk1_connection_ok', 'checkbox');
            $itemsB[] = $itemB('B4 Heizkreis 1 Pumpe/Mischer korrekt, Stellmotor frei (falls vorhanden)', 'b4_hk1_pump_mixer_ok', 'checkbox');
            $itemsB[] = $itemB('B4 Heizkreis 1 Verteiler/Armaturen dicht, Entlüfter/KFE funktionsfähig (falls vorhanden)', 'b4_hk1_manifold_ok', 'checkbox');

            $itemsB[] = $itemB('B4 Heizkreis 2 Anschluss korrekt (nur wenn vorhanden)', 'b4_hk2_connection_ok', 'checkbox');
            $itemsB[] = $itemB('B4 Heizkreis 2 Pumpe/Mischer korrekt (nur wenn vorhanden)', 'b4_hk2_pump_mixer_ok', 'checkbox');
            $itemsB[] = $itemB('B4 Heizkreis 2 Verteiler/Armaturen dicht (nur wenn vorhanden)', 'b4_hk2_manifold_ok', 'checkbox');

            $itemsB[] = $itemB('B4 Anlage gespült (falls vorgesehen), Abscheider/Filter kontrolliert', 'b4_flushed_checked', 'checkbox');
            $itemsB[] = $itemB('B4 Anlage befüllt und vollständig entlüftet', 'b4_filled_vented', 'checkbox');

            $itemsB[] = $itemB('B4 Druckprüfung Heizungsnetz — Prüfdruck (bar)', 'b4_test_pressure_bar', 'text', null, false, null, 'z.B. 3,0');
            $itemsB[] = $itemB('B4 Druckprüfung Heizungsnetz — Dauer (min)', 'b4_test_duration_min', 'text', null, false, null, 'z.B. 30');
            $itemsB[] = $itemB('B4 Druckprüfung Heizungsnetz — Ergebnis', 'b4_test_result', 'radio', [
                'i. O.',
                'n. i. O.',
            ]);

            $insertItems($itemsB);

            // ==========================================================
            // C) Trinkwasserseite (separate checklist)
            // ==========================================================
            $idC = $upsertChecklist(
                'C',
                $baseTitle . ' — C) Trinkwasserseite',
                'Trinkwasserseite: Sichtprüfung, Dichtheit, Zirkulation.'
            );
            $itemC = $makeItemBuilder($idC);

            $itemsC = [];
            $itemsC[] = $itemC('Verantwortlich (Name)', 'c_responsible_name', 'text', null, true, null, 'Name');
            $itemsC[] = $itemC('Datum/Uhrzeit', 'c_checked_at', 'text', null, false, null, 'TT.MM.JJJJ HH:MM');
            $itemsC[] = $itemC('Unterschrift (Foto)', 'c_signature', 'file_image', null, false, null, null, 'image/*');

            $itemsC[] = $itemC('C1 Leitungsführung spannungsfrei, Befestigung korrekt, keine Scheuerstellen', 'c1_route_ok', 'checkbox');
            $itemsC[] = $itemC('C1 Übergänge/Pressungen/Gewinde fachgerecht, spannungsfrei', 'c1_transitions_ok', 'checkbox');
            $itemsC[] = $itemC('C1 Dämmung WW/Zirk vollständig (falls vorhanden)', 'c1_insulation_ok', 'checkbox');
            $itemsC[] = $itemC('C1 Absperrungen vorhanden, zugänglich, beschriftet', 'c1_isolations_ok', 'checkbox');

            $itemsC[] = $itemC('C2 Anzahl Pressungen gesamt', 'c2_press_count', 'text', null, false, null, 'z.B. 14');
            $itemsC[] = $itemC('C2 Sicht-/Wischprüfung trocken', 'c2_wipe_test_ok', 'checkbox');
            $itemsC[] = $itemC('C2 Dichtheitsprüfung — Prüfdruck (bar)', 'c2_test_pressure_bar', 'text', null, false, null, 'z.B. 10');
            $itemsC[] = $itemC('C2 Dichtheitsprüfung — Dauer (min)', 'c2_test_duration_min', 'text', null, false, null, 'z.B. 10');
            $itemsC[] = $itemC('C2 Dichtheitsprüfung — Ergebnis', 'c2_test_result', 'radio', [
                'i. O.',
                'n. i. O.',
            ]);

            $itemsC[] = $itemC('C3 Zirkulationspumpe montiert, Flussrichtung korrekt, Absperrungen vorhanden', 'c3_circ_pump_ok', 'checkbox');
            $itemsC[] = $itemC('C3 Rückflussverhinderer/Schwerkraftbremse korrekt (falls vorgesehen)', 'c3_check_valve_ok', 'checkbox');
            $itemsC[] = $itemC('C3 Entlüftet, leiser Lauf möglich, keine Undichtigkeiten', 'c3_vented_no_leaks', 'checkbox');

            $insertItems($itemsC);

            // ==========================================================
            // D) Elektrischer Anschluss (separate checklist)
            // ==========================================================
            $idD = $upsertChecklist(
                'D',
                $baseTitle . ' — D) Elektrischer Anschluss (Elektrofachkraft)',
                'Elektrischer Anschluss: Sicherheit, Verdrahtung, Messwerte/Prüfungen.'
            );
            $itemD = $makeItemBuilder($idD);

            $itemsD = [];
            $itemsD[] = $itemD('Elektrofachkraft (Name)', 'd_electrician_name', 'text', null, true, null, 'Name');
            $itemsD[] = $itemD('Datum/Uhrzeit', 'd_checked_at', 'text', null, false, null, 'TT.MM.JJJJ HH:MM');
            $itemsD[] = $itemD('Unterschrift (Foto)', 'd_signature', 'file_image', null, false, null, null, 'image/*');

            $itemsD[] = $itemD('D1 Zuleitung korrekt dimensioniert, Klemmen fest, Zugentlastung vorhanden', 'd1_supply_ok', 'checkbox');
            $itemsD[] = $itemD('D1 Schutzleiter angeschlossen, Potentialausgleich geprüft (falls erforderlich)', 'd1_pe_ok', 'checkbox');
            $itemsD[] = $itemD('D1 Kabeleinführungen dicht, mechanisch geschützt', 'd1_cable_entries_ok', 'checkbox');
            $itemsD[] = $itemD('D1 Absicherung/Schutzschalter korrekt, Beschriftung vorhanden', 'd1_fuses_labeled', 'checkbox');
            $itemsD[] = $itemD('D1 FI/RCD Test', 'd1_rcd_test', 'radio', [
                'i. O.',
                'n. i. O.',
            ]);

            $itemsD[] = $itemD('D2 Fühler korrekt angeschlossen (Außen/VL/RL/Speicher)', 'd2_sensors_ok', 'checkbox');
            $itemsD[] = $itemD('D2 Kommunikation/Steuerleitungen korrekt (falls vorhanden)', 'd2_comms_ok', 'checkbox');
            $itemsD[] = $itemD('D2 Freigabe-/Sperreingänge korrekt (falls vorhanden)', 'd2_enable_inputs_ok', 'checkbox');
            $itemsD[] = $itemD('D2 Keine losen Adern, keine Quetschstellen, saubere Kabelführung', 'd2_cable_management_ok', 'checkbox');

            $itemsD[] = $itemD('D3 Netzspannung (V)', 'd3_mains_voltage_v', 'text', null, false, null, 'z.B. 230');
            $itemsD[] = $itemD('D3 Schutzleiterprüfung dokumentiert', 'd3_pe_test_doc', 'radio', [
                'ja',
                'nein',
            ]);
            $itemsD[] = $itemD('D3 Schutzorgane geprüft (Auslösung/Prüftaste)', 'd3_protections_tested', 'radio', [
                'ja',
                'nein',
            ]);

            $insertItems($itemsD);

            // ==========================================================
            // E) Mängel / Nacharbeit (separate checklist)
            // ==========================================================
            $idE = $upsertChecklist(
                'E',
                $baseTitle . ' — E) Mängel / Nacharbeit',
                'Sammelfeld für Auffälligkeiten, Mängel, Nacharbeit inkl. Erfassung.'
            );
            $itemE = $makeItemBuilder($idE);

            $itemsE = [];
            $itemsE[] = $itemE('Mängel / Nacharbeit / Bemerkungen', 'e_issues_notes', 'textarea', null, false, 'Bereich + Beschreibung + Maßnahme + Status.');
            $itemsE[] = $itemE('Erfasst von (Name)', 'e_recorded_by', 'text', null, false, null, 'Name');
            $itemsE[] = $itemE('Datum/Uhrzeit', 'e_recorded_at', 'text', null, false, null, 'TT.MM.JJJJ HH:MM');

            $insertItems($itemsE);

            // ==========================================================
            // F) Freigabe zur Inbetriebnahme (separate checklist)
            // ==========================================================
            $idF = $upsertChecklist(
                'F',
                $baseTitle . ' — F) Freigabe zur Inbetriebnahme',
                'Freigabe-Checks (A–D) sowie Unterschriften Montage/Elektro.'
            );
            $itemF = $makeItemBuilder($idF);

            $itemsF = [];
            $itemsF[] = $itemF('A Ladeleitung vollständig i. O.', 'f_release_a_ok', 'checkbox');
            $itemsF[] = $itemF('B Heizungsseite vollständig i. O.', 'f_release_b_ok', 'checkbox');
            $itemsF[] = $itemF('C Trinkwasser vollständig i. O. (falls vorhanden)', 'f_release_c_ok', 'checkbox');
            $itemsF[] = $itemF('D Elektro vollständig i. O. (Elektrofachkraft)', 'f_release_d_ok', 'checkbox');

            $itemsF[] = $itemF('Montage-Freigabe — Name', 'f_montage_release_name', 'text', null, false, null, 'Name');
            $itemsF[] = $itemF('Montage-Freigabe — Unterschrift (Foto)', 'f_montage_release_signature', 'file_image', null, false, null, null, 'image/*');

            $itemsF[] = $itemF('Elektro-Freigabe — Name', 'f_electro_release_name', 'text', null, false, null, 'Name');
            $itemsF[] = $itemF('Elektro-Freigabe — Unterschrift (Foto)', 'f_electro_release_signature', 'file_image', null, false, null, null, 'image/*');

            $insertItems($itemsF);
        });
    }
}
