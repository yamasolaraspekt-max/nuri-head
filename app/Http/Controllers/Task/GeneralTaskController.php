<?php

namespace App\Http\Controllers\Task;

use App\Events\GeneralTaskChanged;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Task\Concerns\HandlesGeneralTaskSteps;
use App\Models\Department;
use App\Models\Employee;
use App\Models\GeneralTask;
use App\Models\GeneralTaskStep;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GeneralTaskController extends Controller
{
    use HandlesGeneralTaskSteps;

    public function index(Request $request)
    {
        $authEmployeeId = (int) auth()->user()->name;

        $employee = Employee::with('departments')->find($authEmployeeId);

        $myDepartmentIds = $employee
            ? $employee->departments()->pluck('departments.id')->all()
            : [];

        $query = GeneralTask::with($this->taskRelations())
            ->where(function ($q) use ($authEmployeeId, $myDepartmentIds) {
                $q->where('visibility', 'all')
                    ->orWhere(function ($q) use ($myDepartmentIds) {
                        $q->where('visibility', 'department');

                        if (!empty($myDepartmentIds)) {
                            $q->whereIn('department_id', $myDepartmentIds);
                        }
                    })
                    ->orWhereHas('assignees', function ($a) use ($authEmployeeId) {
                        $a->where('employees.id', $authEmployeeId);
                    })
                    ->orWhereHas('steps.assignees', function ($a) use ($authEmployeeId) {
                        $a->where('employees.id', $authEmployeeId);
                    })
                    ->orWhere('created_by', $authEmployeeId)
                    ->orWhere('claimed_by', $authEmployeeId);
            });

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('reports', function ($r) use ($search) {
                        $r->where('body', 'like', "%{$search}%");
                    })
                    ->orWhereHas('steps', function ($s) use ($search) {
                        $s->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignees', function ($a) use ($search) {
                        $a->where(DB::raw("CONCAT(COALESCE(name,''),' ',COALESCE(lastname,''))"), 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $this->applyPriorityOrdering($query)->get();

        $dependencyOptions = $tasks
            ->whereNotIn('status', ['archived'])
            ->sortBy(function ($task) {
                return [$this->priorityRank($task->priority ?? 'normal'), (int) ($task->sort_order ?? 999999), mb_strtolower($task->title ?? '')];
            })
            ->values();

        $archivedTasks = GeneralTask::with($this->taskRelations())
            ->onlyTrashed()
            ->latest()
            ->get()
            ->merge($tasks->where('status', 'archived'));

        $recurringNotificationTasks = $tasks
            ->sortBy(function ($task) {
                return [$this->priorityRank($task->priority ?? 'normal'), (int) ($task->sort_order ?? 999999), $task->due_at ? Carbon::parse($task->due_at)->timestamp : PHP_INT_MAX];
            })
            ->filter(function ($task) {
                return (bool) ($task->is_recurring ?? false)
                    && !in_array($task->status, ['archived'], true)
                    && !empty($task->due_at)
                    && Carbon::parse($task->due_at)->lte(now()->addDays(14)->endOfDay());
            })
            ->sortBy('due_at')
            ->values();

        return view('admin.general_tasks.index', [
            'tasks' => $tasks,
            'archivedTasks' => $archivedTasks,
            'recurringNotificationTasks' => $recurringNotificationTasks,
            'dependencyOptions' => $dependencyOptions,
            'departments' => Department::orderBy('department_name')->get(),
            'employees' => Employee::where('status', 'Active')
                ->orderBy('name')
                ->orderBy('lastname')
                ->get(),
            'activeEmployees' => Employee::with('mainDepartmentPosition.department')
                ->where('status', 'Active')
                ->orderBy('name')
                ->orderBy('lastname')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $data = $this->taskData($validated);
        $data = $this->normalizeRecurringData($request, $data);
        $data['created_by'] = (int) auth()->user()->name;
        $data['task_mode'] = $request->input('task_mode') === 'bulk' ? 'bulk' : 'single';
        $data['progress_percent'] = 0;
        $data['soll_minutes'] = 0;
        $data['ist_minutes'] = 0;

        $task = DB::transaction(function () use ($request, $data) {
            $task = GeneralTask::create($data);

            $this->syncTaskDependencies($task, $this->requestedDependencyIds($request));
            $this->syncTaskSteps($task, $request);

            return $task->fresh($this->taskRelations());
        });

        broadcast(new GeneralTaskChanged(
            $task,
            'Eine neue Aufgabe wurde erstellt.',
            'created'
        ))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Aufgabe wurde gespeichert.',
                'task' => $task,
            ]);
        }

        return back()->with('save_msg', 'Aufgabe wurde gespeichert.');
    }

    public function update(Request $request, GeneralTask $generalTask)
    {
        $request->validate([
            'change_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated = $this->validated($request);
        $data = $this->taskData($validated);
        $data = $this->normalizeRecurringData($request, $data);
        $data['task_mode'] = $request->input('task_mode') === 'bulk' ? 'bulk' : 'single';

        $task = DB::transaction(function () use ($request, $generalTask, $data) {
            $generalTask->update($data);

            $this->syncTaskDependencies($generalTask, $this->requestedDependencyIds($request));
            $this->syncTaskSteps($generalTask, $request);

            $this->logTaskChangeReason(
                $generalTask,
                $request->input('change_reason'),
                'Aufgabe bearbeitet'
            );

            return $generalTask->fresh($this->taskRelations());
        });

        broadcast(new GeneralTaskChanged(
            $task,
            'Eine Aufgabe wurde bearbeitet.',
            'updated'
        ))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Aufgabe wurde aktualisiert.',
                'task' => $task,
            ]);
        }

        return back()->with('update_msg', 'Aufgabe wurde aktualisiert.');
    }

    public function claim(Request $request, GeneralTask $generalTask)
    {
        $employeeId = (int) auth()->user()->name;

        $data = $request->validate([
            'planned_hours_today' => ['nullable', 'numeric', 'min:0'],
            'due_at' => ['nullable', 'date'],
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $updates = [
            'status' => 'in_progress',
            'claimed_by' => $employeeId,
        ];

        if (!empty($data['planned_hours_today'])) {
            $updates['planned_hours_today'] = $data['planned_hours_today'];
        }

        if (!empty($data['due_at'])) {
            $updates['due_at'] = $data['due_at'];
        }

        $generalTask->update($updates);
        $generalTask->assignees()->syncWithoutDetaching([$employeeId]);
        $this->ensureDefaultSingleStep($generalTask);
        $this->logTaskChangeReason($generalTask, $data['note'], 'Aufgabe übernommen');

        $task = $generalTask->fresh($this->taskRelations());

        broadcast(new GeneralTaskChanged(
            $task,
            'Eine Aufgabe wurde übernommen.'
        ))->toOthers();

        return response()->json([
            'message' => 'Aufgabe wurde übernommen.',
            'task' => $task,
        ]);
    }

    public function move(Request $request)
    {
        $data = $request->validate([
            'task_id' => ['required', 'exists:general_tasks,id'],
            'status' => ['required', 'in:open,in_progress,review,done,archived'],
            'claim' => ['nullable', 'boolean'],
            'planned_hours_today' => ['nullable', 'numeric', 'min:0'],
            'due_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
            'change_reason' => ['nullable', 'string', 'max:2000'],
            'step_checks' => ['nullable', 'array'],
            'step_checks.*.id' => ['nullable', 'integer', 'exists:general_task_steps,id'],
            'step_checks.*.step_id' => ['nullable', 'integer', 'exists:general_task_steps,id'],
            'step_checks.*.is_done' => ['nullable', 'boolean'],
            'step_checks.*.ist_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'step_checks.*.actual_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'step_checks.*.reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $task = GeneralTask::with($this->taskRelations())->findOrFail($data['task_id']);
        $employeeId = (int) auth()->user()->name;

        DB::transaction(function () use ($task, $data, $request, $employeeId) {
            $updates = [
                'status' => $data['status'],
            ];

            if (
                ($data['claim'] ?? false)
                || ($task->status === 'open' && $data['status'] === 'in_progress' && empty($task->claimed_by))
            ) {
                $updates['claimed_by'] = $employeeId;

                if (!empty($data['planned_hours_today'])) {
                    $updates['planned_hours_today'] = $data['planned_hours_today'];
                }

                if (!empty($data['due_at'])) {
                    $updates['due_at'] = $data['due_at'];
                }

                $task->assignees()->syncWithoutDetaching([$employeeId]);
            }

            if ($data['status'] === 'done') {
                $updates['completed_at'] = now();
            } else {
                $updates['completed_at'] = null;
            }

            if ($data['status'] === 'archived') {
                $updates['archived_at'] = now();
            } else {
                $updates['archived_at'] = null;
            }

            $task->update($updates);

            $reason = $data['change_reason'] ?? $data['note'] ?? null;
            $this->logTaskChangeReason($task, $reason, 'Status geändert: ' . $data['status']);
            $this->applyStepChecksFromRequest($task, $request);
        });

        $nextTask = null;

        if ($data['status'] === 'done') {
            $nextTask = $this->createNextRecurringOccurrence($task->fresh(['assignees', 'dependsOn', 'steps.assignees']));
        }

        $freshTask = $task->fresh($this->taskRelations());

        broadcast(new GeneralTaskChanged(
            $freshTask,
            'Aufgabenstatus wurde geändert.',
            'moved'
        ))->toOthers();

        return response()->json([
            'message' => $nextTask
                ? 'Aufgabe wurde aktualisiert. Die nächste Wiederholung wurde erstellt.'
                : 'Aufgabe wurde aktualisiert.',
            'task' => $freshTask,
            'next_task' => $nextTask,
        ]);
    }

    public function archive(Request $request, GeneralTask $generalTask)
    {
        $data = $request->validate([
            'change_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $generalTask->update([
            'status' => 'archived',
            'archived_at' => now(),
            'completed_at' => null,
        ]);

        $this->logTaskChangeReason($generalTask, $data['change_reason'] ?? null, 'Aufgabe archiviert');

        $task = $generalTask->fresh($this->taskRelations());

        broadcast(new GeneralTaskChanged(
            $task,
            'Eine Aufgabe wurde archiviert.',
            'archived'
        ))->toOthers();

        return response()->json([
            'message' => 'Aufgabe wurde archiviert.',
            'task' => $task,
        ]);
    }

    public function destroy(Request $request, GeneralTask $generalTask)
    {
        $data = $request->validate([
            'change_reason' => ['nullable', 'string', 'max:2000'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = $data['change_reason'] ?? $data['reason'] ?? null;
        $employeeId = (int) auth()->user()->name;

        DB::transaction(function () use ($generalTask, $reason, $employeeId) {
            $generalTask->reports()->create([
                'employee_id' => $employeeId,
                'type' => 'comment',
                'body' => trim((string) $reason) !== ''
                    ? 'Aufgabe gelöscht / in Papierkorb verschoben: ' . trim((string) $reason)
                    : 'Aufgabe gelöscht / in Papierkorb verschoben.',
            ]);

            $generalTask->updateQuietly([
                'status' => 'archived',
                'archived_at' => now(),
                'completed_at' => null,
            ]);

            $generalTask->delete();
        });

        broadcast(new GeneralTaskChanged(
            GeneralTask::withTrashed()->whereKey($generalTask->id)->first() ?: $generalTask,
            'Eine Aufgabe wurde gelöscht.',
            'deleted'
        ))->toOthers();

        return response()->json([
            'message' => 'Aufgabe wurde gelöscht und in den Papierkorb verschoben.',
        ]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:open,in_progress,review,done,archived'],
            'ordered_task_ids' => ['nullable', 'array'],
            'ordered_task_ids.*' => ['integer', 'exists:general_tasks,id'],
            'orders' => ['nullable', 'array'],
            'orders.*.id' => ['required_with:orders', 'integer', 'exists:general_tasks,id'],
            'orders.*.status' => ['nullable', 'in:open,in_progress,review,done,archived'],
            'orders.*.sort_order' => ['nullable', 'integer', 'min:1'],
        ]);

        if (!Schema::hasColumn('general_tasks', 'sort_order')) {
            return response()->json([
                'message' => 'Die Spalte general_tasks.sort_order fehlt. Bitte zuerst die Migration ausführen.',
            ], 422);
        }

        $employeeId = (int) auth()->user()->name;
        $status = $data['status'] ?? null;
        $updated = 0;

        DB::transaction(function () use ($data, $status, &$updated) {
            if (!empty($data['orders'])) {
                foreach ($data['orders'] as $row) {
                    $updates = [
                        'sort_order' => (int) ($row['sort_order'] ?? 1),
                    ];

                    if (!empty($row['status'])) {
                        $updates['status'] = $row['status'];
                    }

                    GeneralTask::whereKey((int) $row['id'])->update($updates);
                    $updated++;
                }

                return;
            }

            $ids = collect($data['ordered_task_ids'] ?? [])
                ->filter(fn($id) => $id !== null && $id !== '')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            foreach ($ids as $index => $taskId) {
                $updates = [
                    'sort_order' => $index + 1,
                ];

                if ($status) {
                    $updates['status'] = $status;
                }

                GeneralTask::whereKey($taskId)->update($updates);
                $updated++;
            }
        });

        return response()->json([
            'message' => 'Reihenfolge wurde gespeichert.',
            'updated' => $updated,
        ]);
    }


    public function card(GeneralTask $generalTask)
    {
        $task = $generalTask->fresh($this->taskRelations());

        $statusLabels = [
            'open' => 'Offen',
            'in_progress' => 'In Bearbeitung',
            'review' => 'Zur Prüfung',
            'done' => 'Erledigt',
            'archived' => 'Archiviert',
        ];

        $priorityLabels = [
            'low' => 'Niedrig',
            'normal' => 'Normal',
            'important' => 'Wichtig',
            'urgent' => 'Dringend',
        ];

        return response()->json([
            'html' => view('admin.general_tasks.partials.card', compact('task', 'statusLabels', 'priorityLabels'))->render(),
            'task' => $task,
        ]);
    }

    public function reports(GeneralTask $generalTask)
    {
        return response()->json([
            'reports' => $generalTask->reports()
                ->with('employee')
                ->latest()
                ->get()
                ->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'employee_name' => trim(($report->employee->name ?? '') . ' ' . ($report->employee->lastname ?? '')),
                        'type' => $report->type,
                        'body' => $report->body,
                        'hours' => $report->hours,
                        'created_at' => optional($report->created_at)->format('d.m.Y H:i'),
                    ];
                }),
        ]);
    }

    public function storeReport(Request $request, GeneralTask $generalTask)
    {
        $data = $request->validate([
            'type' => ['required', 'in:comment,report'],
            'body' => ['required', 'string'],
            'hours' => ['nullable', 'numeric', 'min:0'],
        ]);

        $generalTask->reports()->create([
            'employee_id' => (int) auth()->user()->name,
            'type' => $data['type'],
            'body' => $data['body'],
            'hours' => $data['hours'] ?? null,
        ]);

        $task = $generalTask->fresh($this->taskRelations());

        broadcast(new GeneralTaskChanged(
            $task,
            'Ein neuer Kommentar/Bericht wurde hinzugefügt.'
        ))->toOthers();

        return response()->json([
            'message' => 'Kommentar/Bericht wurde gespeichert.',
            'task' => $task,
        ]);
    }

    public function restore(Request $request, $id)
    {
        $data = $request->validate([
            'change_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $task = GeneralTask::withTrashed()->findOrFail($id);

        if (method_exists($task, 'restore')) {
            $task->restore();
        }

        $task->update([
            'status' => 'open',
            'archived_at' => null,
            'completed_at' => null,
        ]);

        $this->logTaskChangeReason($task, $data['change_reason'] ?? null, 'Aufgabe wiederhergestellt');

        $freshTask = $task->fresh($this->taskRelations());

        broadcast(new GeneralTaskChanged(
            $freshTask,
            'Eine Aufgabe wurde wiederhergestellt.'
        ))->toOthers();

        return response()->json([
            'message' => 'Aufgabe wurde wiederhergestellt.',
            'task' => $freshTask,
        ]);
    }

    public function storeDependency(Request $request, GeneralTask $generalTask)
    {
        $dependsOnTaskId = (int) (
            $request->input('depends_on_task_id')
            ?: $request->input('dependency_id')
            ?: $request->input('dependency_parent_id')
            ?: $request->input('parent_task_id')
        );

        if ($dependsOnTaskId <= 0) {
            return response()->json([
                'message' => 'Bitte wähle eine Vorgänger-Aufgabe aus.',
            ], 422);
        }

        $request->merge(['depends_on_task_id' => $dependsOnTaskId]);

        $data = $request->validate([
            'depends_on_task_id' => ['required', 'integer', 'exists:general_tasks,id'],
            'change_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $dependsOnTaskId = (int) $data['depends_on_task_id'];

        if ($dependsOnTaskId === (int) $generalTask->id) {
            return response()->json([
                'message' => 'Eine Aufgabe kann nicht von sich selbst abhängig sein.',
            ], 422);
        }

        if ($this->wouldCreateCircularDependency((int) $generalTask->id, $dependsOnTaskId)) {
            return response()->json([
                'message' => 'Diese Abhängigkeit würde eine Schleife erzeugen.',
            ], 422);
        }

        $this->insertDependency((int) $generalTask->id, $dependsOnTaskId);

        $this->logTaskChangeReason($generalTask, $data['change_reason'] ?? null, 'Abhängigkeit erstellt');

        $task = $generalTask->fresh($this->taskRelations());

        broadcast(new GeneralTaskChanged(
            $task,
            'Eine Aufgaben-Abhängigkeit wurde erstellt.'
        ))->toOthers();

        return response()->json([
            'message' => 'Abhängigkeit wurde erstellt.',
            'task' => $task,
        ]);
    }

    public function destroyDependency(Request $request, GeneralTask $generalTask, GeneralTask $dependencyTask)
    {
        $data = $request->validate([
            'change_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        [$childColumn, $parentColumn] = $this->dependencyColumns();

        DB::table('general_task_dependencies')
            ->where($childColumn, $generalTask->id)
            ->where($parentColumn, $dependencyTask->id)
            ->delete();

        $this->logTaskChangeReason($generalTask, $data['change_reason'] ?? null, 'Abhängigkeit entfernt');

        $task = $generalTask->fresh($this->taskRelations());

        broadcast(new GeneralTaskChanged(
            $task,
            'Eine Aufgaben-Abhängigkeit wurde entfernt.'
        ))->toOthers();

        return response()->json([
            'message' => 'Abhängigkeit wurde entfernt.',
            'task' => $task,
        ]);
    }

    private function validated(Request $request): array
    {
        $rules = array_merge([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,normal,important,urgent'],
            'visibility' => ['required', 'in:all,department,specific'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'due_at' => ['nullable', 'date'],
            'planned_hours_today' => ['nullable', 'numeric', 'min:0'],
            'show_due_datetime' => ['nullable', 'boolean'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurrence_frequency' => ['nullable', 'in:daily,weekly,biweekly,monthly,quarterly,semiannual,yearly'],
            'recurrence_weekday' => ['nullable', 'integer', 'between:0,6'],
            'recurrence_ends_at' => ['nullable', 'date', 'after_or_equal:due_at'],
            'assignee_ids' => ['nullable', 'array'],
            'assignee_ids.*' => ['nullable', 'integer', 'exists:employees,id'],
            'dependency_ids' => ['nullable', 'array'],
            'dependency_ids.*' => ['nullable', 'integer', 'exists:general_tasks,id'],
            'dependency_parent_ids' => ['nullable', 'array'],
            'dependency_parent_ids.*' => ['nullable', 'integer', 'exists:general_tasks,id'],
            'depends_on_task_ids' => ['nullable', 'array'],
            'depends_on_task_ids.*' => ['nullable', 'integer', 'exists:general_tasks,id'],
            'parent_task_ids' => ['nullable', 'array'],
            'parent_task_ids.*' => ['nullable', 'integer', 'exists:general_tasks,id'],
        ], $this->stepValidationRules());

        if ($request->boolean('is_recurring')) {
            $rules['due_at'] = ['required', 'date'];
            $rules['recurrence_frequency'] = ['required', 'in:daily,weekly,biweekly,monthly,quarterly,semiannual,yearly'];
        }

        return $request->validate($rules);
    }

    private function taskData(array $validated): array
    {
        return Arr::only($validated, [
            'title',
            'description',
            'priority',
            'visibility',
            'department_id',
            'due_at',
            'planned_hours_today',
            'show_due_datetime',
            'is_recurring',
            'recurrence_frequency',
            'recurrence_weekday',
            'recurrence_ends_at',
            'task_mode',
        ]);
    }

    private function normalizeRecurringData(Request $request, array $data): array
    {
        $data['show_due_datetime'] = $request->boolean('show_due_datetime');

        $isRecurring = $request->boolean('is_recurring');
        $data['is_recurring'] = $isRecurring;

        if (!$isRecurring) {
            $data['recurrence_frequency'] = null;
            $data['recurrence_weekday'] = null;
            $data['recurrence_ends_at'] = null;
            return $data;
        }

        $frequency = $request->input('recurrence_frequency', 'weekly');

        $data['recurrence_frequency'] = $frequency;
        $data['recurrence_weekday'] = in_array($frequency, ['weekly', 'biweekly'], true)
            ? $request->input('recurrence_weekday')
            : null;
        $data['recurrence_ends_at'] = $request->input('recurrence_ends_at') ?: null;

        return $data;
    }

    private function createNextRecurringOccurrence(GeneralTask $task): ?GeneralTask
    {
        if (!$task->is_recurring || !$task->due_at || !$task->recurrence_frequency) {
            return null;
        }

        if (GeneralTask::where('recurrence_generated_from_id', $task->id)->exists()) {
            return null;
        }

        $nextDueAt = $this->calculateNextDueAt($task);

        if (!$nextDueAt) {
            return null;
        }

        if ($task->recurrence_ends_at && $nextDueAt->gt(Carbon::parse($task->recurrence_ends_at))) {
            return null;
        }

        $task->loadMissing(['assignees', 'dependsOn', 'steps.assignees']);

        $nextTask = $task->replicate([
            'status',
            'claimed_by',
            'completed_at',
            'archived_at',
            'deleted_at',
            'created_at',
            'updated_at',
            'progress_percent',
            'soll_minutes',
            'ist_minutes',
        ]);

        $nextTask->status = 'open';
        $nextTask->claimed_by = null;
        $nextTask->completed_at = null;
        $nextTask->archived_at = null;
        $nextTask->due_at = $nextDueAt;
        $nextTask->progress_percent = 0;
        $nextTask->soll_minutes = 0;
        $nextTask->ist_minutes = 0;
        $nextTask->recurrence_parent_id = $task->recurrence_parent_id ?: $task->id;
        $nextTask->recurrence_generated_from_id = $task->id;
        $nextTask->save();

        $nextTask->assignees()->sync($task->assignees->pluck('id')->all());

        $nextTask->dependsOn()->sync($task->dependsOn->pluck('id')->mapWithKeys(function ($id) {
            return [
                $id => [
                    'type' => 'finish_to_start',
                    'lag_days' => 0,
                    'created_by' => (int) auth()->user()->name,
                ],
            ];
        })->all());

        foreach ($task->steps as $step) {
            $newStep = $nextTask->steps()->create([
                'title' => $step->title,
                'description' => $step->description,
                'due_at' => $step->due_at,
                'sort_order' => $step->sort_order,
                'soll_minutes' => (int) ($step->soll_minutes ?? 0),
                'ist_minutes' => 0,
                'is_done' => false,
                'checked_by' => null,
                'checked_at' => null,
                'created_by' => (int) auth()->user()->name,
            ]);

            $newStep->assignees()->sync($step->assignees->pluck('id')->all());
        }

        $this->recalculateTaskStepProgress($nextTask);

        $nextTask->reports()->create([
            'employee_id' => (int) auth()->user()->name,
            'type' => 'comment',
            'body' => 'Automatisch aus wiederkehrender Aufgabe erstellt.',
        ]);

        return $nextTask->fresh($this->taskRelations());
    }

    private function calculateNextDueAt(GeneralTask $task): ?Carbon
    {
        $current = Carbon::parse($task->due_at);
        $next = $current->copy();

        match ($task->recurrence_frequency) {
            'daily' => $next->addDay(),
            'weekly' => $next->addWeek(),
            'biweekly' => $next->addWeeks(2),
            'monthly' => $next->addMonthNoOverflow(),
            'quarterly' => $next->addMonthsNoOverflow(3),
            'semiannual' => $next->addMonthsNoOverflow(6),
            'yearly' => $next->addYearNoOverflow(),
            default => null,
        };

        if (in_array($task->recurrence_frequency, ['weekly', 'biweekly'], true) && $task->recurrence_weekday !== null) {
            $targetDay = (int) $task->recurrence_weekday;
            $diff = ($targetDay - (int) $next->dayOfWeek + 7) % 7;
            $next->addDays($diff);
        }

        return $next;
    }

    private function requestedDependencyIds(Request $request): array
    {
        return collect()
            ->merge((array) $request->input('dependency_ids', []))
            ->merge((array) $request->input('dependency_parent_ids', []))
            ->merge((array) $request->input('depends_on_task_ids', []))
            ->merge((array) $request->input('parent_task_ids', []))
            ->filter(fn($id) => $id !== null && $id !== '')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function syncTaskDependencies(GeneralTask $task, array $dependencyIds): void
    {
        $dependencyIds = collect($dependencyIds)
            ->filter(fn($id) => $id !== null && $id !== '')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->reject(fn($id) => $id === (int) $task->id)
            ->values();

        [$childColumn, $parentColumn] = $this->dependencyColumns();

        DB::transaction(function () use ($task, $dependencyIds, $childColumn, $parentColumn) {
            DB::table('general_task_dependencies')
                ->where($childColumn, $task->id)
                ->delete();

            foreach ($dependencyIds as $dependencyId) {
                if ($this->wouldCreateCircularDependency((int) $task->id, $dependencyId)) {
                    continue;
                }

                $this->insertDependency((int) $task->id, $dependencyId, $childColumn, $parentColumn);
            }
        });
    }

    private function insertDependency(int $taskId, int $dependsOnTaskId, ?string $childColumn = null, ?string $parentColumn = null): void
    {
        [$detectedChildColumn, $detectedParentColumn] = $this->dependencyColumns();
        $childColumn = $childColumn ?: $detectedChildColumn;
        $parentColumn = $parentColumn ?: $detectedParentColumn;

        $now = now();
        $payload = [
            $childColumn => $taskId,
            $parentColumn => $dependsOnTaskId,
            'type' => 'finish_to_start',
            'lag_days' => 0,
            'created_by' => (int) auth()->user()->name,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('general_task_dependencies')->updateOrInsert(
            [
                $childColumn => $taskId,
                $parentColumn => $dependsOnTaskId,
            ],
            $payload
        );
    }

    private function wouldCreateCircularDependency(int $taskId, int $dependsOnTaskId): bool
    {
        if ($taskId === $dependsOnTaskId) {
            return true;
        }

        [$childColumn, $parentColumn] = $this->dependencyColumns();
        $visited = [];
        $stack = [$dependsOnTaskId];

        while (!empty($stack)) {
            $current = array_pop($stack);

            if ($current === $taskId) {
                return true;
            }

            if (isset($visited[$current])) {
                continue;
            }

            $visited[$current] = true;

            $parents = DB::table('general_task_dependencies')
                ->where($childColumn, $current)
                ->pluck($parentColumn)
                ->map(fn($id) => (int) $id)
                ->all();

            foreach ($parents as $parentId) {
                if (!isset($visited[$parentId])) {
                    $stack[] = $parentId;
                }
            }
        }

        return false;
    }

    private function dependencyColumns(): array
    {
        if (!Schema::hasTable('general_task_dependencies')) {
            return ['task_id', 'depends_on_task_id'];
        }

        $hasClassic = Schema::hasColumn('general_task_dependencies', 'task_id')
            && Schema::hasColumn('general_task_dependencies', 'depends_on_task_id');

        if ($hasClassic) {
            return ['task_id', 'depends_on_task_id'];
        }

        $hasParentChild = Schema::hasColumn('general_task_dependencies', 'child_task_id')
            && Schema::hasColumn('general_task_dependencies', 'parent_task_id');

        if ($hasParentChild) {
            return ['child_task_id', 'parent_task_id'];
        }

        return ['task_id', 'depends_on_task_id'];
    }

    private function logTaskChangeReason(GeneralTask $task, ?string $reason, string $action): void
    {
        $reason = trim((string) $reason);

        if ($reason === '') {
            return;
        }

        $task->reports()->create([
            'employee_id' => (int) auth()->user()->name,
            'type' => 'comment',
            'body' => "Änderungsgrund ({$action}): {$reason}",
        ]);
    }


    /**
     * Compatibility route handler.
     * Some existing routes/JS call GeneralTaskController::toggleStep.
     * The real logic stays in GeneralTaskStepController::toggle(), but this wrapper
     * prevents "Method ...::toggleStep does not exist" when route cache still points here.
     */
    public function toggleStep(Request $request, GeneralTask $generalTask, GeneralTaskStep $step)
    {
        return app(GeneralTaskStepController::class)->toggle($request, $generalTask, $step);
    }


    private function applyPriorityOrdering($query)
    {
        $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'normal' THEN 3 WHEN 'low' THEN 4 ELSE 5 END");

        if (Schema::hasColumn('general_tasks', 'sort_order')) {
            $query->orderBy('sort_order');
        }

        return $query
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->latest('created_at');
    }

    private function priorityRank(?string $priority): int
    {
        return match ($priority) {
            'urgent' => 1,
            'important' => 2,
            'normal' => 3,
            'low' => 4,
            default => 5,
        };
    }

    private function taskRelations(): array
    {
        return [
            'department',
            'claimedBy',
            'assignees',
            'reports.employee',
            'steps.assignees',
            'steps.checkedBy',
            'dependsOn.assignees',
            'blockingTasks.assignees',
            'dependencyParents.assignees',
            'dependencyChildren.assignees',
        ];
    }
}
