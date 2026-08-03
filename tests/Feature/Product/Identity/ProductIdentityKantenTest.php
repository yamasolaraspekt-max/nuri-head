<?php

namespace Tests\Feature\Product\Identity;

use App\Models\Brand;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Product;
use App\Services\Product\Identity\IdentityMatch;
use App\Services\Product\Identity\ProductIdentity;
use App\Services\Product\Identity\ProductIdentityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AUF-P1-S4 · Die 16 Kantenfälle aus Spez 11-…md §9 — je ein benannter Test (K-03).
 *
 * §14-Nachtrag: brands.type='manufacturer' existiert im Bestand nicht (0/50) —
 * Kante 14 ist der REGELFALL. Fixtures setzen den brand-Typ deshalb IMMER explizit.
 */
class ProductIdentityKantenTest extends TestCase
{
    use RefreshDatabase;

    private ProductIdentityService $dienst;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dienst = new ProductIdentityService();
    }

    /** Hersteller-Marke (type=manufacturer) — im Bestand der Sonderfall, §14. */
    private function herstellerMarke(string $name = 'Viessmann'): Brand
    {
        return Brand::create(['name' => $name, 'type' => 'manufacturer', 'status' => 1]);
    }

    /** Nicht-Hersteller-Marke — der Regelfall im Bestand (§14). */
    private function handelsMarke(string $name = 'Eigenmarke'): Brand
    {
        return Brand::create(['name' => $name, 'type' => 'brand', 'status' => 1]);
    }

    private function lieferant(string $name): Distributor
    {
        return Distributor::create(['name' => $name, 'short_name' => strtoupper(substr($name, 0, 4)), 'status' => 1]);
    }

    // ── Kante 1 ─────────────────────────────────────────────────────────────

    public function test_kante_01_fuehrende_null_0815_und_815_sind_zwei_artikel(): void
    {
        $marke = $this->herstellerMarke();
        Product::create(['product' => 'Artikel A', 'article_no' => '0815', 'brand_id' => $marke->id]);

        $match = $this->dienst->resolve(new ProductIdentity(
            manufacturerArticleNo: '815',
            brandId: $marke->id,
        ));

        $this->assertSame(IdentityMatch::NEU, $match->ergebnis, 'Führende Nullen sind bedeutungstragend — 815 darf 0815 nicht treffen.');

        $this->dienst->createFrom(new ProductIdentity(manufacturerArticleNo: '815', brandId: $marke->id, name: 'Artikel B'));

        $this->assertSame(2, Product::count(), 'Keine Verschmelzung: 0815 und 815 sind zwei Artikel.');
    }

    // ── Kante 2 ─────────────────────────────────────────────────────────────

    public function test_kante_02_gtin_in_8_12_13_14_stellen_ist_ein_artikel(): void
    {
        // 12345670 ist eine gültige EAN-8 (GS1-Prüfziffer 0).
        $produkt = $this->dienst->createFrom(new ProductIdentity(gtin: '12345670', name: 'GTIN-Artikel'));

        $this->assertSame('00000012345670', $produkt->gtin_normalized);

        foreach (['000012345670', '0000012345670', '00000012345670'] as $variante) {
            $match = $this->dienst->resolve(new ProductIdentity(gtin: $variante));

            $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis, "GTIN-Variante {$variante} muss treffen.");
            $this->assertSame(1, $match->stufe);
            $this->assertSame($produkt->id, $match->product->id);
        }

        $this->assertSame(1, Product::count());
    }

    // ── Kante 3 ─────────────────────────────────────────────────────────────

    public function test_kante_03_gtin_mit_falscher_pruefziffer_gilt_als_nicht_vorhanden_leiter_faellt_auf_stufe_2(): void
    {
        $marke = $this->herstellerMarke();
        $produkt = Product::create(['product' => 'Stufe-2-Artikel', 'article_no' => 'VIT-250', 'brand_id' => $marke->id]);

        // 12345671: Prüfziffer falsch (richtig wäre 0).
        $match = $this->dienst->resolve(new ProductIdentity(
            gtin: '12345671',
            manufacturerArticleNo: 'VIT-250',
            brandId: $marke->id,
        ));

        $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis);
        $this->assertSame(2, $match->stufe, 'Ungültige GTIN gilt als nicht vorhanden — die Leiter fällt auf Stufe 2.');
        $this->assertSame($produkt->id, $match->product->id);
    }

    // ── Kante 4 ─────────────────────────────────────────────────────────────

    public function test_kante_04_gleiche_gtin_verschiedene_brand_id_ist_konflikt_ohne_schreibzugriff(): void
    {
        $markeA = $this->herstellerMarke('Hersteller A');
        $markeB = $this->herstellerMarke('Hersteller B');

        $this->dienst->createFrom(new ProductIdentity(gtin: '12345670', brandId: $markeA->id, name: 'GTIN-Artikel'));

        $produkteVorher = Product::count();

        $match = $this->dienst->resolve(new ProductIdentity(gtin: '12345670', brandId: $markeB->id));

        $this->assertSame(IdentityMatch::KONFLIKT, $match->ergebnis);
        $this->assertNull($match->product, 'Konflikt heißt: keine Zuordnung.');
        $this->assertSame($produkteVorher, Product::count(), 'Kein Schreibzugriff bei Konflikt.');
        $this->assertSame(0, DB::table('product_identity_suggestions')->count());
    }

    // ── Kante 5 ─────────────────────────────────────────────────────────────

    public function test_kante_05_derselbe_artikel_bei_zwei_lieferanten_ein_produkt_zwei_preiszeilen(): void
    {
        $marke = $this->herstellerMarke();
        $d1 = $this->lieferant('Sonepar');
        $d2 = $this->lieferant('Rexel');

        $produkt = $this->dienst->createFrom(
            new ProductIdentity(manufacturerArticleNo: 'VIT-250', brandId: $marke->id, name: 'Wärmepumpe'),
        );
        DistributorPrice::create(['distributor_id' => $d1->id, 'product_id' => $produkt->id, 'article_no' => 'SON-111', 'price' => 100]);

        // Zweiter Lieferant meldet denselben Artikel über die Herstellernummer.
        $match = $this->dienst->resolve(new ProductIdentity(
            manufacturerArticleNo: 'VIT-250',
            brandId: $marke->id,
            supplierArticleNo: 'REX-999',
            distributorId: $d2->id,
        ));

        $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis);
        $this->assertSame($produkt->id, $match->product->id);

        DistributorPrice::create(['distributor_id' => $d2->id, 'product_id' => $match->product->id, 'article_no' => 'REX-999', 'price' => 98]);

        $this->assertSame(1, Product::count(), 'Ein products-Datensatz.');
        $this->assertSame(2, DistributorPrice::where('product_id', $produkt->id)->count(), 'Zwei distributor_prices.');
    }

    // ── Kante 6 ─────────────────────────────────────────────────────────────

    public function test_kante_06_zwei_lieferanten_gleiche_sku_verschiedene_artikel_bleiben_zwei_artikel(): void
    {
        $d1 = $this->lieferant('Sonepar');
        $d2 = $this->lieferant('Rexel');

        $p1 = Product::create(['product' => 'Kabel A']);
        $p2 = Product::create(['product' => 'Schalter B']);
        DistributorPrice::create(['distributor_id' => $d1->id, 'product_id' => $p1->id, 'article_no' => 'X100', 'price' => 10]);
        DistributorPrice::create(['distributor_id' => $d2->id, 'product_id' => $p2->id, 'article_no' => 'X100', 'price' => 20]);

        $match1 = $this->dienst->resolve(new ProductIdentity(supplierArticleNo: 'X100', distributorId: $d1->id));
        $match2 = $this->dienst->resolve(new ProductIdentity(supplierArticleNo: 'X100', distributorId: $d2->id));

        $this->assertSame($p1->id, $match1->product->id, 'Lieferant 1 + X100 muss Artikel 1 liefern (der Fall aus §2.1).');
        $this->assertSame($p2->id, $match2->product->id, 'Lieferant 2 + X100 muss Artikel 2 liefern — nicht first().');
        $this->assertNotSame($match1->product->id, $match2->product->id);
    }

    // ── Kante 7 ─────────────────────────────────────────────────────────────

    public function test_kante_07_artikel_ohne_hersteller_stufe_2_uebersprungen_nicht_abgebrochen(): void
    {
        $d1 = $this->lieferant('Sonepar');
        $produkt = Product::create(['product' => 'Ohne Hersteller', 'article_no' => 'ART-1']);
        DistributorPrice::create(['distributor_id' => $d1->id, 'product_id' => $produkt->id, 'article_no' => 'SON-55', 'price' => 5]);

        $match = $this->dienst->resolve(new ProductIdentity(
            manufacturerArticleNo: 'ANDERS',   // trifft nichts
            brandId: null,                     // kein Hersteller -> Stufe 2 wird übersprungen
            supplierArticleNo: 'SON-55',
            distributorId: $d1->id,
        ));

        $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis, 'Ohne Hersteller darf die Leiter nicht abbrechen.');
        $this->assertSame(3, $match->stufe);
        $this->assertSame($produkt->id, $match->product->id);
    }

    // ── Kante 8 ─────────────────────────────────────────────────────────────

    public function test_kante_08_not_filled_gilt_als_leer_und_wird_nicht_geschrieben(): void
    {
        // Bestand mit Sentinel darf NIE als Schlüssel dienen.
        Product::create(['product' => 'Altbestand', 'article_no' => 'Not filled']);

        $match = $this->dienst->resolve(new ProductIdentity(manufacturerArticleNo: 'Not filled'));
        $this->assertSame(IdentityMatch::NEU, $match->ergebnis, 'Sentinel wird nie als Schlüssel benutzt.');

        $neu = $this->dienst->createFrom(new ProductIdentity(manufacturerArticleNo: 'Not filled', name: 'Neuer Artikel'));
        $this->assertNull($neu->article_no, "'Not filled' wird nicht geschrieben — article_no bleibt leer.");
    }

    // ── Kante 9 ─────────────────────────────────────────────────────────────

    public function test_kante_09_fuehrendes_leerzeichen_trifft_denselben_artikel(): void
    {
        $marke = $this->herstellerMarke();
        $produkt = Product::create(['product' => 'Artikel', 'article_no' => '4711', 'brand_id' => $marke->id]);

        $match = $this->dienst->resolve(new ProductIdentity(manufacturerArticleNo: ' 4711', brandId: $marke->id));

        $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis, 'Die Kollation fängt führende Leerzeichen NICHT — der Code muss es.');
        $this->assertSame($produkt->id, $match->product->id);
    }

    // ── Kante 10 ────────────────────────────────────────────────────────────

    public function test_kante_10_nachlaufendes_leerzeichen_trifft_denselben_artikel(): void
    {
        $marke = $this->herstellerMarke();
        $produkt = Product::create(['product' => 'Artikel', 'article_no' => '4711', 'brand_id' => $marke->id]);

        $match = $this->dienst->resolve(new ProductIdentity(manufacturerArticleNo: '4711  ', brandId: $marke->id));

        $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis);
        $this->assertSame($produkt->id, $match->product->id);
    }

    // ── Kante 11 ────────────────────────────────────────────────────────────

    public function test_kante_11_identischer_name_verschiedene_nummern_wird_nie_zusammengefuehrt(): void
    {
        Product::create(['product' => 'Wärmepumpe 8 kW', 'article_no' => 'A-1']);
        Product::create(['product' => 'Wärmepumpe 8 kW', 'article_no' => 'A-2']);

        // Produktname allein: NIE (§4) — auch nicht als Vorschlag.
        $match = $this->dienst->resolve(new ProductIdentity(name: 'Wärmepumpe 8 kW'));

        $this->assertSame(IdentityMatch::NEU, $match->ergebnis, 'Name allein darf nie zuordnen oder vorschlagen.');
        $this->assertSame(2, Product::count());
    }

    // ── Kante 12 ────────────────────────────────────────────────────────────

    public function test_kante_12_dieselbe_datei_zweimal_importiert_ergibt_einen_artikel(): void
    {
        $marke = $this->herstellerMarke();
        $identitaet = new ProductIdentity(
            gtin: '12345670',
            manufacturerArticleNo: 'VIT-250',
            brandId: $marke->id,
            name: 'Wärmepumpe',
            channel: 'test:doppelimport',
        );

        // Lauf 1: kein Treffer -> anlegen.
        $ersterLauf = $this->dienst->resolve($identitaet);
        $this->assertSame(IdentityMatch::NEU, $ersterLauf->ergebnis);
        $produkt = $this->dienst->createFrom($identitaet);

        // Lauf 2: dieselben Daten -> automatischer Treffer, kein zweiter Artikel.
        $zweiterLauf = $this->dienst->resolve($identitaet);
        $this->assertSame(IdentityMatch::AUTOMATISCH, $zweiterLauf->ergebnis);
        $this->assertSame($produkt->id, $zweiterLauf->product->id);

        $this->assertSame(1, Product::where('article_no', 'VIT-250')->count());
    }

    // ── Kante 13 ────────────────────────────────────────────────────────────

    public function test_kante_13_umlaute_und_sz_im_herstellernamen_treffen_denselben_artikel(): void
    {
        $marke = $this->herstellerMarke('Größmüller & Söhne');
        $produkt = Product::create([
            'product' => 'Größenverstellbare Straßenleuchte',
            'model' => 'GS-100ß',
            'brand_id' => $marke->id,
            'article_no' => 'GRÖSSE-100',
        ]);

        // Stufe 2 mit Umlaut-Artikelnummer in anderer Schreibweise (mb_strtoupper).
        $match = $this->dienst->resolve(new ProductIdentity(manufacturerArticleNo: 'größe-100', brandId: $marke->id));
        $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis);
        $this->assertSame($produkt->id, $match->product->id);

        // Stufe 5 (Dreifach-Textschlüssel) mit Mehrfach-Leerzeichen im Umlaut-Text.
        $vorschlag = $this->dienst->resolve(new ProductIdentity(
            name: '  Größenverstellbare   Straßenleuchte ',
            model: 'GS-100ß',
            brandId: $marke->id,
        ));
        $this->assertSame(IdentityMatch::VORSCHLAG, $vorschlag->ergebnis);
        $this->assertSame($produkt->id, $vorschlag->product->id);

        $this->assertSame(1, Product::count());
    }

    // ── Kante 14 ────────────────────────────────────────────────────────────

    public function test_kante_14_brand_typ_brand_stufe_2_greift_nicht_leiter_faellt_auf_stufe_3(): void
    {
        // REGELFALL im Bestand (§14: brands manufacturer = 0/50).
        $marke = $this->handelsMarke();
        $d1 = $this->lieferant('Sonepar');

        $produkt = Product::create(['product' => 'Artikel', 'article_no' => '4711', 'brand_id' => $marke->id]);
        DistributorPrice::create(['distributor_id' => $d1->id, 'product_id' => $produkt->id, 'article_no' => 'SON-77', 'price' => 7]);

        $match = $this->dienst->resolve(new ProductIdentity(
            manufacturerArticleNo: '4711',
            brandId: $marke->id,           // type='brand' -> Stufe 2 greift nicht
            supplierArticleNo: 'SON-77',
            distributorId: $d1->id,
        ));

        $this->assertSame(IdentityMatch::AUTOMATISCH, $match->ergebnis);
        $this->assertSame(3, $match->stufe, 'brands.type != manufacturer: Stufe 2 greift nicht, Stufe 3 trägt.');
        $this->assertSame($produkt->id, $match->product->id);
    }

    // ── Kante 15 ────────────────────────────────────────────────────────────

    public function test_kante_15_schalter_ist_per_default_aus(): void
    {
        // Der Rückweg (§11): Default false, ohne env-Eintrag. Die Byte-Gleichheit
        // der neun Pfade bei aktiv=false belegt der K-04-Test auf Pfad-Ebene.
        $this->assertFalse(
            (bool) config('produkt.identitaet.aktiv'),
            'produkt.identitaet.aktiv muss per Default false sein — das ist der Rückweg ohne Datenmigration.'
        );
    }

    // ── Kante 16 ────────────────────────────────────────────────────────────

    public function test_kante_16_stufe_5_treffer_erzeugt_vorschlagszeile_ohne_products_schreibzugriff(): void
    {
        $produkt = Product::create(['product' => 'Bestandsartikel', 'article_no' => '9999']);
        $standVorher = $produkt->fresh()->toArray();
        $anzahlVorher = Product::count();

        $identitaet = new ProductIdentity(manufacturerArticleNo: '9999', channel: 'test:kante16');
        $match = $this->dienst->resolve($identitaet);

        $this->assertSame(IdentityMatch::VORSCHLAG, $match->ergebnis);
        $this->assertSame(5, $match->stufe);

        $this->dienst->vorschlagSpeichern($identitaet, $match);

        $this->assertDatabaseHas('product_identity_suggestions', [
            'product_id' => $produkt->id,
            'channel' => 'test:kante16',
            'status' => 'offen',
        ]);
        $this->assertSame($anzahlVorher, Product::count(), 'Kein neuer Artikel durch einen Vorschlag.');
        $this->assertSame($standVorher, $produkt->fresh()->toArray(), 'Kein Schreibzugriff auf products.');
    }

    // ── Zusatz: der Abbruch nach oben aus §4 (Beispielfall der Spezifikation) ──

    public function test_abbruch_nach_oben_stufe_3_treffer_mit_widersprechender_gtin_ist_konflikt(): void
    {
        $d1 = $this->lieferant('Sonepar');

        $produkt = $this->dienst->createFrom(new ProductIdentity(gtin: '12345670', name: 'Artikel A'));
        DistributorPrice::create(['distributor_id' => $d1->id, 'product_id' => $produkt->id, 'article_no' => '4711', 'price' => 9]);

        // Stufe 3 fände Artikel A — aber die eingehende GTIN widerspricht (eine andere, gültige Nummer).
        $match = $this->dienst->resolve(new ProductIdentity(
            gtin: '11111115',              // gültige EAN-8, andere Nummer
            supplierArticleNo: '4711',
            distributorId: $d1->id,
        ));

        $this->assertSame(IdentityMatch::KONFLIKT, $match->ergebnis, 'Zwei verschiedene GTIN = zwei verschiedene Artikel: Treffer verworfen.');
        $this->assertNull($match->product);
    }
}
