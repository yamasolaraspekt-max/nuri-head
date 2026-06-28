<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\NewLeads;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerObjectProductModalController extends Controller
{
    public function tree(Request $request, $customer)
    {
        $lead = NewLeads::query()
            ->with([
                'objects' => function ($q) {
                    $q->orderByDesc('main')->orderBy('id');
                },
                'objects.products' => function ($q) {
                    $q->with([
                        'product:id,article_group,image',
                        // ✅ FIX: relationship is "service", not "section"
                        'service:id,phase_section,status,product_id',
                    ])->orderBy('id');
                },
            ])
            ->findOrFail($customer);

        $data = [
            'customer' => [
                'id'          => $lead->id,
                'name'        => trim(($lead->title ? $lead->title . ' ' : '') . ($lead->name ?? '') . ' ' . ($lead->lastname ?? '')),
                'firma'       => $lead->firma,
                'customer_no' => $lead->customer_no,
                'phone'       => $lead->phone,
                'email'       => $lead->email,
                'branch'      => $lead->branch,
            ],
            'objects' => $lead->objects->map(function ($obj) {
                return [
                    'id'           => $obj->id,
                    'customer_id'  => $obj->lead_id, // lead_id = customer id
                    'object_name'  => $obj->object_name,
                    'full_address' => $obj->full_address,
                    'request_date' => optional($obj->request_date)->format('Y-m-d H:i:s'),
                    'main'         => (int)($obj->main ?? 0),
                    'products'     => $obj->products->map(function ($p) {
                        return [
                            'id'               => $p->id,
                            'product_id'        => $p->product_id,
                            'product_name'      => optional($p->product)->article_group,
                            'product_image'     => optional($p->product)->image,

                            'service_id'        => $p->service_id,

                            // ✅ FIX: use $p->service (PhaseSection)
                            'phase_section_key' => optional($p->service)->phase_section,

                            'status'            => $p->status,
                            'work_status'       => $p->work_status,
                            'price'             => $p->price,
                            'stage'             => $p->stage,
                        ];
                    })->values(),
                ];
            })->values(),
        ];

        return response()->json(['ok' => true, 'data' => $data]);
    }

    public function moveProduct(Request $request, $leadProduct)
    {
        $data = $request->validate([
            'to_alternative_id' => ['required','integer','min:1'],
        ]);

        /** @var LeadProductList $product */
        $product = LeadProductList::query()->findOrFail($leadProduct); 
        $toObject = LeadAlternativeAdd::query()->findOrFail((int) $data['to_alternative_id']);

        // ensure same customer
        if ((int) $toObject->lead_id !== (int) $product->customer_id) {
            return response()->json(['ok' => false, 'message' => 'Customer mismatch.'], 422);
        }

        $product->alternative_id = (int) $toObject->id;
        $product->save();

        return response()->json(['ok' => true]);
    }

    public function createObject(Request $request, $customer)
    {
        $lead = NewLeads::query()->findOrFail($customer);

        $data = $request->validate([
            'object_name'  => ['required','string','max:255'],
            'request_date' => ['nullable','date'],
            'branch'       => ['nullable','integer'],
            'full_address' => ['nullable','string'],
            'street'       => ['nullable','string','max:255'],
            'postcode'     => ['nullable','string','max:20'],
            'city'         => ['nullable','string','max:255'],
            'lat'          => ['nullable','numeric'],
            'lon'          => ['nullable','numeric'],
            'note'         => ['nullable','string'],
            'objective'    => ['nullable','string','max:255'],
        ]);

        $obj = DB::transaction(function () use ($lead, $data) {
            $obj = new LeadAlternativeAdd();
            $obj->lead_id = $lead->id;

            $obj->object_name = $data['object_name'];
            $obj->request_date = $data['request_date'] ?? now();

            $obj->full_address = $data['full_address'] ?? null;
            $obj->street = $data['street'] ?? null;
            $obj->postcode = $data['postcode'] ?? null;
            $obj->city = $data['city'] ?? null;
            $obj->lat = $data['lat'] ?? null;
            $obj->lon = $data['lon'] ?? null;

            $obj->note = $data['note'] ?? null;
            $obj->objective = $data['objective'] ?? null;

            $obj->status = 'Published';
            $obj->stage = 'lead';
            $obj->save();

            return $obj;
        });

        return response()->json([
            'ok' => true,
            'object' => [
                'id'           => $obj->id,
                'customer_id'  => $obj->lead_id,
                'object_name'  => $obj->object_name,
                'full_address' => $obj->full_address,
                'request_date' => optional($obj->request_date)->format('Y-m-d H:i:s'),
                'main'         => (int)($obj->main ?? 0),
                'products'     => [],
            ]
        ]);
    }


    public function deleteProduct(Request $request, $leadProduct)
{
    /** @var LeadProductList $product */
    $product = LeadProductList::query()->findOrFail($leadProduct);

    // Optional: authorization / branch checks

    $product->delete(); // soft delete if SoftDeletes trait is enabled

    return response()->json(['ok' => true]);
}

public function deleteObject(Request $request, $object)
{
    /** @var LeadAlternativeAdd $obj */
    $obj = LeadAlternativeAdd::query()->findOrFail($object);

    $data = $request->validate([
        'move_to_alternative_id' => ['nullable','integer','min:1'],
        'delete_products'        => ['nullable','boolean'],
    ]);

    // Optional safety: prevent deleting main object
    // if ((int)$obj->main === 1) {
    //     return response()->json(['ok'=>false,'message'=>'Hauptobjekt kann nicht gelöscht werden.'], 422);
    // }

    $deleteProducts = (bool)($data['delete_products'] ?? false);
    $moveToId = $data['move_to_alternative_id'] ?? null;

    // If moveTo provided, ensure it belongs to same customer
    if ($moveToId) {
        $to = LeadAlternativeAdd::query()->findOrFail((int)$moveToId);
        if ((int)$to->lead_id !== (int)$obj->lead_id) {
            return response()->json(['ok'=>false,'message'=>'Customer mismatch.'], 422);
        }
    }

    DB::transaction(function () use ($obj, $deleteProducts, $moveToId) {

        $productsQ = LeadProductList::query()->where('alternative_id', $obj->id);

        if ($productsQ->exists()) {
            if ($moveToId && !$deleteProducts) {
                // ✅ Move products
                $productsQ->update(['alternative_id' => (int)$moveToId]);
            } else {
                // ✅ Delete products
                $productsQ->delete(); // soft delete if enabled
            }
        }

        // ✅ Delete the object itself
        $obj->delete(); // LeadAlternativeAdd already uses SoftDeletes
    });

    return response()->json(['ok' => true]);
}

}
