<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectFeedback;
use App\Models\ProjectAward;
use App\Models\ProjectTask;

class ProjectFeedbackController extends Controller {

    public function __construct(){
        $this->middleware('auth');
    }
    public function getFeedbacks($projectId)
    {
        $feedbacks = ProjectFeedback::where('project_id', $projectId)->latest()->get();
        return response()->json($feedbacks);
    }


    public function show($id)
    {
        $project = Project::with(['feedbacks', 'award'])->findOrFail($id);
        return view('admin.project.feedback', compact('project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'type' => 'required|in:customer,controller',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        ProjectFeedback::create([
            'project_id' => $request->project_id,
            'type' => $request->type,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'given_by' => auth()->user()->name // for controller type
        ]);

        return response()->json(['success' => true]);
    }

    public function evaluateAward(Request $request)
    {
        $project = Project::with('feedbacks')->findOrFail($request->project_id);

        $customerAvg = $project->feedbacks()->where('type', 'customer')->avg('rating');
        $controllerAvg = $project->feedbacks()->where('type', 'controller')->avg('rating');
        $deadlineMet = $project->end_date <= $project->project_timelines()->max('due_date');

        $qualified = $customerAvg >= 5 && $controllerAvg >= 5 && $deadlineMet;

        ProjectAward::updateOrCreate(
            ['project_id' => $project->id],
            [
                'qualified' => $qualified,
                'coins_awarded' => $qualified ? 10 : 0,
                'reason' => $qualified ? 'All feedbacks are 5⭐ and deadline met.' : 'Conditions not met.'
            ]
        );

        return response()->json(['success' => true, 'qualified' => $qualified]);
    }

    public function destroy($id)
    {
        $feedback = ProjectFeedback::findOrFail($id);
        $feedback->delete();
    
        return response()->json(['success' => true]);
    }


    public function controllerCheck(Request $request)
    {
        $feedback = ProjectFeedback::firstOrNew([
            'project_id' => $request->project_id,
            'phase_id' => $request->phase_id,
            'activity_id' => $request->activity_id
        ]);

        $feedback->controller_feedback = true;

        // Check if task + date + controller are all valid
        $task = ProjectTask::where('project_id', $request->project_id)
            ->where('phase_id', $request->phase_id)
            ->where('activities_id', $request->activity_id)
            ->first();

        $award = ProjectAward::where('project_id', $request->project_id)
            ->where('phase_id', $request->phase_id)
            ->where('activity_id', $request->activity_id)
            ->first();

        $qualified = false;

        if (
            $task &&
            $task->done === 'true' &&
            $task->start_date <= $award->restricted_day &&
            $task->done_date <= $award->restricted_day &&
            $feedback->controller_feedback
        ) {
            $qualified = true;
        }

        $feedback->qualified = $qualified;
        $feedback->save();

        return response()->json(['success' => true]);
    }

}
