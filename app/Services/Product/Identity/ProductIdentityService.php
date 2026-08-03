<?php

namespace App\Services\Product\Identity;

use App\Models\Brand;
use App\Models\DistributorPrice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Die eine Identitätsleiter für Produkte (AUF-P1-S4).
 *
 * Spezifikation (bindend): docs/product-data/11-identitaetsspezifikation-paket1-schritt3-4.md
 *   §4 Leiter · §5 Normalisierung · §6 Schnittstelle · §9 Kantenfälle.
 *
 * Diese Datei ist die EINZIGE Ausnahme des Verriegelungstests — nur hier darf
 * `Product` angelegt werden. createFrom() ist der identitätsbewusste Weg;
 * createLegacy()/newLegacy() sind reine Durchreichen für die aktiv=false-Zweige
 * der neun umgestellten Pfade (byte-gleiches Altverhalten, K-04), damit der
 * Quelltext-Grep (K-01) und die Byte-Gleichheit gleichzeitig erfüllbar sind.
 */
final class ProductIdentityService
{
    /**
     * Sentinel-Werte gelten als leer und werden NIE als Schlüssel benutzt (§5).
     * Vergleich nach Normalisierung (GROSS): 'Not filled', '-', 'n/a', 'N/A', '0', '', null.
     */
    private const SENTINELS = ['NOT FILLED', '-', 'N/A', '0', ''];

    public function normalize(ProductIdentity $in): ProductIdentity
    {
        return new ProductIdentity(
            gtin:                  $this->normalizeGtin($in->gtin),
            manufacturerArticleNo: $this->normalizeArticleNo($in->manufacturerArticleNo),
            brandId:               $in->brandId,
            supplierArticleNo:     $this->normalizeArticleNo($in->supplierArticleNo),
            distributorId:         $in->distributorId,
            model:                 $this->normalizeText($in->model),
            name:                  $this->normalizeText($in->name),
            channel:               $in->channel,
        );
    }

    /**
     * Führt die Leiter aus (§4). Erste Übereinstimmung gewinnt, kein Treffer heißt NEU.
     * Ein Treffer wird verworfen, wenn eine höhere Stufe widerspricht (KONFLIKT).
     * Schreibt NICHTS.
     */
    public function resolve(ProductIdentity $in): IdentityMatch
    {
        $id = $this->normalize($in);

        // ── Stufe 1: gtin_normalized (14-stellig, Prüfziffer gültig) ── automatisch
        if ($id->gtin !== null) {
            $product = Product::query()->where('gtin_normalized', $id->gtin)->first();

            if ($product) {
                if ($grund = $this->widerspruch($product, $id, 1)) {
                    return new IdentityMatch(IdentityMatch::KONFLIKT, null, 1, $grund);
                }

                return new IdentityMatch(
                    IdentityMatch::AUTOMATISCH, $product, 1,
                    'Stufe 1: GTIN ' . $id->gtin
                );
            }
        }

        // ── Stufe 2: (brand_id [type=manufacturer], article_no) ── automatisch
        // Ohne Hersteller wird die Stufe übersprungen, nicht abgebrochen (Kante 7).
        // brands.type !== 'manufacturer' => Stufe greift nicht (Kante 14, Regelfall im Bestand §14).
        if ($id->brandId !== null && $id->manufacturerArticleNo !== null) {
            $brand = Brand::query()->find($id->brandId);

            if ($brand && $brand->type === 'manufacturer') {
                $product = Product::query()
                    ->where('brand_id', $id->brandId)
                    ->where('article_no', $id->manufacturerArticleNo)
                    ->first();

                if ($product) {
                    if ($grund = $this->widerspruch($product, $id, 2)) {
                        return new IdentityMatch(IdentityMatch::KONFLIKT, null, 2, $grund);
                    }

                    return new IdentityMatch(
                        IdentityMatch::AUTOMATISCH, $product, 2,
                        'Stufe 2: Hersteller ' . $id->brandId . ' + Artikelnummer ' . $id->manufacturerArticleNo
                    );
                }
            }
        }

        // ── Stufe 3: (distributor_id, sku) ── automatisch
        // Auflösung über distributor_prices — das Vorbild aus SupplierProductImportService (§8 Zeile 1),
        // MIT Lieferantenfilter (behebt §2.1: Preis am falschen Artikel).
        if ($id->distributorId !== null && $id->supplierArticleNo !== null) {
            $price = DistributorPrice::query()
                ->where('distributor_id', $id->distributorId)
                ->where('article_no', $id->supplierArticleNo)
                ->with('product')
                ->first();

            if ($price && $price->product) {
                $product = $price->product;

                if ($grund = $this->widerspruch($product, $id, 3)) {
                    return new IdentityMatch(IdentityMatch::KONFLIKT, null, 3, $grund);
                }

                return new IdentityMatch(
                    IdentityMatch::AUTOMATISCH, $product, 3,
                    'Stufe 3: Lieferant ' . $id->distributorId . ' + Artikelnummer ' . $id->supplierArticleNo
                );
            }
        }

        // ── Stufe 4: (branchennummer, land, branche) — OMD branch[] ──
        // Platzhalter: die Datenfelder existieren heute nicht (§4). Gilt als „kein Treffer",
        // damit Paket 6 die Stufe einhängen kann, ohne die Reihenfolge zu ändern.

        // ── Stufe 5: normalisierter Textvergleich ── NUR VORSCHLAG (E1)
        // a) article_no allein (ohne Herstellerbezug)
        if ($id->manufacturerArticleNo !== null) {
            $product = Product::query()->where('article_no', $id->manufacturerArticleNo)->first();

            if ($product) {
                if ($grund = $this->widerspruch($product, $id, 5)) {
                    return new IdentityMatch(IdentityMatch::KONFLIKT, null, 5, $grund);
                }

                return new IdentityMatch(
                    IdentityMatch::VORSCHLAG, $product, 5,
                    'Stufe 5: Textvergleich article_no ' . $id->manufacturerArticleNo
                );
            }
        }

        // b) (product, model, brand_id) — der Dreifach-Textschlüssel des manuellen Wegs (§2.2)
        if ($id->name !== null) {
            $query = Product::query()->where('product', $id->name);

            $id->model === null
                ? $query->whereNull('model')
                : $query->where('model', $id->model);

            $id->brandId === null
                ? $query->whereNull('brand_id')
                : $query->where('brand_id', $id->brandId);

            $product = $query->first();

            if ($product) {
                if ($grund = $this->widerspruch($product, $id, 5)) {
                    return new IdentityMatch(IdentityMatch::KONFLIKT, null, 5, $grund);
                }

                return new IdentityMatch(
                    IdentityMatch::VORSCHLAG, $product, 5,
                    'Stufe 5: Textvergleich (product, model, brand_id)'
                );
            }
        }

        // Produktname allein: NIE (§4).

        return new IdentityMatch(IdentityMatch::NEU, null, null, 'kein Treffer auf Stufe 1-5');
    }

