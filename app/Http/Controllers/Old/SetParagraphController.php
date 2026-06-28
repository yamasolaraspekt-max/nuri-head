<?php

namespace App\Http\Controllers;

use App\Models\SetParagraph;
use Illuminate\Http\Request;
use DB;

class SetParagraphController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($master)
    {
    
            $data['data'] = DB::table('set_paragraphs')
                    ->join('product_master_sets', 'product_master_sets.id', '=', 'set_paragraphs.master_id')
                    ->where('set_paragraphs.master_id', $master)
                    ->select('set_paragraphs.*', 'product_master_sets.setname')
                    ->paginate('20');
            $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();
            return view('admin.offer.set.paragraph.set_paragraph', $data);
  
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
        $request->validate([
            'editor_text'   =>  'required',
        ]);

        $data = new SetParagraph;
        $data->content= $request->editor_text;
        $data->status="Published";
        $data->master_id=$request->master_id;
        $data->save();


        return redirect()->back()->with('save_msg', 'The record saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(SetParagraph $setParagraph)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SetParagraph $setParagraph)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SetParagraph $setParagraph)
    {
        
        $request->validate([
            'content'   =>  'required',
        ]);
        $id=request()->input('id');
        $data =SetParagraph::find($id);
        $data->content= $request->content;
        $data->status="Published";
        $data->master_id=$request->master_id;
        $data->save();


        return redirect()->back()->with('save_msg', 'The record saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = SetParagraph::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully');
    }
}
