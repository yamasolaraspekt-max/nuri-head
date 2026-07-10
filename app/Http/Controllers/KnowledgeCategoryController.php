<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use DB;

class KnowledgeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['edit', 'update']);
        $this->middleware('permission:Product,delete')->only(['delete_photo', 'destroy']);
        $this->middleware('auth');
    }
    public function index()
    {
        $data = KnowledgeCategory::all();

        return view('admin.knowledge.category.index')->with('data', $data);
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
        public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/knowledge/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }
    public function store(Request $request)
    {
        $validator = $request->validate([
            'title' =>  'required|string',
            'description'  =>   'required|string',
            'icon'    =>   'required'
        ]);
      
        $data = new KnowledgeCategory;
        $data->title = $request->title;
        $data->description = $request->description;
        $data->status = "Published";
         if($request->hasFile('icon')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move('images/knowledge/', $image_name);
            $data->icon=$image_name; 
        }
        $data->save();

        return redirect()->back()->with('save_msg', 'The Knowledge Base is saved');
    }

    /**
     * Display the specified resource.
     */
    public function show(KnowledgeCategory $knowledgeCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeCategory $knowledgeCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, $id)
        {
            $knowledge = KnowledgeCategory::findOrFail($id);

            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $knowledge->title = $request->title;
            $knowledge->description = $request->description;

            if ($request->hasFile('icon')) {
                $image_name=time().'.'.$request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move('images/knowledge/', $image_name);
                $knowledge->icon = $image_name;
            }

            $knowledge->save();

            return redirect()->back()->with('success', 'Record updated successfully');
        }


    /**
     * Remove the specified resource from storage.
     */
        public function destroy($id)
        {
            $knowledge = KnowledgeCategory::findOrFail($id);
            $knowledge->delete();

            return response()->json(['message' => 'Record deleted successfully']);
        }

        public function question($id){
            $data = DB::table('knowledge_questions')
                        ->where('knowledge_id', $id)
                        ->get();

            return view('admin.knowledge.question.question')->with('data', $data);
        }

        public function search(Request $request){
            $search = request()->query('search');

            $data = DB::table('knowledge_questions')
                        ->where('question', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%")
                        ->paginate(10);
        return response()->json($data, 200);
        }
}
