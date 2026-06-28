<?php

namespace App\Http\Controllers\Task;
use App\Http\Controllers\Controller;

use App\Models\PersonalTask;
use App\Models\PersonalTaskKey;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonalTaskStepController extends Controller
{
    // GET /personal-tasks/{task}/steps
    public function personalTaskStepsIndex(PersonalTask $task)
    {
        Log::info('[PTS::INDEX_STEPS] Task id='.$task->id);

        $steps = $task->steps()
            ->with(['doneBy:id,name,lastname,image'])
            ->get();

        $steps->each->append('employees');

        Log::info('[PTS::INDEX_STEPS] Count='.count($steps), [
            'step_ids' => $steps->pluck('id'),
        ]);

        return response()->json(['success' => true, 'steps' => $steps]);
    }

    // POST /personal-tasks/{task}/steps
    public function personalTaskStepsStore(Request $req, PersonalTask $task)
    {
        Log::info('[PTS::STORE_STEP] Raw request (pre-strip)', $req->all());

        foreach (['id','personal_task_id','created_at','updated_at'] as $forbid) {
            if ($req->has($forbid)) Log::warning("[PTS::STORE_STEP] Stripping forbidden field: {$forbid}");
            $req->request->remove($forbid);
        }

        $data = $req->validate([
            'title'         => 'required|string',
            'description'   => 'nullable|string',
            'duration'      => 'nullable|numeric|min:0',
            'employee_ids'  => 'nullable|array',
            'employee_ids.*'=> 'integer',
            'status'        => 'nullable|string',
            'work_progress' => 'nullable|integer|min:0|max:100',
            'same_id'       => 'nullable|integer',
            'link'          => 'nullable|string',
            'total_time'    => 'nullable|numeric|min:0',
        ]);

        $empIds = collect($data['employee_ids'] ?? [])
            ->map(fn($v)=>(int)$v)->filter()->unique()->values()->all();

        Log::info('[PTS::STORE_STEP] Normalized', [
            'task_id'      => $task->id,
            'title'        => $data['title'] ?? null,
            'duration'     => $data['duration'] ?? null,
            'status'       => $data['status'] ?? 'open',
            'work_progress'=> $data['work_progress'] ?? 0,
            'employee_ids' => $empIds,
        ]);

        $step = DB::transaction(function () use ($task, $data, $empIds) {
            $payload = [
                'task'            => $data['title'],
                'key_description' => $data['description'] ?? null,
                'duration'        => $data['duration'] ?? null,
                'employee_id'     => $empIds,
                'status'          => $data['status'] ?? 'open',
                'work_progress'   => $data['work_progress'] ?? 0,
                'same_id'         => $data['same_id'] ?? null,
                'link'            => $data['link'] ?? null,
                'total_time'      => $data['total_time'] ?? $data['duration'] ?? null,
            ];

            Log::info('[PTS::STORE_STEP] INSERT personal_task_keys (payload)', $payload);
            $step = $task->steps()->create($payload);
            Log::info('[PTS::STORE_STEP] INSERTED step id='.$step->id.' for personal_task_id='.$task->id);

            if (!empty($empIds)) {
                Log::info('[PTS::STORE_STEP] Upserting task pivots from step employees', ['task_id'=>$task->id,'empIds'=>$empIds]);
                $this->upsertTaskPivots($task->id, $empIds);
            }

            return $step;
        });

        // Read-back: this step + pivots for visibility
        $readBackStep = PersonalTaskKey::query()->find($step->id);
        $pivotAfter   = DB::table('employees_personal_tasks')->where('task_id', $task->id)->get();

        Log::info('[PTS::STORE_STEP] READ-BACK step', $readBackStep?->toArray() ?? []);
        Log::info('[PTS::STORE_STEP] READ-BACK pivots (count='.count($pivotAfter).')', [
            'rows' => $pivotAfter->map(fn($r)=>(array)$r)->all()
        ]);

        $step->load('doneBy:id,name,lastname,image')->append('employees');

        return response()->json(['success' => true, 'step' => $step]);
    }

    // PUT /personal-tasks/steps/{step}
    public function personalTaskStepsUpdate(Request $req, PersonalTaskKey $step)
    {
        Log::info('[PTS::UPDATE_STEP] Raw request for step id='.$step->id, $req->all());

        $data = $req->validate([
            'title'         => 'sometimes|required|string',
            'description'   => 'nullable|string',
            'duration'      => 'nullable|numeric|min:0',
            'employee_ids'  => 'nullable|array',
            'employee_ids.*'=> 'integer',
            'status'        => 'nullable|string',
            'is_completed'  => 'nullable|integer',
            'done_by'       => 'nullable|integer',
            'done_date'     => 'nullable|date',
            'done_status'   => 'nullable|string',
            'work_progress' => 'nullable|integer|min:0|max:100',
            'link'          => 'nullable|string',
            'total_time'    => 'nullable|numeric|min:0',
            'reason'        => 'nullable|string',
        ]);

        $empIds = array_key_exists('employee_ids', $data)
            ? collect($data['employee_ids'])->map(fn($v)=>(int)$v)->filter()->unique()->values()->all()
            : (array) ($step->employee_id ?? []);

        Log::info('[PTS::UPDATE_STEP] Normalized', [
            'step_id'      => $step->id,
            'personal_task_id' => $step->personal_task_id,
            'new_employee_ids' => $empIds,
        ]);

        DB::transaction(function () use ($step, $data, $empIds) {
            $payload = [
                'task'            => $data['title']          ?? $step->task,
                'key_description' => $data['description']    ?? $step->key_description,
                'duration'        => $data['duration']       ?? $step->duration,
                'employee_id'     => $empIds,
                'status'          => $data['status']         ?? $step->status,
                'is_completed'    => $data['is_completed']   ?? $step->is_completed,
                'done_by'         => $data['done_by']        ?? $step->done_by,
                'done_date'       => $data['done_date']      ?? $step->done_date,
                'done_status'     => $data['done_status']    ?? $step->done_status,
                'work_progress'   => $data['work_progress']  ?? $step->work_progress,
                'link'            => $data['link']           ?? $step->link,
                'total_time'      => $data['total_time']     ?? $step->total_time,
                'reason'          => $data['reason']         ?? $step->reason,
            ];
            Log::info('[PTS::UPDATE_STEP] UPDATE payload', $payload);
            $step->fill($payload)->save();

            Log::info('[PTS::UPDATE_STEP] Upserting task pivots', [
                'task_id' => (int) $step->personal_task_id,
                'empIds'  => $empIds
            ]);
            $this->upsertTaskPivots((int) $step->personal_task_id, $empIds);
        });

        // Read-back
        $fresh = PersonalTaskKey::query()->find($step->id);
        $pivots = DB::table('employees_personal_tasks')->where('task_id', $step->personal_task_id)->get();

        Log::info('[PTS::UPDATE_STEP] READ-BACK step', $fresh?->toArray() ?? []);
        Log::info('[PTS::UPDATE_STEP] READ-BACK pivots (count='.count($pivots).')', [
            'rows' => $pivots->map(fn($r)=>(array)$r)->all()
        ]);

        $step->load('doneBy:id,name,lastname,image')->append('employees');

        return response()->json(['success' => true, 'step' => $step]);
    }

    // DELETE /personal-tasks/steps/{step}
    public function personalTaskStepsDestroy(PersonalTaskKey $step)
    {
        Log::warning('[PTS::DELETE_STEP] Deleting step id='.$step->id.' (task='.$step->personal_task_id.')');
        $step->delete();
        return response()->json(['success' => true]);
    }

    // POST /personal-tasks/steps/{step}/employees/sync
    public function personalTaskStepsSyncEmployees(Request $req, PersonalTaskKey $step)
    {
        Log::info('[PTS::SYNC_STEP_EMP] Raw request for step id='.$step->id, $req->all());

        $data = $req->validate([
            'employee_ids'   => 'nullable|array',
            'employee_ids.*' => 'integer',
        ]);

        $empIds = collect($data['employee_ids'] ?? [])
            ->map(fn($v)=>(int)$v)->filter()->unique()->values()->all();

        DB::transaction(function () use ($step, $empIds) {
            Log::info('[PTS::SYNC_STEP_EMP] Saving step employees', [
                'step_id' => $step->id,
                'empIds'  => $empIds
            ]);
            $step->employee_id = $empIds;
            $step->save();

            Log::info('[PTS::SYNC_STEP_EMP] Upserting task pivots', [
                'task_id' => (int) $step->personal_task_id,
                'empIds'  => $empIds
            ]);
            $this->upsertTaskPivots((int) $step->personal_task_id, $empIds);
        });

        // Read-back
        $fresh = PersonalTaskKey::query()->find($step->id);
        $pivots = DB::table('employees_personal_tasks')->where('task_id', $step->personal_task_id)->get();

        Log::info('[PTS::SYNC_STEP_EMP] READ-BACK step', $fresh?->toArray() ?? []);
        Log::info('[PTS::SYNC_STEP_EMP] READ-BACK pivots (count='.count($pivots).')', [
            'rows' => $pivots->map(fn($r)=>(array)$r)->all()
        ]);

        $step->load('doneBy:id,name,lastname,image')->append('employees');

        return response()->json(['success' => true, 'step' => $step]);
    }

    // === PIVOT UPSERT (with logs)
    private function upsertTaskPivots(int $taskId, array $employeeIds): void
    {
        $clean = collect($employeeIds)->map(fn($v)=>(int)$v)->filter()->unique()->values();
        Log::info('[PTS::UPSERT_PIVOTS] Start', ['task_id'=>$taskId,'incoming'=>$employeeIds,'clean'=>$clean->all()]);
        if ($clean->isEmpty()) {
            Log::info('[PTS::UPSERT_PIVOTS] Nothing to insert (empty list).');
            return;
        }

        $existing = DB::table('employees_personal_tasks')
            ->where('task_id', $taskId)
            ->pluck('employee_id')
            ->map(fn($v)=>(int)$v);

        $toInsert = $clean->diff($existing);
        Log::info('[PTS::UPSERT_PIVOTS] Existing', ['existing'=>$existing->all()]);
        Log::info('[PTS::UPSERT_PIVOTS] ToInsert', ['toInsert'=>$toInsert->all()]);

        if ($toInsert->isNotEmpty()) {
            $rows = $toInsert->map(fn($eid)=>[
                'task_id'     => $taskId,
                'employee_id' => $eid,
                'status'      => 'accept',
                'created_at'  => now(),
                'updated_at'  => now(),
            ])->all();

            DB::table('employees_personal_tasks')->insert($rows);
            Log::info('[PTS::UPSERT_PIVOTS] Inserted rows', $rows);
        } else {
            Log::info('[PTS::UPSERT_PIVOTS] No new rows to insert.');
        }
    }
}
