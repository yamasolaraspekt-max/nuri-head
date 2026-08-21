<?php

namespace App\Http\Controllers\Product\IDS\gconline;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\ImportedIdsItem;
use App\Events\IdsItemsImported;
use Illuminate\Support\Facades\Log; 
use App\Models\Product;
use App\Models\Distributor;
use App\Models\DistributorPrice; 
use Illuminate\Support\Str; 
use App\Models\ArticleGroup;
use App\Models\SubArticleGroup;
use App\Models\Brand;
use App\Models\Measure;
use App\Services\Product\Identity\IdentityMatch;
use App\Services\Product\Identity\ProductIdentity;
use App\Services\Product\Identity\ProductIdentityService;

class IdsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['promoteToProduct']);
    }

    // SERVER CALLBACK → must remain PUBLIC
    public function callback(Request $request)
    {
        Log::info("🟦 IDS CALLBACK HIT", ['query' => $request->query()]);

        $xml = $request->input('warenkorb');
        Log::info("🟦 IDS XML RECEIVED", ['xml' => $xml]);

        if (!$xml) {
            Log::error("❌ No XML received in callback");
            return response("NO XML", 400);
        }

        $data = $this->parseIdsXml($xml);

        if (!$data || empty($data['items'])) {
            Log::error("❌ Parsed XML contains no items");
            return response("NO ITEMS", 400);
        }

        // build batch id
        $batchId = 'IDS-' . str_replace(['-', ':'], '', ($data['date'] ?? '') . ($data['time'] ?? ''));

        // Z2-W0-11 Teil A (21.08.2026): der Importeur kommt aus der SITZUNG, nicht aus der Query.
        //
        // Vorher stand hier `$request->query('uid')`. Wer die Adresse kannte, konnte den Import
        // einem beliebigen Kollegen zuschreiben — `?uid=<fremde-id>` genuegte, und im
        // Produktstamm stand danach dessen Name an einer Anlage, die er nie ausgeloest hat.
        //
        // Der Wert aus der Sitzung ist verfuegbar und nicht bloss ein Ersatz: die Route liegt
        // hinter `web` + `Authenticate` (`routes/web.php:511`, per route:list belegt) und der
        // Konstruktor setzt zusaetzlich `auth`. Der Kommentar "must remain PUBLIC" ueber
        // callback() beschreibt einen Zustand, den es hier nicht gibt — der Rueckweg laeuft
        // durch den Browser des angemeldeten Nutzers, samt Sitzungskeks.
        //
        // Eine `uid` in der Query wird bewusst NICHT mehr gelesen, auch nicht als Rueckfall.
        // Ein Rueckfall waere genau das Loch: wer ihn ausloest, bestimmt wieder frei.
        $userId = auth()->id();
        $auto   = $request->query('auto') == '1';

        Log::info("🟦 IDS meta", ['batch_id' => $batchId, 'user_id' => $userId, 'auto' => $auto]);

        foreach ($data['items'] as $i) {
            $item = ImportedIdsItem::create([
                'batch_id'    => $batchId,
                'user_id'     => $userId,
                'article_no'  => $i['article_no'],
                'short_text'  => $i['short_text'],
                'qty'         => $i['qty'],
                'unit'        => $i['unit'],
                'offer_price' => $i['offer_price'],
                'net_price'   => $i['net_price'],
                'vat'         => $i['vat'],
            ]);

            if ($auto) {
                $this->autoPromoteItem($item);
            }
        }

        event(new IdsItemsImported($batchId));

        return response('OK', 200);
    }

    /**
     * Automatically create/update Product, Distributor and DistributorPrice
     * for one imported IDS item.
     */
    protected function autoPromoteItem(ImportedIdsItem $item): void
    {
        if (config('produkt.identitaet.aktiv')) {
            // AUF-P1-S4 §8 Zeile 3: resolve() mit supplierArticleNo — die GC-Online-
            // Nummer ist eine Lieferantennummer, KEINE Herstellernummer. Der Pfad
            // entfällt mit Schritt 5; bis dahin läuft er über die Leiter.
            $distributor = Distributor::firstOrCreate(
                ['name' => 'GC Online'],
                ['status' => 'Published']
            );

            $dienst = app(ProductIdentityService::class);

            $identitaet = new ProductIdentity(
                gtin: $item->ean,
                supplierArticleNo: $item->article_no,
                distributorId: $distributor->id,
                name: $item->short_text ?: $item->article_no,
                channel: 'ids:gconline',
            );

            $match = $dienst->resolve($identitaet);

            if ($match->ergebnis === IdentityMatch::KONFLIKT) {
                Log::warning('IDS autoPromote: Identitätskonflikt — kein Import', [
                    'item_id' => $item->id,
                    'grund' => $match->begruendung,
                ]);
                return;
            }

            if ($match->ergebnis === IdentityMatch::VORSCHLAG) {
                $dienst->vorschlagSpeichern($identitaet, $match);
                Log::info('IDS autoPromote: Stufe-5-Vorschlag angelegt — kein automatischer Import', [
                    'item_id' => $item->id,
                    'grund' => $match->begruendung,
                ]);
                return;
            }

            $product = $match->product ?? $dienst->createFrom($identitaet, [
                'product'           => $item->short_text ?: $item->article_no,
                'short_description' => $item->long_text,
                'status'            => 'Published',
            ]);
        } else {
            // Alt-Verhalten, byte-gleich (Kante 15): firstOrCreate auf article_no.
            $product = Product::where('article_no', $item->article_no)->first()
                ?: app(ProductIdentityService::class)->createLegacy([
                    'article_no'        => $item->article_no,
                    'ean'               => $item->ean,
                    'product'           => $item->short_text ?: $item->article_no,
                    'short_description' => $item->long_text,
                    'status'            => 'Published',
                ]);

            // 2) Distributor "GC Online"
            $distributor = Distributor::firstOrCreate(
                ['name' => 'GC Online'],
                ['status' => 'Published']
            );
        }

        // 3) Distributor price
        DistributorPrice::updateOrCreate(
            [
                'distributor_id' => $distributor->id,
                'product_id'     => $product->id,
            ],
            [
                'article_no'      => $item->article_no,
                'price'           => $item->offer_price,
                'purchase_price'  => $item->net_price,
                'discount_price'  => null,
                'discount_percent'=> null,
                'price_date'      => now()->toDateString(),
                'availability'    => null,
                'status'          => 'Published',
            ]
        );
    }


    // Return parsed items to browser
    public function results($batchId)
    {
        return ImportedIdsItem::where('batch_id', $batchId)->get();
    }


    private function parseIdsXml($xmlString)
    {
        $xml = simplexml_load_string($xmlString);
        if (!$xml) return null;

        // Namespace fix
        $ns = $xml->getNamespaces(true);
        if (isset($ns[''])) {
            $xml = $xml->children($ns['']);
        }

        $info  = $xml->WarenkorbInfo ?? null;
        $order = $xml->Order ?? null;

        if (!$order) return null;

        $result = [
            'date'   => (string)($info->Date ?? ''),
            'time'   => (string)($info->Time ?? ''),
            'items'  => [],
        ];

        foreach ($order->OrderItem as $item) {
            $result['items'][] = [
                'article_no'  => (string)($item->ArtNo ?? ''),
                'short_text'  => (string)($item->Kurztext ?? ''),
                'qty'         => (float)($item->Qty ?? 0),
                'unit'        => (string)($item->QU ?? ''),
                'offer_price' => (float)($item->OfferPrice ?? 0),
                'net_price'   => (float)($item->NetPrice ?? 0),
                'vat'         => (float)($item->VAT ?? 0),
            ];
        }

        return $result;
    }

    // app/Http/Controllers/IdsController.php

   public function localSearch(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $items = \App\Models\ImportedIdsItem::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('article_no', 'like', "%{$q}%")
                        ->orWhere('short_text', 'like', "%{$q}%")
                        ->orWhere('long_text', 'like', "%{$q}%");
                });
            })
            ->orderByRaw('CASE WHEN product_id IS NULL THEN 0 ELSE 1 END ASC')
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        return response()->json($items);
    }

   public function promoteToProduct(Request $request, ImportedIdsItem $item)
    {
        $validated = $request->validate([
            'brand_id'             => ['required', 'exists:brands,id'],
            'article_group_id'     => ['required', 'exists:article_groups,id'],
            'sub_article_group_id' => ['nullable', 'exists:sub_article_groups,id'],
            'product_name'         => ['nullable', 'string', 'max:255'],
            'measure_unit'         => ['nullable', 'string', 'max:50'], // text: "ST", "m", ...
        ]);

        $distributor = Distributor::firstOrCreate(
            ['name' => 'GC Online'],
            ['status' => 'Published']
        );

        $fallbackName = $item->short_text ?: $item->long_text ?: 'Unbenanntes Produkt';
        $productName  = $validated['product_name'] ?? $fallbackName;

        $articleGroup    = ArticleGroup::find($validated['article_group_id']);
        $subArticleGroup = $validated['sub_article_group_id']
            ? SubArticleGroup::find($validated['sub_article_group_id'])
            : null;

        // ----------------- MEASURE HANDLING -----------------
        // Prefer user input from form, fallback to IDS unit
        $measureText = $validated['measure_unit'] ?? $item->unit ?? null;
        $measureId   = null;

        if (!empty($measureText)) {
            // check if exists, otherwise create
            $measure = Measure::firstOrCreate(['measure' => $measureText]);
            $measureId = $measure->id;
        }
        // ----------------------------------------------------

        $neueFelder = [
            'ean'               => $item->ean ?? null,
            'product'           => \Illuminate\Support\Str::limit($productName, 255),
            'model'             => null,
            'category'          => 'Produkt',
            'roof_type'         => 'none',
            'color'             => null,

            // price_unit: free text for display
            'price_unit'        => $measureText,

            'package_unit'      => null,
            'short_description' => $item->long_text ?? $item->short_text,
            'status'            => 'Published',
            'brand_id'          => $validated['brand_id'],

            // FK IDs (int) for groups + measure
            'article_group' => $validated['article_group_id'],
            'sub_article'   => $validated['sub_article_group_id'] ?? null,
            'measure_unit'  => $measureId,
        ];

        if (config('produkt.identitaet.aktiv')) {
            // AUF-P1-S4 §8 Zeile 4 (wie Zeile 3): resolve() mit supplierArticleNo —
            // die IDS-Nummer ist eine Lieferantennummer, keine Herstellernummer.
            $dienst = app(ProductIdentityService::class);

            $identitaet = new ProductIdentity(
                gtin: $item->ean ?? null,
                brandId: (int) $validated['brand_id'],
                supplierArticleNo: $item->article_no,
                distributorId: $distributor->id,
                name: $productName,
                channel: 'ids:gconline',
            );

            $match = $dienst->resolve($identitaet);

            if ($match->ergebnis === IdentityMatch::KONFLIKT) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Identitätskonflikt — nicht angelegt: ' . $match->begruendung,
                    ], 409);
                }

                return redirect()->route('ids.search.form')
                    ->with('error', 'Identitätskonflikt — nicht angelegt: ' . $match->begruendung);
            }

            if ($match->ergebnis === IdentityMatch::VORSCHLAG) {
                $dienst->vorschlagSpeichern($identitaet, $match);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Möglicherweise vorhandener Artikel (#' . $match->product->id
                            . ') — Vorschlag zur Prüfung angelegt, nicht automatisch übernommen.',
                        'product_id' => $match->product->id,
                    ], 409);
                }

                return redirect()->route('ids.search.form')
                    ->with('error', 'Möglicherweise vorhandener Artikel — Vorschlag zur Prüfung angelegt.');
            }

            $product = $match->product ?? $dienst->createFrom($identitaet, $neueFelder);
        } else {
            // Alt-Verhalten, byte-gleich (Kante 15): firstOrCreate auf article_no.
            $product = Product::where('article_no', $item->article_no)->first()
                ?: app(ProductIdentityService::class)->createLegacy(
                    ['article_no' => $item->article_no] + $neueFelder
                );
        }

        // If product existed: update metadata
        $product->fill([
            'product'       => \Illuminate\Support\Str::limit($productName, 255),
            'price_unit'    => $measureText ?? $product->price_unit,
            'brand_id'      => $validated['brand_id'],
            'article_group' => $validated['article_group_id'],
            'sub_article'   => $validated['sub_article_group_id'] ?? null,
            'measure_unit'  => $measureId ?? $product->measure_unit,
        ]);
        $product->save();

        DistributorPrice::updateOrCreate(
            [
                'distributor_id' => $distributor->id,
                'product_id'     => $product->id,
            ],
            [
                'discount_group_id' => null,
                'article_no'        => $item->article_no,
                'discount_price'    => null,
                'discount_percent'  => null,
                'price'             => $item->offer_price ?? null,
                'purchase_price'    => $item->net_price ?? null,
                'price_date'        => now()->toDateString(),
                'availability'      => null,
                'status'            => 'Published',
            ]
        );

        $item->update([
            'product_id' => $product->id,
        ]);

        ImportedIdsItem::where('article_no', $item->article_no)
            ->whereNull('product_id')
            ->update(['product_id' => $product->id]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'        => true,
                'message'        => 'Produkt mit Hersteller, Artikelgruppe und Maßeinheit angelegt/aktualisiert.',
                'product_id'     => $product->id,
                'distributor_id' => $distributor->id,
            ]);
        }

        return redirect()
            ->route('ids.search.form')
            ->with('status', 'Produkt aus IDS-Artikel angelegt (inkl. Maßeinheit).');
    }


        public function showPromoteForm(ImportedIdsItem $item)
        {
            $articleGroups = ArticleGroup::orderBy('article_group')->get();
            $brands        = Brand::orderBy('name')->get(); // ggf. ->where('status','Published')

            return view('ids.promote_product', [
                'item'          => $item,
                'articleGroups' => $articleGroups,
                'brands'        => $brands,
            ]);
        }


        public function getSubArticleGroups(ArticleGroup $articleGroup)
        {
            $subGroups = $articleGroup->subArticleGroups()
                ->orderBy('sub_article')
                ->get(['id', 'sub_article']);

            return response()->json($subGroups);
        }

}
