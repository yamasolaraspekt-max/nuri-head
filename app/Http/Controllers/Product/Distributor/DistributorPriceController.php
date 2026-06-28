<?php

namespace App\Http\Controllers\Product\Distributor;

use App\Http\Controllers\Controller;

use App\Models\DiscountGroup;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use Illuminate\Http\Request;
use DB;
use App\Models\Product;
use Log; 
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\View;
class DistributorPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index($id, $product_id)
        {
            $search = request()->query('search');
            
            $data['product_id'] = $product_id;
            $data['discount_groups'] = DB::table('discount_groups')
                ->select('id', 'discount_group', 'discount')
                ->get();

            $query = DB::table('distributor_prices')
                ->leftJoin('products', 'products.id', '=', 'distributor_prices.product_id')
                ->leftJoin('distributors', 'distributors.id', '=', 'distributor_prices.distributor_id')
                ->select('distributor_prices.*', 'distributors.name', 'products.product');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('products.product', 'LIKE', "%$search%")
                    ->orWhere('distributors.name', 'LIKE', "%$search%")
                    ->orWhere('distributor_prices.price_date', 'LIKE', "%$search%");
                });
            } else {
                $query->where('products.id', $product_id)
                    ->where('distributors.id', $id);
            }

            $data['distributor_price'] = $query->get();

            return view('admin.product.distributor.distributor_price', $data);
        }


    /**
     * Show the form for creating a new resource.
     */
    public function create($product_id)
    {
        $search = request()->query('search');
        $distributor_id = request()->query('distributor_id');
        $price_date = request()->query('price_date');
    
        // Common data
        $data['product_id'] = $product_id;
        $data['discount_groups'] = DB::table('discount_groups')->select('id', 'discount_group', 'discount')->get();
        $data['product'] = Product::find($product_id);
    
        $data['distributors'] = DB::table('distributor_product')
            ->join('distributors', 'distributors.id', '=', 'distributor_product.distributor_id')
            ->join('products', 'products.id', '=', 'distributor_product.product_id')
            ->select(
                'distributors.name as distributor_name',
                'distributors.image as distributor_image',
                'distributor_product.product_id',
                'distributors.id as distributor_id'
            )
            ->where('products.id', $product_id)
            ->groupBy(
                'distributors.name',
                'distributors.image',
                'distributor_product.product_id',
                'distributors.id'
            )
            ->get();
    
        $query = DB::table('distributor_prices')
            ->join('products', 'products.id', '=', 'distributor_prices.product_id')
            ->join('distributors', 'distributors.id', '=', 'distributor_prices.distributor_id')
            ->select('distributor_prices.*', 'distributors.name', 'products.product')
            ->where('products.id', '=', $product_id);
    
        // Apply filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product', 'LIKE', "%{$search}%")
                  ->orWhere('distributors.name', 'LIKE', "%{$search}%")
                  ->orWhere('distributor_prices.price_date', 'LIKE', "%{$search}%");
            });
        }
    
        if ($distributor_id) {
            $query->where('distributor_prices.distributor_id', $distributor_id);
        }
    
        if ($price_date) {
            $query->whereDate('distributor_prices.price_date', $price_date);
        }
    
        $data['distributor_price'] = $query->get();
    
        return view('admin.product.distributor.create_distributor_price', $data);
    }
    


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'd.*.price' => 'required',                      
            'd.*.price_date' => 'required',
            'd.*.discount_price' => 'required',
            'd.*.availability' => 'required',
            'd.*.product_id' => 'required',  // Add validation to ensure product_id is passed
        ], [
            'd.*.price.required' => 'Preis: Dieses Feld ist erforderlich',
            'd.*.price_date.required' => 'Preisdatum: Dieses Feld ist erforderlich',
            'd.*.discount_price.required' => 'Rabattpreis: Dieses Feld ist erforderlich',
            'd.*.availability.required' => 'Verfügbarkeit: Dieses Feld ist erforderlich',
            'd.*.product_id.required' => 'Produkt ID ist erforderlich',  // Custom validation message
        ]);
    
        foreach ($request->input('d') as $data) {
            // Ensure product_id is passed in the request
            $product_id = $data['product_id'];
    
            // Calculate purchase price
            $purchasePrice = isset($data['discount_percent']) 
                ? $data['price'] - ($data['price'] * $data['discount_percent'] / 100) 
                : $data['price'] - $data['discount_price'];
    
            // Create DistributorPrice
            DistributorPrice::create([
                "distributor_id" => $data['distributor_id'],
                "product_id" => $product_id,  // Use the passed product_id
                "article_no" => $data['article_no'],
                "status" => $data['status'] ?? 'Published',
                "price" => $data['price'],
                "price_date" => $data['price_date'],
                "availability" => $data['availability'],
                "discount_price" => $data['discount_price'],
                "purchase_price" => $purchasePrice,
                "discount_percent" => $data['discount_percent'] ?? null,
            ]);
        }
    
        return back()->with('save_msg', 'Record has been saved successfully!');
    }
    
    

    /**
     * Display the specified resource.
     */
    public function publish($id){
        $data=DistributorPrice::findorFail($id);
        $data->status='Published';
        $data->save();

        return redirect()->back()->with('save_msg', 'Das Unternehmen ist jetzt veröffentlicht');

    }

    public function unpublish($id){
        $data=DistributorPrice::findorFail($id);
        $data->status='Unpublished';
        $data->save();
    
        return redirect()->back()->with('delete_msg', 'Der Artikel wurde nun erfolgreich unveröffentlicht!');

    }

    /**
     * Update the specified resource in storage.
     */
 
