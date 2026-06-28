<?php

namespace App\Http\Controllers\Customer\Kanban;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PersonalTask;
use App\Models\PersonalTaskComment;
use App\Models\PersonalTaskKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class KanbanPersonalTaskPanelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function tasks(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = $this->nullableInt($data['alternative_id'] ?? null);
        $productId = $this->nullableInt($data['product_id'] ?? null);

        $tasks = PersonalTask::query()
            ->with([
                'employees:id,name,lastname,image,gender',
                'assignedBy:id,name,lastname,image,gender',
                'product:id,article_group,initial',
                'customer:id,name,lastname,firma',
                'keys' => function ($q) {
                    $q->orderBy('id');
                },
                'rootComments' => function ($q) {
                    $q->with([
                        'author:id,name,lastname,image,gender',
                        'replies.author:id,name,lastname,image,gender',
                    ]);
                },
            ])
            ->whereNull('deleted_at')
            ->whereNull('archived_at')
            ->where('customer_id', $customerId)
            /*
             * Kanban sidebar must not hide older tasks.
             * Older tasks are often saved only with customer_id, without alternative_id/product_id.
             * Therefore we load all customer tasks and only sort exact context matches first.
             */
            ->orderByRaw("
                CASE
                    WHEN ? IS NOT NULL AND ? IS NOT NULL AND alternative_id = ? AND product_id = ? THEN 0
                    WHEN ? IS NOT NULL AND alternative_id = ? THEN 1
                    WHEN ? IS NOT NULL AND product_id = ? THEN 2
                    ELSE 3
                END
            ", [$alternativeId, $productId, $alternativeId, $productId, $alternativeId, $alternativeId, $productId, $productId])
            ->orderByRaw("
                CASE
                    WHEN task_status IN ('open', 'start', 'new') THEN 1
                    WHEN task_status IN ('on_progress', 'in_progress', 'progress') THEN 2
                    WHEN task_status IN ('pause') THEN 3
                    WHEN task_status IN ('completed') THEN 4
                    ELSE 5
                END
            ")
            ->orderByRaw('COALESCE(due_date, reminder_date, start_date, created_at) ASC')
            ->get();

        return response()->json([
            'status' => 'ok',
            'count' => $tasks->count(),
            'tasks' => $tasks->map(fn($task) => $this->taskPayload($task))->values(),
        ]);
    }

    public function show(Request $request, PersonalTask $task): JsonResponse
    {
        $this->authorizeTaskAccess($task);

        $task->load([
            'employees:id,name,lastname,image,gender',
            'assignedBy:id,name,lastname,image,gender',
            'product:id,article_group,initial',
            'customer:id,name,lastname,firma',
            'keys' => function ($q) {
                $q->orderBy('id');
            },
            'rootComments' => function ($q) {
                $q->with([
                    'author:id,name,lastname,image,gender',
                    'replies.author:id,name,lastname,image,gender',
                ]);
            },
        ]);

        return response()->json([
            'status' => 'ok',
            'task' => $this->taskPayload($task),
        ]);
    }

    public function storeComment(Request $request, PersonalTask $task): JsonResponse
    {
        $this->authorizeTaskAccess($task);

        $data = $request->validate([
            'comment' => ['required', 'string', 'max:10000'],
        ]);

        $comment = PersonalTaskComment::create([
            'task_id' => $task->id,
            'comment_by' => $this->currentEmployeeId(),
            'comment' => $this->cleanComment($data['comment']),
            'status' => 'Published',
            'parent_id' => null,
        ]);

        $comment->load('author:id,name,lastname,image,gender');

        return response()->json([
            'status' => 'ok',
            'message' => 'Kommentar wurde gespeichert.',
            'comment' => $this->commentPayload($comment),
        ]);
    }

    public function storeReply(Request $request, PersonalTaskComment $comment): JsonResponse
    {
        $task = PersonalTask::findOrFail($comment->task_id);
        $this->authorizeTaskAccess($task);

        $data = $request->validate([
            'comment' => ['required', 'string', 'max:10000'],
        ]);

        $reply = PersonalTaskComment::create([
            'task_id' => $comment->task_id,
            'comment_by' => $this->currentEmployeeId(),
            'comment' => $this->cleanComment($data['comment']),
            'status' => 'Published',
            'parent_id' => $comment->id,
        ]);

        $reply->load('author:id,name,lastname,image,gender');

        return response()->json([
            'status' => 'ok',
            'message' => 'Antwort wurde gespeichert.',
            'reply' => $this->commentPayload($reply),
        ]);
    }

    public function toggleKey(Request $request, PersonalTaskKey $key): JsonResponse
    {
        $task = PersonalTask::findOrFail($key->personal_task_id);
        $this->authorizeTaskAccess($task);

        $isCompleted = (int) ($key->is_completed ?? 0) === 1;

        $key->update([
            'is_completed' => $isCompleted ? 0 : 1,
            'status' => $isCompleted ? 'pending' : 'completed',
            'done_status' => $isCompleted ? null : 'done',
            'done_by' => $isCompleted ? null : $this->currentEmployeeId(),
            'done_date' => $isCompleted ? null : now(),
        ]);

        return response()->json([
            'status' => 'ok',
            'key' => [
                'id' => $key->id,
                'is_completed' => (bool) $key->is_completed,
                'status' => $key->status,
                'done_by' => $key->done_by,
                'done_date' => $this->safeDateTime($key->done_date ?? null),
            ],
        ]);
    }

    protected function taskPayload(PersonalTask $task): array
    {
        $keys = $task->keys ?? collect();

        return [
            'id' => $task->id,
            'task_id' => $task->task_id,
            'title' => $task->task_title,
            'description' => $task->description,
            'status' => $task->task_status,
            'board_column' => $task->board_column,
            'priority' => $task->priority,
            'color' => $task->color,
            'progress' => (int) ($task->progress ?? 0),
            'public' => (bool) $task->public,
            'is_report' => (bool) $task->is_report,

            'start_date' => $this->safeDate($task->start_date),
            'due_date' => $this->safeDate($task->due_date),
            'due_time' => $task->due_time,
            'reminder_date' => $this->safeDate($task->reminder_date),
            'reminder_time' => $task->reminder_time,
            'total_day' => $task->total_day,
            'total_time' => $task->total_time,
            'created_at' => $this->safeDateTime($task->created_at),
            'updated_at' => $this->safeDateTime($task->updated_at),

            'customer_id' => $task->customer_id,
            'alternative_id' => $task->alternative_id,
            'product_id' => $task->product_id,
            'lead_product_list_id' => $task->lead_product_list_id ?? null,
            'lead_stage_id' => $task->lead_stage_id ?? null,
            'lead_stage_sub_stage_id' => $task->lead_stage_sub_stage_id ?? null,
            'lead_stage_context' => $this->leadStageContextPayload($task),

            'customer' => [
                'id' => $task->customer?->id,
                'name' => trim(($task->customer?->name ?? '') . ' ' . ($task->customer?->lastname ?? '')) ?: ($task->customer?->firma ?? null),
            ],

            'product' => [
                'id' => $task->product?->id,
                'name' => $task->product?->article_group,
                'initial' => $task->product?->initial,
            ],

            'assigned_by' => $this->employeePayload($task->assignedBy),
            'employees' => $this->employeesPayload($task->employees ?? collect()),
            'controllers' => $this->employeesPayload($task->controllers ?? collect()),

            'keys_count' => $keys->count(),
            'keys_done' => $keys->filter(fn($key) => (int) ($key->is_completed ?? 0) === 1)->count(),
            'keys' => $keys->map(fn($key) => $this->keyPayload($key))->values(),

            'comments_count' => ($task->rootComments ?? collect())->count(),
            'comments' => ($task->rootComments ?? collect())
                ->map(fn($comment) => $this->commentPayload($comment))
                ->values(),
        ];
    }


    protected function leadStageContextPayload(PersonalTask $task): array
    {
        $stageId = $task->lead_stage_id ?? null;
        $subStageId = $task->lead_stage_sub_stage_id ?? null;

        $stage = null;
        $subStage = null;

        if ($stageId && Schema::hasTable('lead_stages')) {
            $stage = DB::table('lead_stages')
                ->where('id', $stageId)
                ->first(['id', 'key', 'name', 'color', 'icon']);
        }

        if ($subStageId && Schema::hasTable('lead_stage_sub_stages')) {
            $subStage = DB::table('lead_stage_sub_stages')
                ->where('id', $subStageId)
                ->first(['id', 'lead_stage_id', 'key', 'name', 'color', 'icon']);
        }

        return [
            'lead_stage_id' => $stage?->id ?? $stageId,
            'lead_stage_key' => $stage?->key ?? null,
            'lead_stage_name' => $stage?->name ?? null,
            'lead_stage_color' => $stage?->color ?? '#74b2d4',
            'lead_stage_sub_stage_id' => $subStage?->id ?? $subStageId,
            'lead_stage_sub_stage_name' => $subStage?->name ?? null,
            'lead_stage_sub_stage_color' => $subStage?->color ?? '#93c21c',
        ];
    }

    protected function keyPayload(PersonalTaskKey $key): array
    {
        $employeeIds = $this->decodeEmployeeIds($key->employee_id ?? null);

        return [
            'id' => $key->id,
            'personal_task_id' => $key->personal_task_id,
            'task' => $key->task,
            'description' => $key->key_description,
            'duration' => $key->duration,
            'total_time' => $key->total_time,
            'submit_time' => $key->submit_time,
            'status' => $key->status,
            'done_status' => $key->done_status ?? null,
            'is_completed' => (bool) ($key->is_completed ?? false),
            'work_progress' => (int) ($key->work_progress ?? 0),
            'reason' => $key->reason,
            'link' => $key->link ?? null,
            'done_by' => $key->done_by,
            'done_date' => $this->safeDateTime($key->done_date ?? null),
            'employee_ids' => $employeeIds,
            'employees' => $this->employeesByIds($employeeIds),
        ];
    }

    protected function commentPayload(PersonalTaskComment $comment): array
    {
        return [
            'id' => $comment->id,
            'task_id' => $comment->task_id,
            'comment' => $comment->comment,
            'status' => $comment->status,
            'created_at' => $this->safeDateTime($comment->created_at),
            'author' => $this->employeePayload($comment->author),
            'replies' => ($comment->replies ?? collect())
                ->map(fn($reply) => $this->commentPayload($reply))
                ->values(),
        ];
    }

    protected function authorizeTaskAccess(PersonalTask $task): void
    {
        $employeeId = $this->currentEmployeeId();

        $isCreator = (string) $task->assigned_by === (string) $employeeId;

        $isAssigned = DB::table('employees_personal_tasks')
            ->where('task_id', $task->id)
            ->where('employee_id', $employeeId)
            ->exists();

        $isPublic = (bool) $task->public;

        abort_unless($isCreator || $isAssigned || $isPublic, 403, 'Keine Berechtigung für diese Aufgabe.');
    }

    protected function currentEmployeeId(): ?int
    {
        return Auth::check() ? (int) Auth::user()->name : null;
    }

    protected function nullableInt($value): ?int
    {
        if ($value === null || $value === '' || $value === 0 || $value === '0' || $value === 'null' || $value === 'undefined') {
            return null;
        }

        return (int) $value;
    }


    protected function safeDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return $value instanceof Carbon
                ? $value->toDateString()
                : Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    protected function safeDateTime($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return $value instanceof Carbon
                ? $value->toDateTimeString()
                : Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    protected function cleanComment(string $comment): string
    {
        return strip_tags($comment, '<p><br><b><strong><i><em><u><ul><ol><li><a><span>');
    }

    protected function decodeEmployeeIds($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return collect($value)->filter()->map(fn($id) => (int) $id)->unique()->values()->all();
        }

        $decoded = json_decode((string) $value, true);

        if (is_array($decoded)) {
            return collect($decoded)->filter()->map(fn($id) => (int) $id)->unique()->values()->all();
        }

        return collect(explode(',', (string) $value))
            ->filter()
            ->map(fn($id) => (int) trim($id))
            ->unique()
            ->values()
            ->all();
    }

    protected function employeesByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return Employee::query()
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'lastname', 'image', 'gender'])
            ->map(fn($employee) => $this->employeePayload($employee))
            ->filter()
            ->values()
            ->all();
    }

    protected function employeesPayload($employees): array
    {
        return collect($employees)
            ->map(fn($employee) => $this->employeePayload($employee))
            ->filter()
            ->values()
            ->all();
    }

    protected function employeePayload($employee): ?array
    {
        if (!$employee) {
            return null;
        }

        return [
            'id' => $employee->id,
            'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
            'image' => $employee->image ? asset('images/employee/' . $employee->image) : null,
            'gender' => $employee->gender ?? null,
        ];
    }
}