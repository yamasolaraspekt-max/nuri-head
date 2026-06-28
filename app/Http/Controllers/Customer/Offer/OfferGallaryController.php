<?php

namespace App\Http\Controllers\Customer\Offer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferGallaryController extends Controller
{
     
    public function index($customer_id, $alternative_id, $product_id)
    {
        $uploads = Image::where('stage', 'offer')
            ->where('customer_id', $customer_id)
            ->where('alternative_id', $alternative_id)
            ->where('article_group', $product_id)
            ->get();
    
        // Only return gallery partial (you don't have a full page view)
        return view('admin.offer.offer.partials.gallary', compact('uploads'))->render();
    }
    
    public function upload(Request $request)
    {
        \Log::info("Requested Upload", [$request->all()]);
        $request->validate([
            'file' => 'required|file|max:10240',
            'customer_id' => 'required|integer',
            'alternative_id' => 'required|integer',
            'product_id' => 'required|integer', // treated as article_group
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '_' . Str::slug($file->getClientOriginalName());
            $file->move(public_path('uploads'), $filename);

            Image::create([
                'customer_id'    => $request->customer_id,
                'alternative_id' => $request->alternative_id,
                'article_group'  => $request->product_id,
                'stage'          => 'offer',
                'image_name'     => $file->getClientOriginalName(),
                'image'          => $filename,
                'file_type'      => $file->getClientMimeType(),
                'created_by'     => auth()->id(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $file = Image::findOrFail($id);
        $path = public_path('uploads/' . $file->image);

        if (File::exists($path)) {
            File::delete($path);
        }

        $file->delete();
        return response()->json(['success' => true]);
    }

    public function rename(Request $request, $id)
    {
        $file = Image::findOrFail($id);
        $file->image_name = $request->input('name');
        $file->update_by = auth()->id();
        $file->save();

        return response()->json(['success' => true]);
    }
     
}