public function update(Request $request, Product $product, DistributorPrice $price)
{
    $toDec = function ($v) {
        if ($v === null) return null;
        $s = trim((string)$v);
        if ($s === '') return null;
        $s = str_replace(["\u{00A0}", ' '], '', $s);
        $s = str_replace(',', '.', $s);
        return is_numeric($s) ? $s : null;
    };

    $validator = Validator::make($request->all(), [
        'distributor_id'    => 'required|integer|exists:distributors,id',
        'discount_group_id' => 'nullable|integer|exists:discount_groups,id',
        'article_no'        => 'nullable|string|max:255',

        'discount_price'    => 'nullable',
        'discount_percent'  => 'nullable|min:0|max:100',
        'price'             => 'nullable',
        'purchase_price'    => 'nullable',

        'price_date'        => 'nullable|date',
        'availability'      => 'nullable|string|max:255',
        'status'            => 'nullable|in:Published,Unpublished',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    $data['discount_group_id'] = $data['discount_group_id'] ?: null;
    $data['article_no']        = ($data['article_no'] ?? '') !== '' ? $data['article_no'] : null;

    // normalize decimals
    $data['price']            = $toDec($data['price'] ?? null);
    $data['discount_price']   = $toDec($data['discount_price'] ?? null);
    $data['discount_percent'] = $toDec($data['discount_percent'] ?? null);
    $data['purchase_price']   = $toDec($data['purchase_price'] ?? null);

    // ensure this price belongs to product
    if ((int)$price->product_id !== (int)$product->id) {
        return response()->json(['error' => 'Datensatz gehört nicht zu diesem Produkt.'], 403);
    }

    // compute purchase_price if missing
    $priceVal = $data['price'] !== null ? (float)$data['price'] : null;
    $discP    = $data['discount_percent'] !== null ? (float)$data['discount_percent'] : null;
    $discE    = $data['discount_price'] !== null ? (float)$data['discount_price'] : null;
    $ek       = $data['purchase_price'] !== null ? (float)$data['purchase_price'] : null;

    if ($ek === null) {
        if ($priceVal !== null && $discP !== null) $ek = $priceVal - ($priceVal * $discP / 100);
        elseif ($priceVal !== null && $discE !== null) $ek = $priceVal - $discE;
    }
    $data['purchase_price'] = $ek !== null ? number_format($ek, 2, '.', '') : null;

    $data['status'] = $data['status'] ?? 'Published';

    DB::transaction(function () use ($price, $data) {
        $price->update($data);
    });

    $row = DB::table('distributor_prices')
        ->join('distributors', 'distributors.id', '=', 'distributor_prices.distributor_id')
        ->select('distributor_prices.*', 'distributors.name as distributor_name')
        ->where('distributor_prices.id', $price->id)
        ->first();

    return response()->json([
        'message' => 'Lieferantenpreis aktualisiert.',
        'price'   => $row, // ✅ your JS expects res.price
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data= DistributorPrice::find($id);
        $data->delete();
        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich geschlöcht!');

    }


    public function save(Request $request)
    {
        $data = $request->validate([
            'price.*.distributor_id' => 'required|exists:distributors,id',
            'price.*.price' => 'required|numeric',
            'price.*.discount_price' => 'nullable|numeric',
            'price.*.discount_percent' => 'nullable|numeric',
            'price.*.purchase_price' => 'nullable|numeric',
            'price.*.article_no' => 'nullable|string',
            'price.*.price_date' => 'nullable|date',
            'price.*.availability' => 'nullable|string',
            'price.*.discount_group_id' => 'nullable|exists:discount_groups,id',
            'price.*.product_id' => 'required|exists:products,id'
        ]);

        foreach ($data['price'] as $entry) {
            DistributorPrice::create($entry);
        }

        return response()->json(['message' => 'Preise gespeichert']);
    }

    public function list($productId)
    {
        return response()->json(
            DistributorPrice::with('distributor')
                ->where('product_id', $productId)
                ->get()
        );
    }

    public function delete($id)
    {
        DistributorPrice::destroy($id);
        return response()->json(['message' => 'Eintrag gelöscht']);
    }

    public function updateDistributor(Request $request, $id)
    {
        $data = $request->validate([
            'price' => 'required|numeric',
            'purchase_price' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'availability' => 'nullable|string',
        ]);

        DistributorPrice::findOrFail($id)->update($data);
        return response()->json(['message' => 'Aktualisiert']);
    }

 
public function storeSingle(Request $request, Product $product)
{
    // ----------------------------
    // Helpers
    // ----------------------------
    $toDec = static function ($v): ?string {
        if ($v === null) return null;

        $s = trim((string) $v);
        if ($s === '') return null;

        // remove spaces & NBSP
        $s = str_replace(["\u{00A0}", ' '], '', $s);
        // allow german comma
        $s = str_replace(',', '.', $s);

        return is_numeric($s) ? $s : null;
    };

    $fmt2 = static function ($v): ?string {
        if ($v === null) return null;
        return number_format((float) $v, 2, '.', '');
    };

    try {
        // ----------------------------
        // Validate (never require calc-able combinations)
        // ----------------------------
        $data = Validator::make($request->all(), [
            'distributor_id'    => 'required|integer|exists:distributors,id',
            'discount_group_id' => 'nullable|integer|exists:discount_groups,id',
            'article_no'        => 'nullable|string|max:255',

            // accept anything numeric-like; we normalize ourselves
            'price'             => 'nullable',
            'discount_price'    => 'nullable',
            'discount_percent'  => 'nullable|numeric|min:0|max:100',
            'purchase_price'    => 'nullable',

            'price_date'        => 'nullable|date',
            'availability'      => 'nullable|string|max:255',
            'status'            => 'nullable|in:Published,Unpublished',
        ])->validate();

        // ----------------------------
        // Normalize primitive inputs
        // ----------------------------
        $priceStr = $toDec($data['price'] ?? null);
        $discEStr = $toDec($data['discount_price'] ?? null);
        $discPStr = $toDec($data['discount_percent'] ?? null);
        $ekStr    = $toDec($data['purchase_price'] ?? null);

        $price = $priceStr !== null ? (float) $priceStr : null;
        $discE = $discEStr !== null ? (float) $discEStr : null;
        $discP = $discPStr !== null ? (float) $discPStr : null;
        $ek    = $ekStr    !== null ? (float) $ekStr    : null;

        $hasPrice = $price !== null && $price >= 0;
        $hasEk    = $ek    !== null && $ek    >= 0;
        $hasDiscE = $discE !== null && $discE >= 0;
        $hasDiscP = $discP !== null && $discP >= 0;

        // normalize empty string FK -> null
        $data['discount_group_id'] = !empty($data['discount_group_id']) ? (int) $data['discount_group_id'] : null;
        $data['article_no']        = !empty($data['article_no']) ? $data['article_no'] : null;

        // Always default status
        $data['status'] = $data['status'] ?? 'Published';

        // ----------------------------
        // BUSINESS RULE:
        // EK-only must always work.
        // If only EK is provided => store EK and clear others.
        // ----------------------------
        if ($hasEk && !$hasPrice && !$hasDiscE && !$hasDiscP) {
            $price = null;
            $discE = null;
            $discP = null;
            $hasPrice = $hasDiscE = $hasDiscP = false;
        } else {
            // ----------------------------
            // Compute if possible (but NEVER fail if ambiguous)
            // ----------------------------

            // A) price + percent -> discE + ek
            if ($hasPrice && $hasDiscP) {
                $p = min(max($discP, 0.0), 99.0);
                $discE = $price * $p / 100.0;
                $ek    = $price - $discE;
                $hasDiscE = $hasEk = true;
            }

            // B) price + discE -> ek + discP
            if ($hasPrice && $hasDiscE) {
                $ek = $price - $discE;
                $hasEk = true;

                if (!$hasDiscP) {
                    $discP = $price > 0 ? ($discE / $price) * 100.0 : 0.0;
                    $hasDiscP = true;
                }
            }

            // C) price + ek -> discE + discP
            if ($hasPrice && $hasEk) {
                if (!$hasDiscE) {
                    $discE = $price - $ek;
                    $hasDiscE = true;
                }
                if (!$hasDiscP) {
                    $discP = $price > 0 ? (($price - $ek) / $price) * 100.0 : 0.0;
                    $hasDiscP = true;
                }
            }

            // D) ek + percent -> price + discE
            if ($hasEk && $hasDiscP && !$hasPrice) {
                $p = min(max($discP, 0.0), 99.0);
                $price = ($p >= 100.0) ? null : ($ek / (1 - $p / 100.0));
                if ($price !== null) {
                    $discE = $price - $ek;
                    $hasPrice = $hasDiscE = true;
                }
            }

            // E) ek + discE -> price + discP
            if ($hasEk && $hasDiscE && !$hasPrice) {
                $price = $ek + $discE;
                $hasPrice = true;

                if (!$hasDiscP) {
                    $discP = $price > 0 ? ($discE / $price) * 100.0 : 0.0;
                    $hasDiscP = true;
                }
            }

            // F) If discounts exist without any base (no price AND no ek) => clear discounts
            if (!$hasPrice && !$hasEk) {
                $discE = null;
                $discP = null;
                $hasDiscE = $hasDiscP = false;
            }

            // G) If discount_percent exists but discount_group_id is set and percent missing:
            // (optional: keep your original group logic if you want)
            // NOTE: only apply if user didn't provide discP explicitly
            if (!$hasDiscP && !empty($data['discount_group_id'])) {
                $group = DiscountGroup::find($data['discount_group_id']);
                if ($group && $group->discount !== null) {
                    $discP = (float) $group->discount;
                    $hasDiscP = true;

                    // If we have a base, compute from it
                    if ($hasPrice) {
                        $discE = $price * $discP / 100.0;
                        $ek    = $price - $discE;
                        $hasDiscE = $hasEk = true;
                    } elseif ($hasEk) {
                        $p = min(max($discP, 0.0), 99.0);
                        $price = $ek / (1 - $p / 100.0);
                        $discE = $price - $ek;
                        $hasPrice = $hasDiscE = true;
                    }
                }
            }
        }

        // ----------------------------
        // Persist
        // ----------------------------
        $payload = [
            'product_id'        => $product->id,
            'distributor_id'    => (int) $data['distributor_id'],
            'discount_group_id' => $data['discount_group_id'],
            'article_no'        => $data['article_no'],

            'price'             => $fmt2($price),
            'discount_price'    => $fmt2($discE),
            'discount_percent'  => $discP !== null ? (string) (int) round($discP) : null,
            'purchase_price'    => $fmt2($ek),

            'price_date'        => $data['price_date'] ?? null,
            'availability'      => $data['availability'] ?? null,
            'status'            => $data['status'],
        ];

        DB::beginTransaction();

        // Unique per product+distributor (prevents duplicates)
        $priceRow = DistributorPrice::updateOrCreate(
            [
                'product_id'     => $product->id,
                'distributor_id' => (int) $data['distributor_id'],
            ],
            $payload
        );

        $row = DB::table('distributor_prices')
            ->join('distributors', 'distributors.id', '=', 'distributor_prices.distributor_id')
            ->select('distributor_prices.*', 'distributors.name as distributor_name')
            ->where('distributor_prices.id', $priceRow->id)
            ->first();

        DB::commit();

        return response()->json([
            'message' => 'Lieferantenpreis gespeichert.',
            'price'   => $row, // JS expects res.price
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validierung fehlgeschlagen.',
            'errors'  => $e->errors(),
        ], 422);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('DistributorPrice storeSingle error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

        return response()->json([
            'message' => 'Ein Fehler ist aufgetreten.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
