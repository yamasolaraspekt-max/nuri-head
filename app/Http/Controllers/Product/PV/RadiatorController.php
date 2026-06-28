<?php

namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;
use App\Models\Radiator;
use Illuminate\Http\Request;

class RadiatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('admin.product.pv.radiator.radiator')->with('id', $id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Radiator::create($request->all());

       return redirect()->to('product_create_description/'.$request->product_id)->with('save_msg', 'Das Produkt wurde erfolgreich hinzugefügt!');

    }

    /**
     * Display the specified resource.
     */
    public function show(Radiator $radiator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Radiator $radiator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Radiator $radiator)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Radiator $radiator)
    {
        //
    }
}
