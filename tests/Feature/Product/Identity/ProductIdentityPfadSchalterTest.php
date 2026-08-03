<?php

namespace Tests\Feature\Product\Identity;

use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Product;
use App\Models\SupplierConnection;
use App\Services\Suppliers\SupplierProductImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AUF-P1-S4 · Pfad-Ebene: K-02 (Doppelimport => EIN Artikel, aktiv=true) und
 * K-04 (Schalter trägt: aktiv=false erzeugt die Zeilen des Alt-Codes byte-gleich,
 * inklusive des Sentinels 'Not filled' — Kante 15 auf Pfad 1 gemessen).
 */
class ProductIdentityPfadSchalterTest extends TestCase
{
    use RefreshDatabase;

    private function verbindung(): SupplierConnection
    {
        $distributor = Distributor::create(['name' => 'Testlieferant', 'short_name' => 'TEST', 'status' => 1]);

        return SupplierConnection::create([
            'distributor_id' => $distributor->id,
            'name' => 'Testlieferant',
            'supplier_key' => 'testlieferant',
            'connector_type' => 'csv',
            'auth_type' => 'none',
            'is_active' => true,
        ]);
    }

    /** Dieselben Import-Daten wie aus einer Lieferantendatei — zweimal verwendbar. */
    private function dateiZeile(): array
    {
        return [
            'products' => [
                'article_no' => 'VIT-250-A',
                'ean' => '4012345678901',
                'product' => 'Vitocal 250-A',
                'model' => 'Typ 251.A08',
                'vat_percent' => 19,
            ],
            'brands' => ['name' => 'Viessmann'],
            'distributor_prices' => [
                'article_no' => 'TL-4711',
                'price' => '4.999,00',
                'purchase_price' => '3.999,00',
            ],
        ];
    }

    // ── K-02: Doppelimport erzeugt EINEN Artikel (aktiv=true) ───────────────

    public function test_k02_dieselbe_datei_zweimal_durch_pfad_1_erzeugt_einen_artikel(): void
    {
        config(['produkt.identitaet.aktiv' => true]);

        $verbindung = $this->verbindung();
        $dienst = new SupplierProductImportService();

        $erster = $dienst->importMappedItem($verbindung, $this->dateiZeile());
        $zweiter = $dienst->importMappedItem($verbindung, $this->dateiZeile());

        $this->assertSame($erster->id, $zweiter->id, 'Der zweite Lauf muss denselben Artikel treffen.');
        $this->assertSame(
            1,
            Product::where('article_no', 'VIT-250-A')->count(),
            'SELECT COUNT(*) auf die article_no muss 1 liefern (K-02).'
        );
        $this->assertSame('04012345678901', $erster->fresh()->gtin_normalized, 'GTIN normalisiert auf 14 Stellen.');
    }

    // ── K-04 / Kante 15: Schalter aus => Alt-Verhalten byte-gleich ──────────

    public function test_k04_aktiv_false_erzeugt_die_zeile_des_alt_codes_inklusive_sentinel(): void
    {
        $this->assertFalse((bool) config('produkt.identitaet.aktiv'), 'Default muss false sein.');

        $verbindung = $this->verbindung();
        $dienst = new SupplierProductImportService();

        // Zeile OHNE Herstellernummer: der Alt-Code schreibt hier den Sentinel
        // 'Not filled' — genau das muss bei aktiv=false weiterhin passieren
        // (byte-gleich zum Verhalten vor der Änderung, so falsch es fachlich ist).
        $zeile = [
            'products' => [
                'product' => 'Unbekanntes Kabel',
                'vat_percent' => 19,
            ],
            'brands' => [],
            'distributor_prices' => [
                'article_no' => 'TL-9999',
                'price' => '10,00',
            ],
        ];

        $produkt = $dienst->importMappedItem($verbindung, $zeile);
        $roh = DB::table('products')->where('id', $produkt->id)->first();

        // Die Legacy-Signatur des Alt-Codes, Feld für Feld:
        $this->assertSame('Not filled', $roh->article_no, 'Alt-Code schreibt den Sentinel — aktiv=false muss das erhalten.');
        $this->assertSame('TL-9999', $roh->sku);
        $this->assertSame('Unbekanntes Kabel', $roh->product);

        // Die neuen Identitätsspalten bleiben bei aktiv=false unberührt:
        $this->assertNull($roh->gtin_normalized);
        $this->assertNull($roh->identity_rung);
        $this->assertNull($roh->completeness_level);

        // Keine Vorschläge, keine Leiter-Nebenwirkungen:
        $this->assertSame(0, DB::table('product_identity_suggestions')->count());

        // Doppellauf bei aktiv=false verhält sich wie der Alt-Code (Treffer über
        // die alte 3-Stufen-Suche, kein zweiter Artikel):
        $zweiter = $dienst->importMappedItem($verbindung, $zeile);
        $this->assertSame($produkt->id, $zweiter->id);
        $this->assertSame(1, Product::count());
        $this->assertSame(1, DistributorPrice::count());
    }
}