    /**
     * Die einzige Stelle im System, die Product identitätsbewusst anlegen darf.
     * Identitätsfelder kommen normalisiert aus der Identity (Sentinel => null, Kante 8);
     * beschreibende Felder aus $weitereFelder.
     */
    public function createFrom(ProductIdentity $in, array $weitereFelder = []): Product
    {
        $id = $this->normalize($in);

        $attrs = $weitereFelder;

        // Identitätsfelder führen — auch null (ein Sentinel wird NICHT geschrieben).
        $attrs['article_no'] = $id->manufacturerArticleNo;
        $attrs['sku']        = $id->supplierArticleNo ?? ($weitereFelder['sku'] ?? null);
        $attrs['ean']        = $weitereFelder['ean'] ?? $in->gtin;
        $attrs['brand_id']   = $id->brandId ?? ($weitereFelder['brand_id'] ?? null);

        // Beschreibende Felder: der Pfad weiß mehr (Fallback-Ketten) — er führt.
        $attrs['model']   = $weitereFelder['model']   ?? $id->model;
        $attrs['product'] = $weitereFelder['product'] ?? $id->name;

        $product = new Product();
        $product->fill($attrs);
        $product->gtin_normalized = $id->gtin;
        $product->identity_rung = 'neu';
        $product->save();

        return $product;
    }

