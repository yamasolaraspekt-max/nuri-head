<?php

namespace App\Http\Controllers\Ticket;
use App\Http\Controllers\Controller;

use App\Models\TicketFile;
use Illuminate\Http\Request; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TicketFileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Problem: Rollen-Gate (permission:Problem)
        $this->middleware('permission:Problem,update')->only(['update', 'edit']);
        $this->middleware('permission:Problem,delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
public function index($id)
    {
        \Log::info('Loading ticket files for ticket_id: ' . $id);

        $files = TicketFile::where('ticket_id', $id)
            ->orderBy('file_type')
            ->get()
            ->groupBy('file_type');

        return response()->json($files, 200);
    }


    public function destroy($id)
    {
        $file = TicketFile::findOrFail($id);

        if (File::exists(public_path($file->image))) {
            File::delete(public_path($file->image));
        }

        $file->delete();
        return response()->json(['success' => true]);
    }

public function update(Request $request, $id)
{
    $request->validate([
        'image_name' => 'required|string|max:255',
    ]);

    $file = TicketFile::findOrFail($id);
    $file->image_name = $request->image_name;
    $file->save();

    return response()->json(['success' => true]);
}

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
       $request->validate([
            'file' => 'required|mimes:jpeg,jpg,png,bmp,gif,svg,pdf,docx,xlsx|max:5120',
            'ticket_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'alternative_id' => 'required|integer',
            'product_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'ticket_type' => 'required|string'
        ]);



        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = public_path('uploads/errors');
        $file->move($path, $filename);
 

        TicketFile::create([
            'ticket_id' => $request->ticket_id,
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'product_id' => $request->product_id,
            'employee_id' => $request->employee_id,
            'ticket_type' => $request->ticket_type,
            'image_name' => $file->getClientOriginalName(),
            'image' => 'uploads/errors/' . $filename,
            'file_type' => $file->getClientMimeType(),
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'File uploaded successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketFile $ticketFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketFile $ticketFile)
    {
        //
    }

  
}
