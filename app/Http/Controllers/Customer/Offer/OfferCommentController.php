<?php

namespace App\Http\Controllers\Customer\Offer;
use App\Http\Controllers\Controller;

use App\Models\OfferComment;
use Illuminate\Http\Request;

class OfferCommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['update']);
        $this->middleware('permission:Customer,delete')->only(['destroy']);
    }

    public function index($customer, $alternative, $product)
    {
        $comments = OfferComment::with('employee', 'children.employee')
            ->where('customer_id', $customer)
            ->where('alternative_id', $alternative)
            ->where('product_id', $product)
            ->whereNull('parent_id')
            ->latest()
            ->get();
    
        return view('admin.offer.offer.comments.comments', compact('comments'));
    }
    
    public function store(Request $request)
    {
        $comments = OfferComment::create([
            'customer_id'     => $request->customer_id,
            'alternative_id'  => $request->alternative_id,
            'product_id'      => $request->product_id,
            'employee_id'     => auth()->user()->name,
            'parent_id'       => $request->parent_id,
            'comment'         => $request->comment,
        ]);
    
        // Load relationships
        $comments->load('employee', 'children.employee');
    
        // Render only this comment
        return view('admin.offer.offer.comments.comments', [
            'comments' => collect([$comments]) // Wrap it so @foreach works
        ])->render();
        
    }
    
    public function update(Request $request, $id)
    {
        $comment = OfferComment::findOrFail($id);
        $comment->update(['comment' => $request->comment]);
        return response()->json(['status' => 'updated']);
    }
    
    public function destroy($id)
    {
        OfferComment::findOrFail($id)->delete();
        return response()->json(['status' => 'deleted']);
    }
}
