<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\QrCode;
use Illuminate\Http\Request;
use DB;
class QrCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');

        if($search){
            $data['branch']=Branch::all();
            $data['data']=DB::table('qr_codes')
                        ->join('branches', 'branches.id', 'qr_codes.branch')
                        ->select('qr_codes.*', 'branches.branch')
                        ->where('branches.branch', 'like', "%$search%")
                        ->orWhere('qr_codes.qrcode', 'like', "%$search%")
                        ->paginate(40);
            return view('admin.product.assets.qrcode_details', $data);

        }
        else{
            $data['branch']=Branch::all();

            $data['data']=DB::table('qr_codes')
            ->join('branches', 'branches.id', 'qr_codes.branch')
            ->select('qr_codes.*', 'branches.branch')
            ->paginate(40);

        return view('admin.product.assets.qrcode_details', $data);
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function print()
    {
        $data=DB::table('qr_codes')
        ->join('branches', 'branches.id', 'qr_codes.branch')
        ->select('qr_codes.*', 'branches.branch')
        ->get();

        return view('admin.product.assets.qr')->with('data', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $quantity = $request->quantity;
        $randomNumbers = [];
        
        for ($i = 0; $i < $quantity; $i++) { 
            $qr = mt_rand(1000000000, 9999999999);
            // Store the random number in an array or use it as needed
            $randomNumbers[] = $qr;
        }
       
        foreach ($randomNumbers as $key => $value) {
            QrCode::create([
                'qrcode'    =>  $value,
                'branch'    => $request->branch,
    
            ]);
        }
       
       
        return redirect()->to('qrcode_details')->with('save_msg', 'The barcode generated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(QrCode $qrCode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QrCode $qrCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QrCode $qrCode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=QrCode::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Deleted!!!');
    }
}
