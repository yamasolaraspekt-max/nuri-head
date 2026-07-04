<?php

namespace Database\Seeders;

use App\Models\Accessory;
use App\Models\AccessoryCategory;
use Illuminate\Database\Seeder;

/**
 * Zubehör-Stammdaten (Ventiltechnik) — NUR öffentlich verifizierte Artikelnummern.
 * Quellen + Konfidenz je Zeile: docs/heizkoerper-ventiltechnik-quellen.md (Stand 2026-07).
 *
 * Bewusst NICHT geseedet (ehrliche Lücken, s. Report §D/§E):
 *   - Preise (kommen aus IDS/Sonepar, Stufe iii) — accessories trägt keine Preisspalte.
 *   - kvs_werte = NULL (je Ventil aus Herstellerdatenblatt zu füllen, nicht geschätzt).
 *   - Adapter-SKUs, Hahnblöcke, valve_insert_compatibility = Yama-/SHK-Lücken (0 Zeilen).
 *
 * imported_from-Marker => restlos rückbaubar. Idempotent (updateOrCreate auf hersteller+herst_artikelnr).
 */
class AccessorySeeder extends Seeder
{
    private const MARKER = 'ventiltechnik_recherche_2026-07';

    public function run(): void
    {
        $cats = [
            'thermostatventil'      => ['Thermostatventil-Unterteil / -Gehäuse', 10],
            'thermostatkopf'        => ['Thermostatkopf', 20],
            'ruecklaufverschraubung' => ['Rücklaufverschraubung', 30],
            'einbauventil'          => ['Einbauventil (Ventil-Heizkörper)', 40],
            'ventileinsatz'         => ['Austausch-Ventileinsatz', 50],
        ];
        $catId = [];
        foreach ($cats as $code => [$name, $sort]) {
            $catId[$code] = AccessoryCategory::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'sort_order' => $sort],
            )->id;
        }

        // hersteller, herst_artikelnr, kategorie, name, kopf_norm, dn, voreinstellbar, quelle
        $items = [
            ['IMI Heimeier', '3720-02.000', 'thermostatventil', 'V-exact II Durchgang DN15, AG G¾', 'M30x1_5', 15, true, 'wolf-online-shop.de / waerme24.de (2026)'],
            ['IMI Heimeier', '3711-02.000', 'thermostatventil', 'V-exact II Eckform DN15, vern.', 'M30x1_5', 15, true, 'heizung-billiger.de / raleo.de (2026)'],
            ['Oventrop', '1183804', 'thermostatventil', 'AV 9 Durchgang DN15, R½, PN10', 'M30x1_5', 15, true, 'oventrop.com Herstellerseite (2026)'],
            ['Oventrop', '1183704', 'thermostatventil', 'AV 9 Eck DN15, R½, PN10', 'M30x1_5', 15, true, 'oventrop.com Herstellerseite (2026)'],
            ['Danfoss', '013G0034', 'thermostatventil', 'RA-N Durchgang DN15 (matt vern.)', 'RA', 15, true, 'store.danfoss.com Herstellerstore (2026)'],
            ['Danfoss', '013G0033', 'thermostatventil', 'RA-N Eck DN15', 'RA', 15, true, 'store.danfoss.com Herstellerstore (2026)'],
            ['IMI Heimeier', '6000-00.500', 'thermostatkopf', 'Thermostatkopf K, Flüssigfühler', 'M30x1_5', null, false, 'hornbach.de / heizman24.de (2026)'],
            ['IMI Heimeier', '0356-02.000', 'ruecklaufverschraubung', 'Regutec Rücklaufverschraubung Durchgang DN15', null, 15, false, 'hts24.de / wolf-online-shop.de (2026)'],
            ['IMI Heimeier', '0365-02.000', 'ruecklaufverschraubung', 'Regutec Rücklaufverschraubung Eck DN15', null, 15, false, 'hts24.de (2026)'],
            ['Danfoss', '013G7360', 'einbauventil', 'Einbauventil RA G½ voreinstellbar (Ventil+Kappe)', 'RA', 15, true, 'store.danfoss.com Herstellerstore (2026)'],
            ['Danfoss', '013G0270', 'ventileinsatz', 'RA-N Austausch-Ventileinsatz voreinstellbar', 'RA', 15, true, 'heizungsprofi24.de (2026)'],
        ];

        foreach ($items as [$hersteller, $artnr, $catCode, $name, $kopfNorm, $dn, $voreinstellbar, $quelle]) {
            Accessory::updateOrCreate(
                ['hersteller' => $hersteller, 'herst_artikelnr' => $artnr],
                [
                    'accessory_category_id' => $catId[$catCode],
                    'name' => $name,
                    'kopf_anschluss_norm' => $kopfNorm,
                    'dn' => $dn,
                    'kvs_werte' => null,          // Datenblatt-Aufgabe (Report §B/§E)
                    'einrohr_tauglich' => false,  // Einrohr-Armaturen noch nicht recherchiert
                    'voreinstellbar' => $voreinstellbar,
                    'quelle' => $quelle,
                    'imported_from' => self::MARKER,
                    'aktiv' => true,
                ],
            );
        }
    }
}
