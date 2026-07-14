<?php

namespace Tests\Feature\Energie;

use App\Http\Controllers\Energie\EnergieAuslegungController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * P1a (Stufe 3b) — CHARAKTERISIERUNG (Golden Master) des HEUTIGEN Ist-Verhaltens der WP-Auslegung
 * (EnergieAuslegungController::wpBerechnen/wpDokument → wpErgebnis). Schreibt das aktuelle Verhalten
 * fest, BEVOR P1b die Kosten-/Förder-/Dokument-Logik in Dienste extrahiert — nach der Extraktion müssen
 * diese Werte WERTGLEICH bleiben. KEINE Produktivänderung, kein Ranking, kein Orchestrator, keine View-Änderung.
 *
 * Charakterisiert wird das vom Controller erzeugte VIEW-MODEL (`ergebnis`) über den direkten
 * Methodenaufruf — die Blade wird NICHT gerendert (View-Details sind nicht das Extraktionsziel).
 * Ist-Befund: jaz/verbrauch/kosten/förderung hängen nur an den Formular-Operanden + Konstanten;
 * das gewählte Gerät liefert nur Anzeigefelder + den wp_index-Null-Check.
 *
 * Golden-Werte per Erstlauf gegen den unveränderten Stand f0055d6 erfasst und exakt gepinnt.
 */
class WpAuslegungCharakterisierungTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        $this->seedWaermepumpe();
    }

    /** Zwei Geräte (idx0 TM-8, idx1 TM-12), damit auch Geräte-Unabhängigkeit belegbar ist. */
    private function seedWaermepumpe(): void
    {
        DB::table('brands')->insert(['id' => 1, 'name' => 'TestMarke', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('products')->insert([
            ['id' => 1, 'brand_id' => 1, 'model' => 'TM-8', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'brand_id' => 1, 'model' => 'TM-12', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('product_heat_pump_specs')->insert([
            ['id' => 1, 'product_id' => 1, 'heizleistung_a7_w35_kw' => 8.0, 'heizleistung_am7_w35_kw' => 6.0, 'scop_35' => 4.5, 'scop_55' => 3.2, 'max_vorlauf_c' => 60.0, 'modulation_min_kw' => 2.0, 'modulation_max_kw' => 10.0, 'geraetetyp' => 'luft_wasser', 'serie' => 'TM', 'kaeltemittel' => 'R290', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'product_id' => 2, 'heizleistung_a7_w35_kw' => 12.0, 'heizleistung_am7_w35_kw' => 9.0, 'scop_35' => 5.0, 'scop_55' => 3.6, 'max_vorlauf_c' => 65.0, 'modulation_min_kw' => 3.0, 'modulation_max_kw' => 14.0, 'geraetetyp' => 'luft_wasser', 'serie' => 'TM', 'kaeltemittel' => 'R290', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /** @return array<string,mixed> Normalfall-Formular (vollständig). */
    private function normalfall(array $over = []): array
    {
        return array_merge([
            'wp_index' => 0, 'heizlast_kw' => 8.0, 'heizsystem' => 'heizkoerper', 'wp_typ' => 'luft_wasser',
            'personen_im_haushalt' => 4, 'ww_mit_wp' => true, 'badewanne_vorhanden' => false, 'investition' => 25000,
            'heizungsart' => 'gas', 'heizung_alter' => 25, 'anzahl_we' => 1, 'selbst_bewohnte_we' => 1,
            'effizienzbonus' => false, 'einkommensbonus' => false, 'strompreis' => 0.30,
        ], $over);
    }

    private function ctrlView(string $methode, array $form): View
    {
        $request = Request::create(route('energie.wp-auslegung.berechnen'), 'POST', $form);
        $view = (new EnergieAuslegungController)->{$methode}($request);
        $this->assertInstanceOf(View::class, $view, "$methode lieferte keine View.");

        return $view;
    }

    /** wpBerechnen direkt → View-Model `ergebnis` (Blade wird NICHT gerendert). */
    private function ergebnis(array $form): array
    {
        return $this->ctrlView('wpBerechnen', $form)->getData()['ergebnis'];
    }

    public function test_normalfall_pinnt_rechenkette_und_rundung(): void
    {
        $e = $this->ergebnis($this->normalfall());

        // Rechenkette + Rundungsstellen (jaz 2 Nachkommastellen; q/strom/stromkosten ganzzahlig gerundet).
        $this->assertSame(2.9, $e['jaz']);
        $this->assertSame(55.0, $e['vorlauf_temp']);
        $this->assertSame(2000, $e['b_vh']);
        $this->assertSame(16000.0, $e['q_heiz_kwh']);   // 8.0 kW * B_VH_DEFAULT(2000)
        $this->assertSame(2800.0, $e['q_ww_kwh']);       // 4 Personen * 700 kWh
        $this->assertSame(6483.0, $e['strom_kwh']);
        $this->assertSame(0.3, $e['strompreis']);
        $this->assertSame(1945.0, $e['stromkosten_jahr']); // round(6483 * 0.30)
        $this->assertSame(25000.0, $e['investition_netto']);
        $this->assertNull($e['verbrauch_plausi']);         // kein Verbrauch angegeben
        $this->assertSame('Heizkörper (Vorlauf ~55 °C)', $e['heizsystem_label']);
        $this->assertSame('Luft/Wasser', $e['wp_typ_label']);

        // Warmwasser-Teilergebnis
        $this->assertSame(2800.0, $e['ww']['q_ww_kwh']);
        $this->assertSame(0.8, $e['ww']['phi_ww_kw']);
        $this->assertSame(200, $e['ww']['speicher_liter']);

        // Förderung (BEG): Grund 30 + Klima 20 = 50 % bei selbstbewohnt, keine Boni
        $this->assertSame(12500.0, $e['foerderung']['zuschuss']);
        $this->assertSame(50.0, $e['foerderung']['effektiver_satz_pct']);
        $this->assertSame(12500.0, $e['foerderung']['netto_investition']);
        $this->assertTrue($e['foerderung']['klima_berechtigt']);
    }

    public function test_warmwasser_ohne_wp_senkt_stromverbrauch(): void
    {
        $e = $this->ergebnis($this->normalfall(['ww_mit_wp' => false]));

        $this->assertFalse($e['ww_mit_wp']);
        $this->assertSame(0.0, $e['ww']['phi_ww_kw']);       // WW nicht über WP
        $this->assertSame(5517.0, $e['strom_kwh']);          // < 6483 (Normalfall mit WW über WP)
        $this->assertSame(1655.0, $e['stromkosten_jahr']);
        $this->assertSame(2800.0, $e['q_ww_kwh']);           // Bedarf unverändert
    }

    public function test_foerderung_ohne_klimabonus_bei_heizungsart_keine(): void
    {
        $e = $this->ergebnis($this->normalfall(['heizungsart' => 'keine', 'heizung_alter' => 0]));

        $this->assertFalse($e['foerderung']['klima_berechtigt']);
        $this->assertSame(0, $e['foerderung']['saetze']['klima']);
        $this->assertSame(7500.0, $e['foerderung']['zuschuss']);       // nur Grund 30 %
        $this->assertSame(30.0, $e['foerderung']['effektiver_satz_pct']);
        $this->assertSame(17500.0, $e['foerderung']['netto_investition']);
    }

    public function test_foerderung_mit_effizienz_und_einkommensbonus(): void
    {
        $e = $this->ergebnis($this->normalfall(['effizienzbonus' => true, 'einkommensbonus' => true]));

        $this->assertSame(5, $e['foerderung']['saetze']['effizienz']);
        $this->assertSame(30, $e['foerderung']['saetze']['einkommen']);
        $this->assertSame(17500.0, $e['foerderung']['zuschuss']);      // 70 % Deckel-Satz
        $this->assertSame(70.0, $e['foerderung']['effektiver_satz_pct']);
        $this->assertSame(7500.0, $e['foerderung']['netto_investition']);
    }

    public function test_fehlender_preis_investition_null(): void
    {
        $e = $this->ergebnis($this->normalfall(['investition' => 0]));

        $this->assertSame(0.0, $e['investition_netto']);
        $this->assertSame(0.0, $e['foerderung']['zuschuss']);
        $this->assertSame(0.0, $e['foerderung']['effektiver_satz_pct']);
        $this->assertSame(0.0, $e['foerderung']['netto_investition']);
    }

    public function test_wp_index_ohne_geraet_liefert_fehler_ohne_ergebnis(): void
    {
        // wp_index ohne vorhandenes Gerät → View mit Fehlermeldung, ergebnis null (kein Redirect, keine Exception).
        $view = $this->ctrlView('wpBerechnen', $this->normalfall(['wp_index' => 5]));
        $data = $view->getData();

        $this->assertNull($data['ergebnis']);
        $this->assertNotNull($data['fehler']);
        $this->assertStringContainsString('nicht gefunden', $data['fehler']);
    }

    public function test_unvollstaendige_eingabe_wirft_validierungsfehler(): void
    {
        $form = $this->normalfall();
        unset($form['heizlast_kw']); // Pflichtfeld

        $this->expectException(ValidationException::class);
        $this->ergebnis($form);
    }

    public function test_dokument_liefert_dieselbe_rechenwahrheit_wie_rechenseite(): void
    {
        $form = $this->normalfall();
        $rechen = $this->ergebnis($form);
        $dokument = $this->ctrlView('wpDokument', $form)->getData()['ergebnis'];

        // wpDokument nutzt dieselbe wpErgebnis-Wahrheit — die tragenden Rechenwerte sind identisch.
        foreach (['jaz', 'q_heiz_kwh', 'q_ww_kwh', 'strom_kwh', 'stromkosten_jahr', 'investition_netto'] as $k) {
            $this->assertSame($rechen[$k], $dokument[$k], "Dokument weicht bei $k ab.");
        }
        $this->assertSame($rechen['foerderung']['zuschuss'], $dokument['foerderung']['zuschuss']);
    }

    // ── Matrix 5.3: Bedarf/Rundung ──

    public function test_niedriger_heizbedarf_skaliert_q_heiz_und_strom(): void
    {
        $e = $this->ergebnis($this->normalfall(['heizlast_kw' => 4.0]));
        $this->assertSame(8000.0, $e['q_heiz_kwh']);      // 4.0 * 2000
        $this->assertSame(3724.0, $e['strom_kwh']);
        $this->assertSame(1117.0, $e['stromkosten_jahr']); // round(3724*0.30)
    }

    public function test_hoher_heizbedarf_skaliert_q_heiz_und_strom(): void
    {
        $e = $this->ergebnis($this->normalfall(['heizlast_kw' => 15.0]));
        $this->assertSame(30000.0, $e['q_heiz_kwh']);     // 15.0 * 2000
        $this->assertSame(11310.0, $e['strom_kwh']);
        $this->assertSame(3393.0, $e['stromkosten_jahr']);
    }

    // ── Matrix 5.2: Warmwasser-Bedarf über Personen ──

    public function test_warmwasserbedarf_skaliert_mit_personen(): void
    {
        $e1 = $this->ergebnis($this->normalfall(['personen_im_haushalt' => 1]));
        $this->assertSame(700.0, $e1['q_ww_kwh']);        // 1 * 700
        $this->assertSame(5759.0, $e1['strom_kwh']);

        $e6 = $this->ergebnis($this->normalfall(['personen_im_haushalt' => 6]));
        $this->assertSame(4200.0, $e6['q_ww_kwh']);       // 6 * 700
        $this->assertSame(6966.0, $e6['strom_kwh']);
    }

    // ── Matrix 5.3: Verbrauchsmethode (Plausibilisierung) ──

    public function test_verbrauchsmethode_erzeugt_plausi_block(): void
    {
        $ohne = $this->ergebnis($this->normalfall());
        $this->assertNull($ohne['verbrauch_plausi']);      // kein Verbrauch → null

        $mit = $this->ergebnis($this->normalfall([
            'verbrauch_menge' => 18000, 'verbrauch_einheit' => 'kWh',
            'aktuelles_heizmedium' => 'erdgas', 'verbrauch_zeitraum_jahre' => 1, 'enthaelt_warmwasser' => true,
        ]));
        $this->assertNotNull($mit['verbrauch_plausi']);    // Verbrauch angegeben → Plausi-Block
        $this->assertSame(6483.0, $mit['strom_kwh']);      // Kernrechnung unverändert (Plausi ist additiv)
    }

    // ── Matrix 5.5: Förder-Deckel ──

    public function test_foerderdeckel_begrenzt_foerderfaehige_kosten(): void
    {
        $e = $this->ergebnis($this->normalfall(['investition' => 50000]));
        $this->assertSame(50000.0, $e['investition_netto']);        // volle Investition
        $this->assertSame(30000, $e['foerderung']['deckel']);       // BEG-Deckel 1 WE
        $this->assertSame(30000.0, $e['foerderung']['foerderfaehig']); // gedeckelt
        $this->assertSame(15000.0, $e['foerderung']['zuschuss']);   // 50 % von 30000
    }

    // ── Matrix 5.6: Geräte-Unabhängigkeit (Ist-Kernbefund) ──

    public function test_geraetewahl_aendert_nur_anzeige_nicht_die_zahlen(): void
    {
        $a = $this->ergebnis($this->normalfall(['wp_index' => 0]));
        $b = $this->ergebnis($this->normalfall(['wp_index' => 1]));

        // Anzeige (Gerät) unterscheidet sich …
        $this->assertSame('TM-8', $a['wp']['modell']);
        $this->assertSame('TM-12', $b['wp']['modell']);
        // … die Rechenwerte NICHT (heutiges Ist: Zahlen hängen nur an Formular-Operanden, nicht am Gerät).
        foreach (['jaz', 'q_heiz_kwh', 'q_ww_kwh', 'strom_kwh', 'stromkosten_jahr', 'investition_netto'] as $k) {
            $this->assertSame($a[$k], $b[$k], "Gerätewechsel änderte $k — Ist-Verhalten war Geräte-Unabhängigkeit.");
        }
        $this->assertSame($a['foerderung']['zuschuss'], $b['foerderung']['zuschuss']);
    }

    // ── Matrix 5.7: Dokument bei fehlendem Preis ──

    public function test_dokument_bei_investition_null(): void
    {
        $d = $this->ctrlView('wpDokument', $this->normalfall(['investition' => 0]))->getData()['ergebnis'];
        $this->assertSame(0.0, $d['investition_netto']);
        $this->assertSame(0.0, $d['foerderung']['zuschuss']);
    }
}
