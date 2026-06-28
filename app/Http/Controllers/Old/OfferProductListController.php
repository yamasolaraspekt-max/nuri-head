<?php

namespace App\Http\Controllers;

use App\Models\OfferProductList;
use App\Models\Offer;
use App\Models\OfferFolder;
use Illuminate\Http\Request;

class OfferProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function loadProducts(Offer $offer, OfferFolder $folder)
{
    // get all products for this folder
    $items = $folder->productLists()
        ->orderBy('sort_order')
        ->get();

    return response()->json([
        'items' => $items,
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OfferProductList $offerProductList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfferProductList $offerProductList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OfferProductList $offerProductList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfferProductList $offerProductList)
    {
        //
    }
}
