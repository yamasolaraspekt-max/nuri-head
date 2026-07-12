<?php

namespace Database\Seeders;

use App\Models\ProductFormula;
use App\Services\Form\FormSchemaValidator;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Pilot (Bereich 2): Übernahme des playground-WP-Bedarfsformulars `produkt_waermepumpe`
 * als EINE v2-`product_formulas`-Zeile in die ticket-Formular-Engine.
 *
 * Reduziertes Ziel (Yama 2026-07-12, Option A): „WP-Formularinhalt aus playground lebt als
 * v2 product_formula im ticket-System und validiert sauber." NICHT Render/Erfassung (F2, s.
 * docs/bereich2-folgeposten.md).
 *
 * Regeln:
 * - ticket-Engine (product_formulas + FormSchemaValidator) ist führend; playground liefert nur
 *   die Feldinhalte/Erhebungsstruktur (Feld-Slugs/Typen/Optionen), NICHT sein dynamic_forms-System.
 * - Anker: product_id = 2 (= Gewerk „Wärmepumpe"). Belegt: product_formulas.product_id ist FK auf
 *   article_groups.id (Migration `2025_06_02_083017…:28`); dass id=2 das Gewerk „Wärmepumpe" ist,
 *   stammt aus der Live-`article_groups`-Abfrage der Bereich-2-Inventur. VOR dem echten Prod-Seed ist
 *   per DB-Check zu bestätigen (`SELECT id, article_group FROM article_groups WHERE id=2;`) — Gate A1.
 * - Import-Meta ohne Migration: `imported_from = 'playground:produkt_waermepumpe'` (Composite in der
 *   bestehenden Spalte trägt Quelle + Form-Key; playground_form_key existiert NICHT als eigene Spalte).
 * - schema_version = 2, status = Published.
 * - `key = name = slug` je Feld (v2-Validator nutzt `key`; `name` additiv als Brücke zu späterem
 *   v1-Renderer — F2). Einheiten normalisiert (m², kWh, l, °C).
 * - Vor dem Persistieren wird gegen FormSchemaValidator (v2) geprüft; bei Fehlern KEIN Schreiben
 *   (Operanden-Gate: kein ungeprüfter Import).
 *
 * Rückbau: die eine Zeile ist über `imported_from` LIKE 'playground%' eindeutig identifizier-/löschbar
 * (eigener Posten, kein git rm). Rein additiv — kein Bestandseingriff, keine Migration.
 */
class WpProduktFormularSeeder extends Seeder
{
    /** Gewerk „Wärmepumpe" (article_groups.id). */
    public const WP_ARTICLE_GROUP_ID = 2;

    public const IMPORTED_FROM = 'playground:produkt_waermepumpe';

    public const SECTION_NAME = 'WP-Bedarf';

    public function run(): void
    {
        $this->importieren(self::fields());
    }

    /**
     * Import-Härtung + Persistenz. Validiert die Felder gegen das v2-Schema und schreibt NUR bei
     * leerer Fehlerliste (Operanden-Gate: kein ungeprüfter Import). Idempotent via updateOrCreate.
     *
     * @param  array<int,array<string,mixed>>  $fields
     */
    public function importieren(array $fields): void
    {
        $fehler = (new FormSchemaValidator())->validate($fields);
        if ($fehler !== []) {
            throw new RuntimeException(
                'WP-Formular verletzt v2-Schema, Import abgebrochen: '.implode(' | ', $fehler)
            );
        }

        ProductFormula::updateOrCreate(
            [
                'product_id' => self::WP_ARTICLE_GROUP_ID,
                'section_name' => self::SECTION_NAME,
                'imported_from' => self::IMPORTED_FROM,
            ],
            [
                'fields' => $fields,
                'schema_version' => 2,
                'status' => 'Published',
                'version' => 1,
            ]
        );
    }

    /**
     * Die 18 Felder aus playground `produkt_waermepumpe`, gemappt auf das ticket-v2-Schema.
     * `key = name = slug`; Optionen als {value,label}; source=import.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fields(): array
    {
        return [
            self::multiselect('wp_ziel', 'Ziel des Kunden', false, [
                'heizung_ersetzen' => 'Heizung ersetzen',
                'gas_oel_raus' => 'Gas oder Öl raus',
                'heizkosten_senken' => 'Heizkosten senken',
                'foerderung_nutzen' => 'Förderung nutzen',
                'mit_pv_koppeln' => 'mit Photovoltaik koppeln',
            ]),
            self::select('wp_vorhaben', 'Art des Vorhabens', true, [
                'neue_wp' => 'Neue Wärmepumpe',
                'hybrid' => 'Hybridlösung',
                'austausch_wp' => 'Austausch bestehende WP',
                'ergaenzung' => 'Ergänzung',
            ]),
            self::select('wp_gebaeudeart', 'Gebäudeart', true, [
                'efh' => 'Einfamilienhaus',
                'mfh' => 'Mehrfamilienhaus',
                'reihenhaus' => 'Reihenhaus',
                'doppelhaus' => 'Doppelhaus',
                'gewerbe' => 'Gewerbe',
            ]),
            self::zahl('wp_baujahr', 'Baujahr', 'integer', 'Jahr', false, true, ['min' => 1800, 'max' => 2100]),
            self::zahl('wp_wohnflaeche', 'Wohnfläche', 'area', 'm²', 2, true, ['min' => 0]),
            self::select('wp_daemmzustand', 'Dämmzustand', true, [
                'unsaniert' => 'unsaniert',
                'teilsaniert' => 'teilsaniert',
                'saniert' => 'saniert',
            ]),
            self::select('wp_energietraeger', 'Bisheriger Energieträger', true, [
                'gas' => 'Gas',
                'oel' => 'Öl',
                'fluessiggas' => 'Flüssiggas',
                'strom' => 'Strom',
                'holz' => 'Holz',
                'pellet' => 'Pellet',
                'fernwaerme' => 'Fernwärme',
            ]),
            self::zahl('wp_gasverbrauch', 'Jahresverbrauch Gas', 'number', 'kWh', 0, false, ['min' => 0]),
            self::zahl('wp_oelverbrauch', 'Jahresverbrauch Öl', 'number', 'l', 0, false, ['min' => 0]),
            self::select('wp_warmwasser_enthalten', 'Warmwasser im Verbrauch enthalten', true, [
                'ja' => 'Ja',
                'nein' => 'Nein',
                'unbekannt' => 'unbekannt',
            ]),
            self::select('wp_heizung_art', 'Art der Heizung', true, [
                'gastherme' => 'Gastherme',
                'oelkessel' => 'Ölkessel',
                'brennwert' => 'Brennwert',
                'holz' => 'Holz',
                'pellet' => 'Pellet',
                'wp' => 'Wärmepumpe',
            ]),
            self::zahl('wp_heizung_baujahr', 'Baujahr Heizung', 'integer', 'Jahr', false, false, ['min' => 1800, 'max' => 2100]),
            self::select('wp_warmwasserspeicher', 'Warmwasserspeicher vorhanden', false, [
                'ja' => 'Ja',
                'nein' => 'Nein',
            ]),
            self::select('wp_heizflaechen', 'Heizflächen', true, [
                'heizkoerper' => 'Heizkörper',
                'fussbodenheizung' => 'Fußbodenheizung',
                'gemischt' => 'gemischt',
            ]),
            self::zahl('wp_vorlauftemperatur', 'Vorlauftemperatur (bekannt)', 'number', '°C', 0, false, ['min' => 0, 'max' => 90]),
            self::select('wp_aufstellung', 'Gewünschte Aufstellung', true, [
                'aussen' => 'Außen',
                'innen' => 'innen',
                'split' => 'Splitgerät',
                'monoblock' => 'Monoblock',
                'zu_pruefen' => 'zu prüfen',
            ]),
            self::select('wp_zaehlerschrankzustand', 'Zählerschrankzustand', true, [
                'neu' => 'neu',
                'alt' => 'alt',
                'zu_pruefen' => 'zu prüfen',
            ]),
            self::select('wp_bilder_heizraum', 'Bilder Heizraum vorhanden', true, [
                'ja' => 'Ja',
                'nein' => 'Nein',
                'fehlt' => 'fehlt',
            ]),
        ];
    }

    /** @param array<string,string> $options value=>label */
    private static function select(string $slug, string $label, bool $required, array $options): array
    {
        return self::basis($slug, $label, 'select', $required) + ['options' => self::opts($options)];
    }

    /** @param array<string,string> $options value=>label */
    private static function multiselect(string $slug, string $label, bool $required, array $options): array
    {
        return self::basis($slug, $label, 'multiselect', $required) + ['options' => self::opts($options)];
    }

    /**
     * @param  int|false  $decimals
     * @param  array<string,int>  $grenzen
     */
    private static function zahl(string $slug, string $label, string $type, string $unit, $decimals, bool $required, array $grenzen = []): array
    {
        $feld = self::basis($slug, $label, $type, $required) + ['unit' => $unit];
        if ($decimals !== false) {
            $feld['decimals'] = $decimals;
        }

        return $feld + $grenzen;
    }

    private static function basis(string $slug, string $label, string $type, bool $required): array
    {
        return [
            'key' => $slug,
            'name' => $slug, // v1-Brücke (F2) — identisch zum key
            'label' => $label,
            'type' => $type,
            'required' => $required,
            'source' => 'import',
        ];
    }

    /**
     * @param  array<string,string>  $options
     * @return array<int,array<string,string>>
     */
    private static function opts(array $options): array
    {
        $out = [];
        foreach ($options as $value => $label) {
            $out[] = ['value' => $value, 'label' => $label];
        }

        return $out;
    }
}
