<?php

namespace Tests\Feature\Heizkoerper;

use App\Models\Accessory;
use App\Models\AccessoryCategory;
use App\Models\ValveInsertCompatibility;
use App\Services\Heizkoerper\CompatibilityService;
use App\Services\Heizkoerper\HydraulicService;
use App\Services\Heizkoerper\RadiatorSituation;
use Database\Seeders\AccessorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * (M3 iv-b) CompatibilityService — Unit je D3-Regel (Bauplan §5) + Datenlage-Stufen + Naht zum HydraulicService.
 * Gegen ticket_testing; seedet die 11 belegten accessories (AccessorySeeder).
 */
class CompatibilityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AccessorySeeder::class); // 5 Kategorien + 11 Zubehör-Artikel
    }

    private function service(): CompatibilityService
    {
        return new CompatibilityService;
    }

    /** §5.1 Ventil-Heizkörper → nur Einsatz + Kopf, KEIN Thermostatventil. */
    public function test_rule_1_ventil_hk_liefert_einsatz_und_kopf(): void
    {
        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(istVentilHeizkoerper: true, kopfNormBestand: 'M30x1_5'));

        $this->assertContains('ventileinsatz', $r->kategorien());
        $this->assertContains('thermostatkopf', $r->kategorien());
        $this->assertNotContains('thermostatventil', $r->kategorien());
    }

    /** §5.2 Kompakt-HK → Ventil + Kopf + Rücklaufverschraubung. */
    public function test_rule_2_kompakt_hk_liefert_ventil_kopf_ruecklauf(): void
    {
        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(istVentilHeizkoerper: false, kopfNormBestand: 'M30x1_5'));

        $this->assertContains('thermostatventil', $r->kategorien());
        $this->assertContains('thermostatkopf', $r->kategorien());
        $this->assertContains('ruecklaufverschraubung', $r->kategorien());
    }

    /** §5.3 Einrohr ohne einrohr-taugliche Armatur → harte Sperre, kein Ventil. */
    public function test_rule_3_einrohr_ohne_taugliche_armatur_sperrt(): void
    {
        // Alle 11 geseedeten accessories sind einrohr_tauglich=false.
        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(istVentilHeizkoerper: false, anschlussFuehrung: 'einrohr'));

        $this->assertNotContains('thermostatventil', $r->kategorien());
        $this->assertNotEmpty($r->sperren);
        $this->assertStringContainsString('§5.3', implode(' ', $r->sperren));
    }

    /** §5.3 Einrohr MIT einrohr-tauglicher Armatur → Ventil erscheint. */
    public function test_rule_3_einrohr_mit_tauglicher_armatur_erlaubt(): void
    {
        Accessory::create([
            'accessory_category_id' => AccessoryCategory::where('code', 'thermostatventil')->value('id'),
            'hersteller' => 'Test', 'herst_artikelnr' => 'EINROHR-1', 'name' => 'Einrohr-Ventil',
            'kopf_anschluss_norm' => 'M30x1_5', 'einrohr_tauglich' => true, 'voreinstellbar' => true, 'aktiv' => true,
        ]);

        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(istVentilHeizkoerper: false, anschlussFuehrung: 'einrohr'));

        $this->assertContains('thermostatventil', $r->kategorien());
    }

    /** §5.4 voreinstellbare Ventile bevorzugt. */
    public function test_rule_4_voreinstellbar_bevorzugt(): void
    {
        Accessory::create([
            'accessory_category_id' => AccessoryCategory::where('code', 'thermostatventil')->value('id'),
            'hersteller' => 'Test', 'herst_artikelnr' => 'NV-1', 'name' => 'Nicht-voreinstellbar',
            'kopf_anschluss_norm' => 'M30x1_5', 'einrohr_tauglich' => false, 'voreinstellbar' => false, 'aktiv' => true,
        ]);

        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(istVentilHeizkoerper: false));

        $ventilPos = collect($r->positionen)->firstWhere('kategorie', 'thermostatventil');
        $this->assertNotNull($ventilPos);
        $this->assertTrue((bool) Accessory::find($ventilPos['accessory_id'])->voreinstellbar);
    }

    /** §5.5 Voreinstellstufe == direkter HydraulicService-Aufruf (Naht, keine Duplikation). */
    public function test_rule_5_voreinstellstufe_gleich_hydraulic_service(): void
    {
        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(heizlastW: 1000, spreizungK: 7, dpVentilMbar: 100));

        $h = new HydraulicService;
        $direct = $h->voreinstellung($h->kvErforderlich($h->volumenstrom(1000, 7), 100));

        $this->assertSame($direct, $r->voreinstellstufe); // Nicht-Duplikations-Beweis
        $this->assertSame(4, $r->voreinstellstufe);         // 122,8 l/h → kv 0,39 → Stufe 4
    }

    /** §5.5 ohne Heizlast/Spreizung → keine Stufe, Hinweis "am Objekt einregulieren". */
    public function test_rule_5_ohne_heizlast_kein_stufe_hinweis(): void
    {
        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation);

        $this->assertNull($r->voreinstellstufe);
        $this->assertStringContainsString('§5.5', implode(' ', $r->hinweise));
    }

    /** §5.6 Altnorm-Kopf (RAV/RAVL) → Austauschempfehlung. */
    public function test_rule_6_altnorm_kopf_austauschempfehlung(): void
    {
        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(kopfNormBestand: 'RAVL'));

        $this->assertStringContainsString('§5.6', implode(' ', $r->hinweise));
        $this->assertStringContainsString('Austausch', implode(' ', $r->hinweise));
    }

    /** Datenlage: leere valve_insert_compatibility → 'regel-kandidaten', trotzdem Kandidaten. */
    public function test_datenqualitaet_regel_kandidaten_bei_leerer_kompatibilitaet(): void
    {
        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(istVentilHeizkoerper: false, kopfNormBestand: 'M30x1_5'));

        $this->assertSame('regel-kandidaten', $r->datenqualitaet);
        $this->assertNotEmpty($r->positionen);
    }

    /** Datenlage: Treffer in valve_insert_compatibility → 'serien-praezise' + dessen Einsatz. */
    public function test_datenqualitaet_serien_praezise_mit_fixture(): void
    {
        $einsatzId = Accessory::where('herst_artikelnr', '013G0270')->value('id'); // geseedeter RA-N-Einsatz
        ValveInsertCompatibility::create([
            'hk_hersteller' => 'Kermi', 'hk_serie' => 'therm-x2', 'baujahr_von' => 2010,
            'einsatz_accessory_id' => $einsatzId, 'kopf_anschluss_norm' => 'M30x1_5', 'quelle' => 'test-fixture',
        ]);

        $r = $this->service()->fuerHeizkoerper(new RadiatorSituation(
            istVentilHeizkoerper: true, hkHersteller: 'Kermi', hkSerie: 'therm-x2', baujahr: 2015,
        ));

        $this->assertSame('serien-praezise', $r->datenqualitaet);
        $einsatzPos = collect($r->positionen)->firstWhere('kategorie', 'ventileinsatz');
        $this->assertSame($einsatzId, $einsatzPos['accessory_id']);
    }
}
