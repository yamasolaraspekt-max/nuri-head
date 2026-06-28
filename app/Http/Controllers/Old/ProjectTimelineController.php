<?php

namespace App\Http\Controllers;

use App\Models\ProjectTimeline;
use App\Models\PhaseActivities;
use Illuminate\Http\Request; 
use App\Models\ProjectTimelineDoneDate;
use Carbon\Carbon;
use DB;


class ProjectTimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function loadTimeline($projectId)
     {
         $timelines = ProjectTimeline::with(['doneBy'])
             ->where('project_id', $projectId)
             ->get()
             ->map(function ($timeline) {
                 return [
                     'phase_id'    => $timeline->phase_id,
                     'activity_id' => $timeline->activity_id,
                     'done_range'  => $timeline->done_range ?? 0,
                     'done_by'     => $timeline->doneBy?->name ?? null,
                     'done_date'   => $timeline->done_date ? \Carbon\Carbon::parse($timeline->done_date)->format('Y-m-d') : null,
                     'start_date'  => $timeline->start_date ? \Carbon\Carbon::parse($timeline->start_date)->format('Y-m-d') : null,
                     'due_date'    => $timeline->due_date ? \Carbon\Carbon::parse($timeline->due_date)->format('Y-m-d') : null,
                 ];
             });
     
         return response()->json($timelines);
     }
     

    public function updateProgress(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'phase_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'done_range' => 'required|integer',
            'employee_id' => 'required|integer'
        ]);

        $timeline = ProjectTimeline::firstOrCreate(
            [
                'project_id' => $request->project_id,
                'phase_id' => $request->phase_id,
                'activity_id' => $request->activity_id,
            ],
            [
                'start_date' => now()
            ]
        );

        // Check last range
        $lastEntry = ProjectTimelineDoneDate::where('timeline_id', $timeline->id)
            ->latest('done_date')
            ->first();

        // Only log if changed
        if (!$lastEntry || $lastEntry->timeline_range != $request->done_range) {
            ProjectTimelineDoneDate::create([
                'project_id' => $request->project_id,
                'timeline_id' => $timeline->id,
                'done_by' => $request->employee_id,
                'done_date' => now(),
                'timeline_range' => $request->done_range,
            ]);
        }

        $timeline->done_range = $request->done_range;
        $timeline->edit_by = $request->employee_id;

        if ((int) $request->done_range === 100) {
            $timeline->is_done = 'yes';
            $timeline->done_by = $request->employee_id;
            $timeline->done_date = now();
            $timeline->date_difference = \Carbon\Carbon::parse($timeline->start_date)->diffInDays(now());
        }

        $timeline->save();

        return response()->json([
            'done_range' => $timeline->done_range,
            'done_by' => $timeline->editBy?->name ?? 'Unbekannt',
            'done_date' => $timeline->done_date?->format('Y-m-d'),
            'start_date' => $timeline->start_date?->format('Y-m-d'),
            'date_difference' => $timeline->date_difference
        ]);
    }



    public function loadHistory($projectId, $phaseId, $activityId)
        {
            $timeline = ProjectTimeline::where('project_id', $projectId)
                ->where('phase_id', $phaseId)
                ->where('activity_id', $activityId)
                ->first();

            if (!$timeline) {
                return response()->json([]);
            }

            $history = ProjectTimelineDoneDate::with('doneBy')
                ->where('timeline_id', $timeline->id)
                ->orderBy('done_date')
                ->get()
                ->map(function ($entry) {
                    return [
                        'range' => $entry->timeline_range,
                        'done_by' => $entry->doneBy?->name ?? 'Unbekannt',
                        'done_date' => \Carbon\Carbon::parse($entry->done_date)->format('Y-m-d'),
                    ];
                });

            return response()->json($history);
        }


        public function getDates($project_id, $phase_id, $activity_id)
    {
        $timeline = ProjectTimeline::where([
            'project_id' => $project_id,
            'phase_id' => $phase_id,
            'activity_id' => $activity_id
        ])->first();

        return response()->json([
            'start_date' => optional($timeline)->start_date,
            'due_date' => optional($timeline)->due_date
        ]);
    }

    public function updateDates(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'phase_id' => 'required',
            'activity_id' => 'required',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
        ]);

        $timeline = ProjectTimeline::updateOrCreate(
            [
                'project_id' => $request->project_id,
                'phase_id' => $request->phase_id,
                'activity_id' => $request->activity_id
            ],
            [
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'edit_by' => auth()->id()
            ]
        );

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'project_id'   => 'required|exists:projects,id',
        'phase_id'     => 'required|exists:task_phases,id',
        'activity_id'  => 'required|exists:phase_activities,id',
        'start_date'   => 'nullable|date',
        'due_date'     => 'nullable|date|after_or_equal:start_date',
        'is_done'      => 'nullable|string',
    ]);

    ProjectTimeline::updateOrCreate(
        [
            'project_id' => $validated['project_id'],
            'phase_id' => $validated['phase_id'],
            'activity_id' => $validated['activity_id']
        ],
        [
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'is_done' => $validated['is_done'] ?? null,
            'done_by' => auth()->id()
        ]
    );

    return redirect()->back()->with('success', 'Zeitplan aktualisiert');
}



public function ajaxBoard()
{
    $tasks = DB::table('project_timelines')
        ->leftJoin('phase_activities', 'phase_activities.id', '=', 'project_timelines.activity_id')
        ->select(
            'project_timelines.id',
            'phase_activities.title as title_activity',
            'phase_activities.description',
            'project_timelines.start_date',
            'project_timelines.due_date',
            'project_timelines.done_range'
        )
        ->get()
        ->map(function ($task) {
            $range = (int) ($task->done_range ?? 0);
            $task->status = match (true) {
                $range === 0 => 'Not started',
                $range === 100 => 'Done',
                default => 'In Progress',
            };
            return $task;
        });

    return response()->json($tasks);
}

public function getOne($id)
{
    $task = DB::table('project_timelines')
        ->select('id', 'start_date', 'due_date')
        ->where('id', $id)
        ->first();

    return response()->json($task);
}

 

    
}
