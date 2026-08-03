<?php

namespace Tests\Feature\Product\Identity;

use App\Services\Product\Identity\ProductIdentity;
use App\Services\Product\Identity\ProductIdentityService;
use Tests\TestCase;

/**
 * AUF-P1-S4 · Normalisierung nach Spez 11-…md §5 — beim Schreiben UND beim Suchen.
 * Reine Wertlogik, kein DB-Zugriff nötig.
 */
class ProductIdentityNormalisierungTest extends TestCase
{
    private ProductIdentityService $dienst;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dienst = new ProductIdentityService();
    }

    // ── GTIN ────────────────────────────────────────────────────────────────

    public function test_gtin_wird_in_allen_stellenzahlen_auf_14_stellen_gefuellt(): void
    {
        // 12345670 = gültige EAN-8; 4012345678901 = gültige EAN-13.
        $this->assertSame('00000012345670', $this->dienst->normalizeGtin('12345670'));
        $this->assertSame('00000012345670', $this->dienst->normalizeGtin('000012345670'));
        $this->assertSame('00000012345670', $this->dienst->normalizeGtin('0000012345670'));
        $this->assertSame('00000012345670', $this->dienst->normalizeGtin('00000012345670'));
        $this->assertSame('04012345678901', $this->dienst->normalizeGtin('4012345678901'));
    }

    public function test_gtin_nicht_ziffern_werden_entfernt(): void
    {
        $this->assertSame('04012345678901', $this->dienst->normalizeGtin(' 4-012345-678901 '));
    }

    public function test_gtin_mit_falscher_pruefziffer_gilt_als_nicht_vorhanden(): void
    {
        $this->assertNull($this->dienst->normalizeGtin('12345671'));
        $this->assertNull($this->dienst->normalizeGtin('4012345678902'));
    }

    public function test_gtin_leer_null_oder_nur_nullen_gilt_als_nicht_vorhanden(): void
    {
        $this->assertNull($this->dienst->normalizeGtin(null));
        $this->assertNull($this->dienst->normalizeGtin(''));
        $this->assertNull($this->dienst->normalizeGtin('0'));
        $this->assertNull($this->dienst->normalizeGtin('00000000000000'));
        $this->assertNull($this->dienst->normalizeGtin('123456789012345'), 'Mehr als 14 Ziffern ist keine GTIN.');
    }

    // ── Artikelnummern (article_no, sku) ────────────────────────────────────

    public function test_artikelnummer_trim_mehrfachleerzeichen_und_grossbuchstaben(): void
    {
        $this->assertSame('AB 12', $this->dienst->normalizeArticleNo('  ab   12 '));
        $this->assertSame('VIT-250', $this->dienst->normalizeArticleNo('vit-250'));
    }

    public function test_artikelnummer_fuehrende_nullen_bleiben(): void
    {
        // '0815' und '815' können zwei Artikel sein — wer hier normalisiert, verschmilzt Artikel.
        $this->assertSame('0815', $this->dienst->normalizeArticleNo('0815'));
        $this->assertNotSame(
            $this->dienst->normalizeArticleNo('0815'),
            $this->dienst->normalizeArticleNo('815'),
        );
    }

    public function test_sentinel_werte_gelten_als_leer(): void
    {
        foreach (['Not filled', '-', 'n/a', 'N/A', '0', '', null] as $sentinel) {
            $this->assertNull(
                $this->dienst->normalizeArticleNo($sentinel),
                'Sentinel ' . var_export($sentinel, true) . ' muss als leer gelten (§5).'
            );
        }
    }

    // ── Texte (Herstellername, Modell, Produktname) ─────────────────────────

    public function test_text_trim_und_mehrfachleerzeichen_schreibweise_bleibt_erhalten(): void
    {
        $this->assertSame('Vitocal 250-A', $this->dienst->normalizeText('  Vitocal   250-A '));
        $this->assertSame('größe ß Straße', $this->dienst->normalizeText('größe  ß   Straße'), 'Schreibweise (klein/Umlaut/ß) bleibt erhalten.');
        $this->assertNull($this->dienst->normalizeText('   '));
        $this->assertNull($this->dienst->normalizeText(null));
    }

    // ── normalize() als Ganzes ──────────────────────────────────────────────

    public function test_normalize_liefert_neue_identitaet_mit_allen_regeln(): void
    {
        $roh = new ProductIdentity(
            gtin: ' 12345670',
            manufacturerArticleNo: ' vit  250 ',
            brandId: 7,
            supplierArticleNo: 'Not filled',
            distributorId: 3,
            model: '  GS  100 ',
            name: ' Wärmepumpe   8 kW ',
            channel: 'test:norm',
        );

        $normalisiert = $this->dienst->normalize($roh);

        $this->assertSame('00000012345670', $normalisiert->gtin);
        $this->assertSame('VIT 250', $normalisiert->manufacturerArticleNo);
        $this->assertSame(7, $normalisiert->brandId);
        $this->assertNull($normalisiert->supplierArticleNo, 'Sentinel als sku muss leer werden.');
        $this->assertSame(3, $normalisiert->distributorId);
        $this->assertSame('GS 100', $normalisiert->model);
        $this->assertSame('Wärmepumpe 8 kW', $normalisiert->name);
        $this->assertSame('test:norm', $normalisiert->channel);
    }
}
