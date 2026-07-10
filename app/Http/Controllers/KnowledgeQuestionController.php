<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeQuestion;
use Illuminate\Http\Request;

class KnowledgeQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function __construct(){
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update', 'edit']);
        $this->middleware('permission:Product,delete')->only(['destroy']);
        $this->middleware('auth');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.knowledge.question.create_question')->with('id', $id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validate = $request->validate([
            'question'  =>  'required|string',
            'editor_text'   =>  'required',
            'id'  =>  'required|exists:knowledge_categories,id'
        ]);
 

        KnowledgeQuestion::create([
            'knowledge_id' =>   $request->id,
            'question'  =>  $request->question,
            'description'  =>   $request->editor_text,
            'created_by'    => auth()->user()->name,
            'video'         =>  $request->video
        ]);

        return redirect()->to('question/'.$request->id)->with('save_msg', 'The question is saved successfully');

    }


    public function update(Request $request)
    {
        
        $validate = $request->validate([
            'question'  =>  'required|string',
            'editor_text'   =>  'required',
            'knowledge_id'  =>  'required|exists:knowledge_categories,id',
            'id'  =>  'required|exists:knowledge_questions,id'
        ]);
 

        KnowledgeQuestion::find($request->id)->update([
            'knowledge_id' =>   $request->knowledge_id,
            'question'  =>  $request->question,
            'description'  =>   $request->editor_text,
            'created_by'    => auth()->user()->name,
            'video'         =>  $request->video
        ]);

        return redirect()->to('question/'.$request->knowledge_id)->with('save_msg', 'The question is saved successfully');

    }

    /**
     * Display the specified resource.
     */
    public function getQuestion($question_id)
    {
        // Debugging: Log the received ID
        \Log::info("Fetching question with ID: $question_id");

        $data = KnowledgeQuestion::find($question_id);

        if (!$data) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        return response()->json($data, 200);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($question_id)
    {
        $data = KnowledgeQuestion::find($question_id);
        return view('admin.knowledge.question.edit_question')->with('data', $data);
    }

   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($question_id)
    {
        $data = KnowledgeQuestion::find($question_id);
        $data->delete();

         return response()->json(['success', 'the question deleted successfully']);
    }
}
