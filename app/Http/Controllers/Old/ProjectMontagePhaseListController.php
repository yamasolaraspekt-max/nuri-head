<?php

namespace App\Http\Controllers;

use App\Models\ProjectMontagePhaseList;
use Illuminate\Http\Request;
use DB;


class ProjectMontagePhaseListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $article = DB::table('article_groups')->get();
        
        return view('admin.checklist.checklist_create')->with('article', $article);
    }

    /**
     * Show the form for creating a new resource.
     */

      public function getPhase($article){
        $data=DB::table('task_phases')
                ->where('product_id', $article)
                ->get();
       return response()->json($data, 200);
    }


    public function getSet($article)
    {
        $data=DB::table('product_master_set')
                    ->where('status', '=', 'Published')
                    ->where('article_group', $article)
                    ->get();
       return response()->json($data, 200);
        
    }
    public function create()
    {
        //
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
    public function show(ProjectMontagePhaseList $projectMontagePhaseList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectMontagePhaseList $projectMontagePhaseList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectMontagePhaseList $projectMontagePhaseList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectMontagePhaseList $projectMontagePhaseList)
    {
        //
    }
}
