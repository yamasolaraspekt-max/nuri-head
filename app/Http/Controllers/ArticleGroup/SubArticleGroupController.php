<?php

namespace App\Http\Controllers\ArticleGroup;
use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\SubArticleGroup;
use Illuminate\Http\Request;
use DB;

class SubArticleGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        
        $search=request()->query('search');

        if($search){
            
            $data['data']=DB::table('sub_article_groups')
                        ->join('article_groups', 'article_groups.id', '=', 'sub_article_groups.article_group_id')
                        ->where('article_groups.id', $id)
                        ->where('sub_article_groups.sub_article', 'LIKE', "%$search%")
                        ->orWhere('article_groups.article_group', 'LIKE', "%$search%")
                        ->select('sub_article_groups.*', 'article_groups.article_group')
                        ->paginate(30);

            $data['title']=DB::table('article_groups')->where('id', $id)->select('article_group')->first();
     
            return view('admin.product.article_group.sub.sub', $data);
        }
        else{
            
            $data['data']=DB::table('sub_article_groups')
            ->join('article_groups', 'article_groups.id', '=', 'sub_article_groups.article_group_id')
            ->where('article_groups.id', $id)

            ->select('sub_article_groups.*', 'article_groups.article_group')
            ->paginate(30);

            $data['title']=DB::table('article_groups')->where('id', $id)->select('article_group')->first();

            return view('admin.product.article_group.sub.sub', $data);


        }
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
     
        $this->validate($request, [
            'sub_article' =>  'required',
        ]);

        SubArticleGroup::create($request->all());

        return redirect()->to('/sub_article/'.$request->article_group_id)->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubArticleGroup $subArticleGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubArticleGroup $subArticleGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $data = SubArticleGroup::find($id);
        $data->article_group_id= $request->article_group;
        $data->sub_article = $request->sub_article;
        $data->save();

        if($data){
        return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich gespeichert');

        }
        else{
            return redirect()->back()->with('delete_msg', 'Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = SubArticleGroup::findOrFail($id);

        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurd erfulgreich gelöscht');
    }
}
