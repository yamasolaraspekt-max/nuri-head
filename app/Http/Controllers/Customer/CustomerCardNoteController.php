<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\CustomerCardNote;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class CustomerCardNoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['save']);
        $this->middleware('permission:Customer,delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'    => 'required|integer',
            'alternative_id' => 'required|integer',
            'product_id'     => 'required|integer',
            'field'          => 'required|in:title,description',
            'value'          => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $data = $validator->validated();
    
        $note = CustomerCardNote::updateOrCreate(
            [
                'customer_id'    => $data['customer_id'],
                'alternative_id' => $data['alternative_id'],
                'product_id'     => $data['product_id'],
            ],
            [$data['field'] => $data['value']]
        );
    
        return response()->json(['success' => true, 'note' => $note]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'     => 'required|exists:new_leads,id',
            'alternative_id'  => 'required|exists:lead_alternative_adds,id',
            'product_id'      => 'nullable|exists:article_groups,id',
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
        ]);

        $note = \App\Models\CustomerCardNote::updateOrCreate(
            [
                'customer_id'    => $request->customer_id,
                'alternative_id' => $request->alternative_id,
                'product_id'     => $request->product_id,
            ],
            [
                'title'       => $request->title,
                'description' => $request->description,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Note gespeichert',
            'note'    => $note,
        ]);
    }


    public function destroy(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required',
            'alternative_id' => 'required',
            'product_id'     => 'nullable',
        ]);

        $note = \App\Models\CustomerCardNote::where('customer_id', $request->customer_id)
            ->where('alternative_id', $request->alternative_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$note) {
            return response()->json(['success' => false, 'message' => 'Notiz nicht gefunden']);
        }

        $note->delete();

        return response()->json(['success' => true, 'message' => 'Notiz gelöscht']);
    }


}
