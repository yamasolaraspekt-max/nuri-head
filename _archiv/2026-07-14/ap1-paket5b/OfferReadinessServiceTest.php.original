<?php

namespace Tests\Feature\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductChecklistValue;
use App\Models\LeadProductList;
use App\Models\ProductFormula;
use App\Services\Offer\OfferReadinessService;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Paket 1 — Angebotsreife (WP), read-only/on-the-fly. Prüft echte Kriterien-Berechnung, Grade,
 * Status, Determinismus, keine Persistenz.
 */
class OfferReadinessServiceTest extends TestCase
{
    use RefreshDatabase;

    private int $cid = 100;
    private int $aid = 200;
    private int $lid = 300;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // WP-Gewerk + Preisgrundlage + WP-Formular als Fixture.
        $this->relax(function () {
            DB::table('article_groups')->insert(['id' => 2, 'article_group' => 'Wärmepumpe', 'initial' => 'WP', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('master_sets')->insert(['id' => 1, 'article_group_id' => 2, 'name' => 'WP-Set', 'created_at' => now(), 'updated_at' => now()]);
        });
        $this->seed(WpProduktFormularSeeder::class);
    }

    private function service(): OfferReadinessService
    {
        return new OfferReadinessService();
    }

    private function relax(callable $fn): void
    {
        DB::statement("SET SESSION sql_mode=''");
        $fn();
        DB::statement("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
    }

    /** @param array<string,bool> $opts kunde/objekt/bedarf/dienstleistung/zustaendigkeit */
    private function wpLead(array $opts = []): LeadProductList
    {
        $o = array_merge(['kunde' => false, 'objekt' => false, 'bedarf' => false, 'dienstleistung' => false, 'zustaendigkeit' => false], $opts);

        $this->relax(function () use ($o) {
            DB::table('new_leads')->insert([
                'id' => $this->cid, 'customer_type' => 'privat',
                'name' => $o['kunde'] ? 'Max' : null, 'lastname' => $o['kunde'] ? 'Muster' : null,
                'email' => $o['kunde'] ? 'max@muster.de' : null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('lead_alternative_adds')->insert([
                'id' => $this->aid,
                'street' => $o['objekt'] ? 'Weg 1' : null, 'postcode' => $o['objekt'] ? '10115' : null, 'city' => $o['objekt'] ? 'Berlin' : null,
                'objective' => $o['bedarf'] ? 'WP-Umstieg' : null, 'ready_for_offer' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('lead_product_lists')->insert([
                'id' => $this->lid, 'customer_id' => $this->cid, 'alternative_id' => $this->aid, 'product_id' => 2,
                'service_id' => $o['dienstleistung'] ? 1 : null,
                'department_id' => $o['zustaendigkeit'] ? 1 : null, 'employee_id' => $o['zustaendigkeit'] ? 1 : null,
                'status' => 'lead', 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        return LeadProductList::with(['customer', 'alternative', 'articleGroup', 'checklistValues'])->findOrFail($this->lid);
    }

    /** @return array<int,string> */
    private function requiredKeys(): array
    {
        return collect(ProductFormula::where('product_id', 2)->firstOrFail()->fields)
            ->filter(fn ($f) => ! empty($f['required']))
            ->map(fn ($f) => $f['key'])->values()->all();
    }

    private function fuelleFormular(float $anteil): void
    {
        $required = $this->requiredKeys();
        $anzahl = (int) floor(count($required) * $anteil);
        $filled = [];
        foreach (array_slice($required, 0, $anzahl) as $k) {
            $filled[$k] = 'x';
        }
        $formula = ProductFormula::where('product_id', 2)->firstOrFail();
        LeadProductChecklistValue::create([
            'lead_product_list_id' => $this->lid, 'product_formula_id' => $formula->id,
            'customer_id' => $this->cid, 'alternative_id' => $this->aid, 'product_id' => 2,
            'section_name' => $formula->section_name,
            'filled_values' => $filled, 'formula_snapshot' => $formula->fields, 'formula_version' => $formula->version,
        ]);
    }

    private function setzeAuslegung(): void
    {
        $this->relax(function () {
            DB::table('anforderungsprofile')->insert(['id' => 1, 'verankerbar_type' => LeadAlternativeAdd::class, 'verankerbar_id' => $this->aid, 'version' => 1, 'status' => 'aktiv', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('anforderungsprofil_werte')->insert(['id' => 1, 'anforderungsprofil_id' => 1, 'schluessel' => 'phi_hl_kw', 'wert_num' => 8.5, 'created_at' => now(), 'updated_at' => now()]);
        });
    }

    public function test_ohne_pflichtdaten_ist_blockiert_und_niedrig(): void
    {
        $r = $this->service()->fuer($this->wpLead());

        $this->assertSame('blockiert', $r['status']);
        $this->assertFalse($r['angebotsfaehig']);
        $this->assertGreaterThan(0, $r['aufgaben']['blockierend']);
        // Nur Gewerk (WP) + Preisgrundlage + Formular-vorhanden erfüllt → niedriger %.
        $this->assertLessThan(50, $r['percent']);
        $this->assertSame('Wärmepumpe', $r['gewerk']);
    }

    public function test_mit_kerndaten_hoeher_aber_noch_nicht_angebotsfaehig(): void
    {
        $r = $this->service()->fuer($this->wpLead(['kunde' => true, 'objekt' => true, 'bedarf' => true, 'dienstleistung' => true, 'zustaendigkeit' => true]));

        $this->assertFalse($r['angebotsfaehig']);
        $this->assertSame(0, $r['aufgaben']['blockierend']); // keine harten Blocker mehr offen
        $this->assertContains($r['status'], ['unvollstaendig', 'in_pruefung']); // Formular/Technik noch offen
        $this->assertGreaterThanOrEqual(70, $r['percent']);
        $this->assertLessThan(100, $r['percent']);
    }

    public function test_formular_ausfuellen_erhoeht_prozent(): void
    {
        $basis = $this->service()->fuer($this->wpLead(['kunde' => true, 'objekt' => true, 'bedarf' => true, 'dienstleistung' => true, 'zustaendigkeit' => true]))['percent'];

        $this->fuelleFormular(0.5);
        $mitHalb = $this->service()->fuerId($this->lid)['percent'];

        $this->assertGreaterThan($basis, $mitHalb, 'Teilweise ausgefülltes Formular muss den Fortschritt erhöhen.');
    }

    public function test_alle_pflicht_erfuellt_ist_angebotsfaehig_und_100(): void
    {
        $this->wpLead(['kunde' => true, 'objekt' => true, 'bedarf' => true, 'dienstleistung' => true, 'zustaendigkeit' => true]);
        $this->fuelleFormular(1.0);
        $this->setzeAuslegung();

        $r = $this->service()->fuerId($this->lid);

        $this->assertTrue($r['angebotsfaehig'], 'Alle Pflichtkriterien erfüllt → angebotsfähig.');
        $this->assertSame('angebotsfaehig', $r['status']);
        $this->assertSame(100, $r['percent']);
        $this->assertSame(0, $r['aufgaben']['offen']);
        $this->assertSame('Angebot kann erstellt werden.', $r['hinweis']);
    }

    public function test_prozent_ist_deterministisch(): void
    {
        $lead = $this->wpLead(['kunde' => true, 'objekt' => true]);
        $a = $this->service()->fuer($lead);
        $b = $this->service()->fuer($lead->fresh(['customer', 'alternative', 'articleGroup', 'checklistValues']));

        $this->assertSame($a['percent'], $b['percent']);
        $this->assertSame($a['status'], $b['status']);
    }

    public function test_keine_persistenz_von_reife(): void
    {
        $lead = $this->wpLead(['kunde' => true]); // Fixture-Anlage NICHT dem Service anlasten
        $vorherFormeln = ProductFormula::count();
        $vorherListen = LeadProductList::count();
        $vorherWerte = LeadProductChecklistValue::count();

        $this->service()->fuer($lead);

        $this->assertSame($vorherFormeln, ProductFormula::count());
        $this->assertSame($vorherListen, LeadProductList::count());
        $this->assertSame($vorherWerte, LeadProductChecklistValue::count());
        // Keine Reife-Statustabelle im Schema.
        $this->assertFalse(Schema::hasTable('offer_readiness'));
        $this->assertFalse(Schema::hasTable('angebotsreife'));
    }
}