    /**
     * Stufe-5-Treffer festhalten: Zeile in product_identity_suggestions,
     * KEIN Schreibzugriff auf products (Kante 16). Ein Vorschlag, den niemand
     * sehen kann, wäre eine Verwerfung mit Extraschritten (§7).
     */
    public function vorschlagSpeichern(ProductIdentity $in, IdentityMatch $match): int
    {
        if ($match->ergebnis !== IdentityMatch::VORSCHLAG || $match->product === null) {
            throw new \InvalidArgumentException('vorschlagSpeichern erwartet einen Stufe-5-VORSCHLAG mit Produkt.');
        }

        return (int) DB::table('product_identity_suggestions')->insertGetId([
            'product_id' => $match->product->id,
            'incoming'   => json_encode([
                'gtin'                  => $in->gtin,
                'manufacturerArticleNo' => $in->manufacturerArticleNo,
                'brandId'               => $in->brandId,
                'supplierArticleNo'     => $in->supplierArticleNo,
                'distributorId'         => $in->distributorId,
                'model'                 => $in->model,
                'name'                  => $in->name,
                'channel'               => $in->channel,
            ], JSON_UNESCAPED_UNICODE),
            'channel'    => mb_substr($in->channel, 0, 64),
            'reason'     => mb_substr($match->begruendung, 0, 255),
            'status'     => 'offen',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * NUR für die aktiv=false-Zweige der neun umgestellten Pfade (K-04):
     * reines Durchreichen an Product::create, KEINE Normalisierung, KEINE Leiter —
     * byte-gleiches Altverhalten. Neue Aufrufer haben hier nichts verloren;
     * der identitätsbewusste Weg ist createFrom().
     */
    public function createLegacy(array $attributes): Product
    {
        return Product::create($attributes);
    }

    /**
     * NUR für die aktiv=false-Zweige (K-04): Ersatz für `new Product()` an den
     * Alt-Stellen, damit der Verriegelungs-Grep (K-01) greifen kann. Ungespeichert.
     */
    public function newLegacy(): Product
    {
        return new Product();
    }

    // ────────────────────────────────────────────────────────────────────────
    //  Normalisierung (§5)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * GTIN: TRIM · Nicht-Ziffern raus · links auf 14 Stellen füllen ·
     * GS1-Prüfziffer validieren · ungültig => null (gilt als nicht vorhanden).
     * Führende Nullen sind bei GTIN bedeutungslos (EAN-8/13 und GTIN-14 sind
     * dasselbe Nummernsystem) — deshalb auffüllen.
     */
    public function normalizeGtin(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', trim($raw));

        if ($digits === '' || strlen($digits) > 14) {
            return null;
        }

        // Sentinel '0' u. ä. — eine GTIN aus lauter Nullen ist keine.
        if ((int) $digits === 0) {
            return null;
        }

        $gtin = str_pad($digits, 14, '0', STR_PAD_LEFT);

        return $this->gs1PruefzifferGueltig($gtin) ? $gtin : null;
    }

    /**
     * Artikelnummern (article_no, sku): TRIM · Mehrfach-Leerzeichen zu einem ·
     * GROSSBUCHSTABEN · führende Nullen BLEIBEN ('0815' und '815' können zwei
     * Artikel sein — wer hier normalisiert, verschmilzt Artikel).
     * Sentinel-Werte => null.
     */
    public function normalizeArticleNo(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $value = mb_strtoupper(preg_replace('/\s+/u', ' ', trim($raw)));

        return in_array($value, self::SENTINELS, true) ? null : $value;
    }

    /**
     * Herstellername / Modell / Produktname: TRIM · Mehrfach-Leerzeichen zu einem ·
     * Schreibweise erhalten (Vergleich case-insensitiv über die Kollation).
     */
    public function normalizeText(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim($raw));

        return $value === '' ? null : $value;
    }

    private function gs1PruefzifferGueltig(string $gtin14): bool
    {
        $digits = str_split($gtin14);
        $check = (int) array_pop($digits);

        $sum = 0;
        foreach (array_reverse($digits) as $i => $digit) {
            $sum += ((int) $digit) * ($i % 2 === 0 ? 3 : 1);
        }

        return (10 - ($sum % 10)) % 10 === $check;
    }

    // ────────────────────────────────────────────────────────────────────────
    //  Abbruch nach oben (§4): Treffer verworfen, wenn eine höhere Stufe
    //  widerspricht. Geprüft nur, wenn BEIDE Seiten den Schlüssel führen —
    //  ein leerer Wert widerspricht nicht.
    // ────────────────────────────────────────────────────────────────────────

    private function widerspruch(Product $product, ProductIdentity $id, int $stufe): ?string
    {
        // GTIN (Stufe 1) schlägt jede tiefere Stufe.
        if ($stufe > 1 && $id->gtin !== null && $product->gtin_normalized !== null
            && $id->gtin !== $product->gtin_normalized) {
            return 'GTIN widerspricht: eingehend ' . $id->gtin . ', Artikel #' . $product->id
                . ' trägt ' . $product->gtin_normalized;
        }

        // Kante 4: gleiche GTIN, aber die Marke widerspricht (beide Seiten gesetzt).
        if ($stufe === 1 && $id->brandId !== null && $product->brand_id !== null
            && (int) $product->brand_id !== $id->brandId) {
            return 'Marke widerspricht bei gleicher GTIN: eingehend brand_id ' . $id->brandId
                . ', Artikel #' . $product->id . ' trägt brand_id ' . $product->brand_id;
        }

        // Stufe 2 (Hersteller + Artikelnummer) schlägt Stufe 3/5 — nur wenn das
        // Schlüsselpaar auf BEIDEN Seiten vollständig ist.
        if ($stufe > 2 && $id->brandId !== null && $id->manufacturerArticleNo !== null) {
            $produktArtikelNo = $this->normalizeArticleNo($product->article_no);

            if ($product->brand_id !== null && $produktArtikelNo !== null) {
                if ((int) $product->brand_id !== $id->brandId
                    || $produktArtikelNo !== $id->manufacturerArticleNo) {
                    return 'Hersteller+Artikelnummer widersprechen: eingehend ('
                        . $id->brandId . ', ' . $id->manufacturerArticleNo . '), Artikel #'
                        . $product->id . ' trägt (' . $product->brand_id . ', ' . $produktArtikelNo . ')';
                }
            }
        }

        return null;
    }
}
