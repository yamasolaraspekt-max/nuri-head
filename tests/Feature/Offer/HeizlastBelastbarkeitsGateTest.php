<?php

namespace Tests\Feature\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\User;
use App\Services\Heizlast\HeizlastBelastbarkeit;
use App\Services\Heizlast\WaermepumpenMatchService;
use App\Services\Offer\AuslegungVorschlagService;
use App\Services\Offer\OfferReadinessService;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AP-1 / Paket 5b — Heizlast-Belastbarkeits-Gate.
 * Prüft: (1) reiner Klassifizierer, (2) abgestufte technische_auslegung in OfferReadinessService,
 * (3) verbindlich-Markierung in AuslegungVorschlagService, (4) Match-Ranking-Verbindlichkeit,
 * (5) keine Persistenz durch den Klassifizierer.
 */
class HeizlastBelastbarkeitsGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);

        DB::table('article_groups')->insert([
            ['id' => 2, 'article_group' => 'Wärmepumpe', 'initial' => 'WP', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('master_sets')->insert(['id' => 1, 'article_group_id' => 2, 'name' => 'WP-Set', 'created_at' => now(), 'updated_at' => now()]);
        $this->seed(WpProduktFormularSeeder::class);
    }

    // ---------- (1) Reiner Klassifizierer ----------

    public function test_klassifizierer_stufen(): void
    {
        // belastbar: raumweise DIN (HeizlastRechner), keine unsicheren Eingaben
        $b = HeizlastBelastbarkeit::beurteile('berechnet', 'HeizlastRechner', 'berechnet', 8.5, false);
        $this->assertSame(HeizlastBelastbarkeit::BELASTBAR, $b['stufe']);
        $this->assertSame(1.0, $b['reifegrad']);
        $this->assertTrue($b['verbindlich']);

        // eingeschränkt: berechnet, aber Eingaben unsicher (ergebnis_hinweis)
        $e1 = HeizlastBelastbarkeit::beurteile('berechnet', 'HeizlastRechner', 'berechnet', 8.5, true);
        $this->assertSame(HeizlastBelastbarkeit::EINGESCHRAENKT, $e1['stufe']);
        $this->assertSame(0.6, $e1['reifegrad']);
        $this->assertFalse($e1['verbindlich']);

        // eingeschränkt: Verbrauch (gemessen)
        $e2 = HeizlastBelastbarkeit::beurteile('gemessen', 'Kundenangabe', 'manuell', 9.0, false);
        $this->assertSame(HeizlastBelastbarkeit::EINGESCHRAENKT, $e2['stufe']);

        // eingeschränkt: berechnet, aber nicht-Rechner-Quelle (Typologie)
        $e3 = HeizlastBelastbarkeit::beurteile('berechnet', 'Typologie TABULA', 'default', 9.0, false);
        $this->assertSame(HeizlastBelastbarkeit::EINGESCHRAENKT, $e3['stufe']);

        // vorläufig: geschätzt
        $v = HeizlastBelastbarkeit::beurteile('geschaetzt', 'Kundenangabe', 'manuell', 10.0, false);
        $this->assertSame(HeizlastBelastbarkeit::VORLAEUFIG, $v['stufe']);
        $this->assertSame(0.3, $v['reifegrad']);
        $this->assertFalse($v['verbindlich']);

        // unzureichend: kein Wert
        $u1 = HeizlastBelastbarkeit::beurteile(null);
        $this->assertSame(HeizlastBelastbarkeit::UNZUREICHEND, $u1['stufe']);
        $this->assertSame(0.0, $u1['reifegrad']);

        // unzureichend: nicht plausibel (<=0)
        $u2 = HeizlastBelastbarkeit::beurteile('berechnet', 'HeizlastRechner', 'berechnet', 0.0, false);
        $this->assertSame(HeizlastBelastbarkeit::UNZUREICHEND, $u2['stufe']);
    }

    // ---------- (2) OfferReadinessService: abgestufte technische_auslegung ----------

    public function test_belastbare_heizlast_zaehlt_technische_auslegung_voll(): void
    {
        $lpl = $this->wpVorgang(10, heizlast: 8.5, datenlage: 'berechnet', quelle: 'HeizlastRechner');
        $r = app(OfferReadinessService::class)->fuer($lpl);

        $this->assertSame('belastbar', $r['heizlast_belastbarkeit']['stufe']);
        $krit = collect($r['kriterien'])->firstWhere('key', 'technische_auslegung');
        $this->assertSame(1.0, $krit['grad']);
        $this->assertTrue($krit['erfuellt']);
    }

    public function test_geschaetzte_heizlast_reduziert_technische_auslegung_nicht_voll(): void
    {
        $lpl = $this->wpVorgang(20, heizlast: 8.5, datenlage: 'geschaetzt', quelle: 'Kundenangabe');
        $r = app(OfferReadinessService::class)->fuer($lpl);

        $this->assertSame('vorlaeufig', $r['heizlast_belastbarkeit']['stufe']);
        $krit = collect($r['kriterien'])->firstWhere('key', 'technische_auslegung');
        $this->assertSame(0.3, $krit['grad']);
        $this->assertFalse($krit['erfuellt']);           // NICHT voll → nicht „technisch freigegeben"
        $this->assertLessThan(100, $r['percent']);       // Reife nicht 100 %
        $this->assertNotSame('angebotsfaehig', $r['status']);
    }

    public function test_berechnet_mit_unsicheren_eingaben_ist_nur_eingeschraenkt(): void
    {
        // Heizlast berechnet (Rechner), aber ergebnis_hinweis gesetzt → Eingaben unsicher → eingeschränkt (0.6)
        $lpl = $this->wpVorgang(25, heizlast: 8.5, datenlage: 'berechnet', quelle: 'HeizlastRechner', ergebnisHinweis: true);
        $r = app(OfferReadinessService::class)->fuer($lpl);

        $this->assertSame('eingeschraenkt', $r['heizlast_belastbarkeit']['stufe']);
        $krit = collect($r['kriterien'])->firstWhere('key', 'technische_auslegung');
        $this->assertSame(0.6, $krit['grad']);
        $this->assertFalse($krit['erfuellt']);
    }

    public function test_fehlende_heizlast_bleibt_offen(): void
    {
        $lpl = $this->wpVorgang(30, heizlast: null);
        $r = app(OfferReadinessService::class)->fuer($lpl);

        $this->assertSame('unzureichend', $r['heizlast_belastbarkeit']['stufe']);
        $krit = collect($r['kriterien'])->firstWhere('key', 'technische_auslegung');
        $this->assertSame(0.0, $krit['grad']);
        $this->assertFalse($krit['erfuellt']);
    }

    // ---------- (3) AuslegungVorschlagService: Vorschau läuft, aber markiert ----------

    public function test_vorschau_laeuft_bei_vorlaeufig_aber_unverbindlich(): void
    {
        $lpl = $this->wpVorgang(40, heizlast: 8.5, datenlage: 'geschaetzt', quelle: 'Kundenangabe');
        $v = app(AuslegungVorschlagService::class)->fuer($lpl);

        $this->assertTrue($v['auslegung_vorhanden']);     // Vorschau läuft weiter
        $this->assertFalse($v['verbindlich']);            // aber unverbindlich
        $this->assertSame('vorlaeufig', $v['belastbarkeit']['stufe']);
        $this->assertStringContainsString('UNVERBINDLICH', $v['kurzergebnis']);
    }

    public function test_vorschau_belastbar_ist_verbindlich(): void
    {
        $lpl = $this->wpVorgang(45, heizlast: 8.5, datenlage: 'berechnet', quelle: 'HeizlastRechner');
        $v = app(AuslegungVorschlagService::class)->fuer($lpl);

        $this->assertTrue($v['verbindlich']);
        $this->assertSame('belastbar', $v['belastbarkeit']['stufe']);
        $this->assertStringNotContainsString('UNVERBINDLICH', $v['kurzergebnis']);
    }

    // ---------- (4) WaermepumpenMatchService: Ranking-Verbindlichkeit ----------

    public function test_match_ranking_unverbindlich_wenn_nicht_belastbar(): void
    {
        $match = app(WaermepumpenMatchService::class);

        $unverbindlich = $match->kandidaten(10.0, 'luft_wasser', 'heizkoerper', 55.0, 6, belastbar: false);
        $this->assertFalse($unverbindlich['verbindlich']);
        $this->assertStringContainsString('nicht belastbar', (string) $unverbindlich['hinweis']);

        $verbindlich = $match->kandidaten(10.0, 'luft_wasser', 'heizkoerper', 55.0); // Default belastbar=true
        $this->assertTrue($verbindlich['verbindlich']);
    }

    // ---------- (5) Keine Persistenz durch den Klassifizierer ----------

    public function test_klassifizierer_schreibt_nichts(): void
    {
        $lpl = $this->wpVorgang(50, heizlast: 8.5, datenlage: 'geschaetzt', quelle: 'Kundenangabe');
        $tabellen = ['anforderungsprofile', 'anforderungsprofil_werte', 'offers', 'offer_details', 'lead_product_lists'];
        $vorher = [];
        foreach ($tabellen as $t) {
            $vorher[$t] = DB::table($t)->count();
        }

        app(OfferReadinessService::class)->fuer($lpl);
        app(AuslegungVorschlagService::class)->fuer($lpl);

        foreach ($tabellen as $t) {
            $this->assertSame($vorher[$t], DB::table($t)->count(), "Tabelle {$t} verändert.");
        }
    }

    // ---------- Helfer ----------

    private function wpVorgang(int $seed, ?float $heizlast, string $datenlage = 'berechnet', string $quelle = 'HeizlastRechner', bool $ergebnisHinweis = false): \App\Models\LeadProductList
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'Kunde', 'lastname' => 'Test', 'email' => 'k@example.com', 'phone' => '0123', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'Stadt', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_product_lists')->insert(['id' => $seed, 'customer_id' => $customer, 'alternative_id' => $alt, 'product_id' => 2, 'status' => 'lead', 'interest' => 'WP', 'service_id' => 1, 'service' => 'complete', 'department_id' => 1, 'employee_id' => 1, 'created_at' => now(), 'updated_at' => now()]);

        if ($heizlast !== null) {
            $profilId = $seed + 500;
            DB::table('anforderungsprofile')->insert(['id' => $profilId, 'verankerbar_type' => LeadAlternativeAdd::class, 'verankerbar_id' => $alt, 'version' => 1, 'status' => 'aktiv', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('anforderungsprofil_werte')->insert(['anforderungsprofil_id' => $profilId, 'schluessel' => 'phi_hl_kw', 'wert' => (string) $heizlast, 'wert_num' => $heizlast, 'datenlage' => $datenlage, 'quelle' => $quelle, 'erfassungsweg' => 'berechnet', 'created_at' => now(), 'updated_at' => now()]);
            if ($ergebnisHinweis) {
                DB::table('anforderungsprofil_werte')->insert(['anforderungsprofil_id' => $profilId, 'schluessel' => 'ergebnis_hinweis', 'wert' => 'enthält geschätzte/ungeprüfte Eingaben', 'wert_num' => null, 'datenlage' => 'berechnet', 'quelle' => 'HeizlastRechner', 'erfassungsweg' => 'berechnet', 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        return \App\Models\LeadProductList::with(['customer', 'alternative', 'articleGroup', 'checklistValues'])->find($seed);
    }
}
