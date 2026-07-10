<?php

namespace App\Http\Controllers\Ticket;
use App\Http\Controllers\Controller;

use App\Models\Error;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\File;

class ErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        // MASTER-01 P1-IDOR Problem: Rollen-Gate (permission:Problem)
        $this->middleware('permission:Problem,update')->only(['error_save', 'update', 'updateStatus']);
        $this->middleware('permission:Problem,delete')->only(['destroy']);
        $this->middleware('auth');
    }


    public function index()
    {
        $search = request()->query('search');

        $query = DB::table('errors')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'errors.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'errors.brand_id')
            ->select('errors.*', 'article_groups.article_group', 'brands.name as brand_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('errors.problem_types', 'LIKE', "%$search%")
                ->orWhere('errors.error_code', 'LIKE', "%$search%")
                ->orWhere('errors.article_name', 'LIKE', "%$search%");
            });
        }

        $data['data'] = $query->orderBy('id', 'asc')->paginate(10);

        $data['product'] = DB::table('article_groups')
            ->select('id', 'article_group')
            ->get();

        $data['brands'] = DB::table('brands')
            ->where('status', 'Published')
            ->get();

        return view('admin.error_type.error_type', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
   public function store(Request $request)
    {
        $request->validate([
            'problem_types' => 'required|string|max:255',
            'error_code'    => 'nullable|string|max:255',
            'reason'        => 'nullable|string',
            'solution'      => 'nullable|string',
            'file'          => 'nullable|file|max:2048', 
            'status'        => 'nullable|string|max:50',
        ], [
            'file.max' => 'Die Datei darf nicht größer als 2 MB sein.'
        ]);

        $data = $request->only(['problem_types', 'error_code', 'reason', 'solution', 'status']);
        $filename = null; // ✅ Prevent undefined variable

        if ($request->hasFile('file')) {
            $filename = time().'_'.$request->file('file')->getClientOriginalName();
            $request->file('file')->move(public_path('uploads/errors'), $filename);
        }

        Error::create([
            'product_id' => $request->product_id,
            'employee_id' => auth()->user()->name,
            'article_name' => $request->article_name,
            'brand_id' => $request->brand_id,
            'problem_types' => $request->problem_types,
            'error_code' => $request->error_code,
            'reason' => $request->reason,
            'solution' => $request->solution,
            'status' => $request->status,
            'file' => $filename,  
            'links' => $request->links,  
            'serial_no' => $request->serial_no,  
            'latest_update' => $request->latest_update,  
        ]);

        return response()->json(['success' => true]);
    }





      public function error_save(Request $request)
    {
        $this->validate($request, [
            'problem_types' =>  'required'
        ]);

       $data = Error::create($request->all());

     return response()->json($data, 200);
    }
        
    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:errors,id',
            'problem_types' => 'required|string|max:255',
            'error_code'    => 'nullable|string|max:255',
            'reason'        => 'nullable|string',
            'solution'      => 'nullable|string',
            'status'        => 'nullable|string|in:Published,Unpublished',
            'file'          => 'nullable|file|max:20480',
            'product_id'    => 'nullable|exists:article_groups,id',
            'article_name'  => 'nullable|string|max:255',
        ]);

        $error = Error::find($request->id);

        if (!$error) {
            return response()->json(['success' => false, 'message' => 'Eintrag nicht gefunden.'], 404);
        }

        $data = [
            'error_code'    => $request->error_code,
            'problem_types' => $request->problem_types,
            'reason'        => $request->reason,
            'solution'      => $request->solution,
            'status'        => $request->status,
            'product_id'    => $request->product_id,
            'article_name'  => $request->article_name,
            'updated_at'    => now(),
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                $filename = time() . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move(public_path('uploads/errors'), $filename);
                $data['file'] = $filename;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Die Datei ist ungültig oder konnte nicht hochgeladen werden.'
                ], 422);
            }
        }

        $error->update($data);

        return response()->json(['success' => true, 'message' => 'Fehlereintrag wurde erfolgreich aktualisiert.']);
    }



     public function updateStatus(Request $request)
        {
           $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string|in:Published,Unpublished'
        ]);


            DB::table('errors')->where('id', $request->id)->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Status aktualisiert.']);
        }

   public function destroy($id)
    {
        $error = DB::table('errors')->where('id', $id)->first();

        if ($error) {
            if ($error->file && File::exists(public_path('uploads/errors/' . $error->file))) {
                File::delete(public_path('uploads/errors/' . $error->file));
            }

            DB::table('errors')->where('id', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Fehler erfolgreich gelöscht.']);
        }

        return response()->json(['success' => false, 'message' => 'Fehler nicht gefunden.'], 404);
    }



    public function addNewError(Request $request)
{
    // Validate input
      $validated = $request->validate([
        'error_code' => 'required|string',
        'problem_types' => 'nullable|string',
        'solution' => 'nullable|string',
        'reason' => 'nullable|string',
        'employee_id' => 'required|exists:employees,id',
    ]);

    // Create new error
    $error = Error::create([
        'error_code' => $validated['error_code'],
        'problem_types' => $validated['problem_types'],
        'solution' => $validated['solution'],
        'reason' => $validated['reason'],
        'employee_id' => $validated['employee_id'],
        'status' => 'Published',  
    ]);

    // Return the new error's ID for the select2 dropdown
    return response()->json([
        'success' => true,
        'id' => $error->id
    ]);
}



public function getErrorCodes(Request $request)
{
    // Check if there's a search term
    $searchTerm = $request->get('q', '');

    $query = DB::table('errors')
        ->leftJoin('article_groups', 'article_groups.id', '=', 'errors.product_id')
        ->leftJoin('brands', 'brands.id', '=', 'errors.brand_id')
        ->select('errors.id', 'errors.error_code', 'brands.name as brand_name', 'article_groups.article_group', 'errors.problem_types', 'errors.reason', 'errors.solution');
    
    // If there's a search term, filter the results
    if ($searchTerm) {
        $query->where('errors.error_code', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('brands.name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('article_groups.article_group', 'LIKE', '%' . $searchTerm . '%');
    }

    $error_codes = $query->get();

    // Map the results to the format expected by select2
    $results = $error_codes->map(function($error_code) {
        return [
            'id' => $error_code->id,
            'text' => $error_code->error_code . ' - ' . $error_code->brand_name . ' (' . $error_code->article_group . ')'
        ];
    });

    return response()->json(['results' => $results]);
}



}
