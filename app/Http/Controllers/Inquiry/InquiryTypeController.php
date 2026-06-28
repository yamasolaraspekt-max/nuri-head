<?php

namespace App\Http\Controllers\Inquiry;
use App\Http\Controllers\Controller;

use App\Models\InquiryType;
use Illuminate\Http\Request;
use DB;

class InquiryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $search=request()->query('search');

        if($search){

            $data=DB::table('inquiry_types')
                ->where('type', 'LIKE', "%$search%") 
                ->paginate(10);

            return view('admin.inquiry.type')->with('data', $data);
        }
        else{
            $data=DB::table('inquiry_types')->orderBy('id', 'desc')
            ->paginate(10);
            return view('admin.inquiry.type')->with('data', $data);

        }
    }

  
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate(
            [
                'customer'            => 'required|array',
                'customer.*.type'     => 'required|string', 
            ],
            [
                'customer.*.type.required' => 'Die Type ist erforderlich', 
            ]
        );

        // Loop through the customer array and save each type
        foreach ($validatedData['customer'] as $customerData) {
            InquiryType::create(['type' => $customerData['type']]);
        }

        return back()->with('save_msg', 'Die Typ wurde erfolgreich hinzugefügt!');
    }


    public function save(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
        ]);

        try {
            $type = InquiryType::create(['type' => $request->type]);

            return response()->json([
                'success' => true,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Fetch all inquiry types
    public function getType()
    {
        $types = InquiryType::orderBy('id', 'desc')->get();
        return response()->json($types, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InquiryType $inquiryType)
    {
        //
    }

    public function update(Request $request, InquiryType $country)
    {
        $request->validate(['type'  =>  'requried', 'id'    =>  'required|exists:inquiry_types,id']);
        $id= $request->id;
        $data=InquiryType::find($id);
        $data->type=$request->type; 
        $data->save();

        return redirect()->back()->with('save_msg', 'Die Typ wurde erfolgreich hinzugefügt!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=InquiryType::find($id);
        $data->delete();
        return back()->with('delete_msg', 'Die Typ wurde erfolgreich gelöscht!');
    }
}
