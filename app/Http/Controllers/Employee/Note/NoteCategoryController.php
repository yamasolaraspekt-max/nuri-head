<?php

namespace App\Http\Controllers\Employee\Note;
use App\Http\Controllers\Controller;

use App\Models\NoteCategory;
use Illuminate\Http\Request;
use DB;

class NoteCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
        {
            $search = request()->query('search');

            // Base query excluding deleted records
            $query = DB::table('note_categories')
                        ->where('user', auth()->user()->employeeId())
                        ->whereNull('deleted_at');

            // Apply search filter if provided
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('category_name', 'LIKE', "%$search%")
                    ->orWhere('type', 'LIKE', "%$search%");
                });
            }

            // Paginate the results
            $data = $query->select('*')->paginate(20);

            // Return the view with the data
            return view('admin.notes.category.note_category')->with('data', $data);
        }


 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'category_name' =>  'required|string', 
            'type'  =>  'required|string',
            'color'  =>  'nullable|string', 
        ]);

        $data = NoteCategory::create([
            'category_name' => $request->category_name,
            'type'  =>  $request->type,
            'color' =>  $request->color,
            'user'  =>  auth()->user()->employeeId()
        ]);
        return redirect()->back()->with('save_msg', 'Hinweiskategorie erfolgreich erstellt');

    }

   public function auto_save(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        NoteCategory::create([
            'category_name' => $request->category_name,
            'type' => $request->type,
            'color' => $request->color,
            'user' => auth()->user()->employeeId(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Hinweiskategorie erfolgreich erstellt',
        ]);
    }


    public function getCategory(Request $request)
    {

        $data = DB::table('note_categories')
                    ->where('user', auth()->user()->employeeId())
                    ->get();

    return response()->json($data, 200); 
    }

 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NoteCategory $noteCategory)
    {
        $validate = $request->validate([
            'id'    =>  'required|exists:note_categories,id',
            'category_name' =>  'required|string', 
            'type'  =>  'required|string',
            'color'  =>  'nullable|string',
            'user'  =>  auth()->user()->employeeId()

        ]);

         $data = NoteCategory::find($request->id)->update([
            'category_name' => $request->category_name,
            'type'  =>  $request->type,
            'color' =>  $request->color,
            'user'  =>  auth()->user()->employeeId()
        ]);
        
        return redirect()->back()->with('save_msg', 'Hinweiskategorie erfolgreich aktualisiert');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = NoteCategory::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Hinweiskategorie erfolgreich gelöscht');

    }
}
