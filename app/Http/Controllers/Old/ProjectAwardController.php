<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectAward;
use App\Models\Project;
use App\Models\ProjectFeedback;

class ProjectAwardController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
 

    public function evaluate(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'coins_awarded' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
        ]);
    
        $awardId = $request->input('award_id');
        $projectId = $request->project_id;
        $phaseId = $request->phase_id;
        $activityId = $request->activity_id;
    
        // 🔍 Check for duplicate (excluding current award if editing)
        $duplicate = ProjectAward::where('project_id', $projectId)
            ->where('phase_id', $phaseId)
            ->where('activity_id', $activityId)
            ->when($awardId, fn($q) => $q->where('id', '!=', $awardId))
            ->exists();
    
        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Diese Auszeichnung existiert bereits.'
            ], 409);
        }
    
        // ✅ Create or update
        $award = ProjectAward::updateOrCreate(
            ['id' => $awardId],
            [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'activity_id' => $activityId,
                'coins_awarded' => $request->coins_awarded,
                'restricted_day' => $request->restricted_day,
                'restricted_time' => $request->restricted_time,
                'reason' => $request->reason,
            ]
        );
    
        return response()->json(['success' => true, 'award' => $award]);
    }
    
 
    

        public function getPhaseAwards($projectId, $phaseId)
            {
                $awards = ProjectAward::with('assignedBy')
                    ->where('project_id', $projectId)
                    ->where('phase_id', $phaseId)
                    ->latest()
                    ->get()
                    ->groupBy('assigned_by')
                    ->map(function ($group) {
                        $employee = $group->first()->assignedBy;
                        return [
                            'employee' => $employee ? $employee->name . ' ' . $employee->lastname : 'Unbekannt',
                            'awards' => $group->map(function ($a) {
                                return [
                                    'id' => $a->id,
                                    'coins' => $a->coins_awarded,
                                    'date' => $a->restricted_day,
                                    'time' => $a->restricted_time,
                                    'reason' => $a->reason,
                                ];
                            }),
                        ];
                    })
                    ->values();
            
                return response()->json($awards);
            }
        

            public function store(Request $request)
            {
                $request->validate([
                    'project_id' => 'required|integer',
                    'phase_id' => 'required|integer',
                    'coins_awarded' => 'required|numeric',
                ]);
            
                // ❌ Prevent duplicate award for same project + phase
                $exists = ProjectAward::where('project_id', $request->project_id)
                    ->where('phase_id', $request->phase_id)
                    ->exists();
            
                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Für diese Projektphase wurde bereits eine Auszeichnung vergeben.'
                    ], 409); // Conflict
                }
            
                // ✅ Save award
                ProjectAward::create([
                    'project_id' => $request->project_id,
                    'phase_id' => $request->phase_id,
                    'activity_id' => $request->activity_id, // optional
                    'coins_awarded' => $request->coins_awarded,
                    'assigned_by' => auth()->user()->name,
                    'restricted_day' => $request->restricted_day,
                    'restricted_time' => $request->restricted_time,
                    'reason' => $request->reason,
                ]);
            
                return response()->json(['success' => true]);
            }
            
            
            
            public function update(Request $request, $id)
            {
                $request->validate([
                    'project_id' => 'required|exists:projects,id',
                    'phase_id' => 'required|exists:task_phases,id',
                    'activity_id' => 'nullable|exists:phase_activities,id',
                    'coins_awarded' => 'required|numeric|min:0',
                    'restricted_day' => 'nullable|date',
                    'restricted_time' => 'nullable|integer|min:0|max:23',
                    'reason' => 'nullable|string',
                ]);
            
                $award = ProjectAward::findOrFail($id);
            
                // ✅ Duplicate check with proper NULL handling
                $duplicate = ProjectAward::where('project_id', $request->project_id)
                    ->where('phase_id', $request->phase_id)
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($duplicate) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Für diese Projektphase wurde bereits eine Auszeichnung vergeben.',
                    ], 409);
                }
            
            
                // 🟢 Update safely
                $award->update([
                    'project_id' => $request->project_id,
                    'phase_id' => $request->phase_id,
                    'activity_id' => $request->activity_id,
                    'coins_awarded' => $request->coins_awarded,
                    'restricted_day' => $request->restricted_day,
                    'restricted_time' => $request->restricted_time,
                    'reason' => $request->reason,
                ]);
            
                return response()->json(['success' => true]);
            }
            
            
            public function destroy($id)
            {
                ProjectAward::destroy($id);
                return response()->json(['success' => true]);
            }
            
            public function show($id)
            {
                return ProjectAward::findOrFail($id);
            }


            public function isController(Request $request)
        {
            $isController = \DB::table('project_control_people')
                ->where('project_id', $request->project_id)
                ->where('phase_id', $request->phase_id)
                ->where(function ($q) use ($request) {
                    $q->whereNull('activity_id')
                    ->orWhere('activity_id', $request->activity_id);
                })
                ->where('employee_id', auth()->user()->employee_id)
                ->exists();

            return response()->json(['isController' => $isController]);
        }
            
    
}
