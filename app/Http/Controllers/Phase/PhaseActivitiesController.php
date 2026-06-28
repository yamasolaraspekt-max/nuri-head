<?php

namespace App\Http\Controllers\Phase;
use App\Http\Controllers\Controller;

use App\Models\PhaseActivities;
use Illuminate\Http\Request;
use DB;

class PhaseActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index($id)
    {
        $activity = PhaseActivities::with([
            'departments:id,department_name',
            'positions:id,position',
            'articles:id,article_no,product',
            'taskPhase:id,phase_name',
            'productGroup:id,article_group'
        ])->findOrFail($id);
    
        $translatePhase = [
            'complete'    => 'Komplettlösung',
            'montage'     => 'Montage',
            'product'     => 'Produkt',
            'plan'        => 'Planung',
            'maintenance' => 'Wartung',
            'repair'      => 'Reparatur',
            'others'      => 'Sonstiges',
        ];
    
        return response()->json([
            'data' => [
                'id'             => $activity->id,
                'title'          => $activity->title,
                'duration'       => $activity->duration,
                'description'    => $activity->description,
                'note'           => $activity->notes,
                'status'         => $activity->status,
                'answered_by'    => $activity->answered_by,
                'product_id'     => $activity->product_id,
                'parent_id'      => $activity->parent_id,
                'phase_id'       => $activity->phase_id,
                'section_id'     => $activity->section_id,
                'section_name'   => $activity->section_name,
                'photo'          => $activity->photo,
                'initial'        => $activity->initial,
                'link'           => $activity->link,
    
                // ✅ For dropdown pre-selection
                'department_ids' => $activity->departments->pluck('id'),
                'position_ids'   => $activity->positions->pluck('id'),
                'article_ids'    => $activity->articles->pluck('id'),
    
                // ✅ For display preview (optional use in frontend)
                'departments'    => $activity->departments->pluck('department_name')->join(', '),
                'positions'      => $activity->positions->pluck('position')->join(', '),
                'articles'       => $activity->articles->pluck('product')->join(', '),
    
                'article_group'  => optional($activity->productGroup)->article_group,
                'phase_name'     => optional($activity->taskPhase)->phase_name,
            ],
            'translatePhase' => $translatePhase
        ]);
    }
    
    public function allActivity($phase_id)
    {
        $activities = PhaseActivities::with([
            'departments:id,department_name',
            'positions:id,position',
            'articles:id,product',
            'productGroup:id,article_group',
            'taskPhase:id,phase_name'
        ])
        ->where('phase_id', $phase_id)
        ->orderBy('sort_order')
        ->get();
    
        $formatted = $activities->map(function ($a) {
            return [
                'id'             => $a->id,
                'title'          => $a->title,
                'duration'       => $a->duration,
                'description'    => $a->description,
                'note'           => $a->notes,
                'status'         => $a->status,
                'answered_by'    => $a->answered_by,
                'product_id'     => $a->product_id,
                'parent_id'      => $a->parent_id,
                'phase_id'       => $a->phase_id,
                'section_id'     => $a->section_id,
                'section_name'   => $a->section_name,
                'photo'          => $a->photo,
                'initial'        => $a->initial,
    
                // ✅ These are now fixed using correct pivot table mapping
                'departments' => $a->departments->pluck('department_name')->join(', '),
                'positions'   => $a->positions->pluck('position')->join(', '),
                'articles'    => $a->articles->pluck('product')->join(', '), 
                'phase_name'     => optional($a->taskPhase)->phase_name,
                'article_group'  => optional($a->productGroup)->article_group,
            ];
        });
    
        return response()->json([
            'data' => $formatted,
            'translatePhase' => [
                'complete'    => 'Komplettlösung',
                'montage'     => 'Montage',
                'product'     => 'Produkt',
                'plan'        => 'Planung',
                'maintenance' => 'Wartung',
                'repair'      => 'Reparatur',
                'others'      => 'Sonstiges',
            ]
        ]);
    }
    

      public function ajax($id, $product)
    {  
           $taskActivities = DB::table('phase_activities')
                        ->join('task_phases', 'task_phases.id', '=', 'phase_activities.phase_id')
                        ->where('phase_activities.product_id', $product)->where('phase_activities.phase_id', $id)
                        ->select('phase_activities.*', 'task_phases.phase_name')
                        ->get();
            return response()->json($taskActivities); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function status(Request $request, $id)
    {
        $data = PhaseActivities::find($id)->update([
            'status'    =>  $request->status,
        ]);

            return response()->json(['success', 'status changed']); 

    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        \Log::info('store activity request', [$request->all()]);

        $validated = $request->validate([
            'title'         => 'required|string',
            'description'   => 'nullable|string',
            'photo'         => 'nullable|string|in:needed',
            'answered_by'   => 'required|integer',
            'phase_id'      => 'required|exists:task_phases,id',
            'product_id'    => 'required|exists:article_groups,id',
            'section_id'    => 'required|exists:phase_sections,id',
            'section_name'  => 'nullable|string',
            'parent_id'     => 'nullable|exists:phase_activities,id',
            'link'          => 'nullable|string',
            'note'          => 'nullable|string',
            'initial'       => 'nullable|string',
            'duration'      => 'nullable',

            'department_id' => 'nullable|array',
            'department_id.*' => 'exists:departments,id',

            'position_id' => 'nullable|array',
            'position_id.*' => 'exists:positions,id',

            'article_id' => 'nullable|array',
            'article_id.*' => 'exists:products,id',
        ]);

        $activity = PhaseActivities::create([
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'photo'          => $request->photo === 'needed' ? 'needed' : null,
            'answered_by'    => $validated['answered_by'],
            'phase_id'       => $validated['phase_id'],
            'product_id'     => $validated['product_id'],
            'duration'     => $validated['duration'],
            'section_id'     => $validated['section_id'],
            'section_name'   => $validated['section_name'] ?? null,
            'parent_id'      => $validated['parent_id'] ?? null,
            'link'           => $validated['link'] ?? null,
            'notes'          => $validated['note'] ?? null,
            'initial'        => $validated['initial'] ?? null,
            'duration'       => $validated['duration'] ?? null,
            'status'         => 'published'
        ]);

        // Pivot table sync
        $activity->departments()->sync($validated['department_id'] ?? []); 
        $activity->positions()->sync($validated['position_id'] ?? []);
        $activity->articles()->sync($validated['article_id'] ?? []);


        return response()->json(['success' => true]);
    }



    public function storeNewActivity(Request $request)
    {
        $request->validate([
            'phase_id'  =>  'required|exists:task_phases,id',
            'product_id'  =>  'required|exists:article_groups,id',
            'section_id'  =>  'required|exists:phase_sections,id', 
            'title' =>  'required|string',
            'description'   =>  'required|string',
            'photo'   =>  'nullable|string'
        ]);
        
        \Log::info($request);
         
            $data=new PhaseActivities;
            $data->phase_id=$request->phase_id;
            $data->product_id=$request->product_id;
            $data->section_id=$request->section_id;
            $data->position_id=$request->position_id;
            $data->section_name=$request->section_name;
            $data->initial=$request->initial;
            $data->title=$request->title;
            $data->description=$request->description;
            $data->duration=$request->duration;
            $data->photo=$request->photo;
            $data->answered_by=$request->answered_by;
            $data->status="published";
            $data->save();
            
         return response()->json(['success', 'New task is added successfully']);
       
       
    }

    /**
     * Display the specified resource.
     */
    public function show(PhaseActivities $phaseActivities)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhaseActivities $phaseActivities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    \Log::info('request activities', [$request->all()]);

    $validated = $request->validate([
        'title'         => 'required|string',
        'description'   => 'nullable|string',
        'photo'         => 'nullable|string|in:needed',
        'answered_by'   => 'required|integer',
        'phase_id'      => 'required|exists:task_phases,id',
        'product_id'    => 'required|exists:article_groups,id',
        'section_id'    => 'required|exists:phase_sections,id',
        'section_name'  => 'nullable|string',
        'parent_id'     => 'nullable|exists:phase_activities,id',
        'link'          => 'nullable|string',
        'note'          => 'nullable|string',
        'initial'       => 'nullable|string',
        'duration'      => 'nullable',

        // Multi-selects
        'department_id' => 'nullable|array',
        'department_id.*' => 'exists:departments,id',
        'position_id' => 'nullable|array',
        'position_id.*' => 'exists:positions,id',
        'article_id' => 'nullable|array',
        'article_id.*' => 'exists:products,id',
    ]);

    $activity = PhaseActivities::findOrFail($id);

    $activity->update([
        'title'          => $validated['title'],
        'description'    => $validated['description'] ?? null,
        'photo'          => $request->photo === 'needed' ? 'needed' : null,
        'answered_by'    => $validated['answered_by'],
        'phase_id'       => $validated['phase_id'],
        'product_id'     => $validated['product_id'],
        'section_id'     => $validated['section_id'],
        'section_name'   => $validated['section_name'] ?? null,
        'duration'   => $validated['duration'] ?? null,
        'parent_id'      => $validated['parent_id'] ?? null,
        'link'           => $validated['link'] ?? null,
        'notes'          => $validated['note'] ?? null,
        'initial'        => $validated['initial'] ?? null,
    ]);

    // ⛓️ Sync pivot tables
    $activity->departments()->sync($validated['department_id'] ?? []);
    $activity->positions()->sync($validated['position_id'] ?? []);
    $activity->articles()->sync($validated['article_id'] ?? []);

    return response()->json(['success' => true]);
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=PhaseActivities::find($id);
        $data->delete();

         return response()->json(['success', 'The record deleted successfully' ]); 
    }
    


public function orderTask(Request $request)
{
    $phase_id = $request->input('phase_id');
    $activity_ids = $request->input('activity_ids'); // ordered array

    foreach ($activity_ids as $index => $activity_id) {
        DB::table('phase_activities')
            ->where('phase_id', $phase_id)
            ->where('id', $activity_id)
            ->update(['sort_order' => $index + 1]);
    }

    return response()->json(['status' => 'success', 'message' => 'Activities reordered successfully']);
}


public function updateDuration(Request $request)
{
    $request->validate([
        'activity_id' => 'required|exists:phase_activities,id',
        'duration' => 'required|date_format:H:i',
    ]);

    $activity = PhaseActivities::find($request->activity_id);
    $activity->duration = $request->duration;
    $activity->save();

    return response()->json(['success' => true]);
}



}
