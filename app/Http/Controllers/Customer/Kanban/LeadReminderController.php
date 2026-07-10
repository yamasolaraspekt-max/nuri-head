<?php

namespace App\Http\Controllers\Customer\Kanban;

use App\Http\Controllers\Controller;
use App\Models\LeadReminder;
use App\Models\LeadProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadReminderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['done']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lead_product_list_id' => ['required', 'exists:lead_product_lists,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'reminder_date' => ['required', 'date'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'priority' => ['required', 'in:normal,important,critical'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'responsible_employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $lpl = LeadProductList::findOrFail($data['lead_product_list_id']);

        $reminder = LeadReminder::create([
            'lead_product_list_id' => $lpl->id,
            'customer_id' => $lpl->customer_id,
            'alternative_id' => $lpl->alternative_id,
            'product_id' => $lpl->product_id,
            'department_id' => $data['department_id'] ?? $lpl->department_id,
            'responsible_employee_id' => $data['responsible_employee_id'] ?? $lpl->employee_id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'reminder_date' => $data['reminder_date'],
            'reminder_time' => $data['reminder_time'] ?? null,
            'priority' => $data['priority'],
            'status' => 'open',
            'created_by' => auth()->user()->name ?? auth()->id(),
        ]);

        DB::table('lead_activity_logs')->insert([
            'new_leads_id' => $lpl->customer_id,
            'alternative_id' => $lpl->alternative_id,
            'product_id' => $lpl->product_id,
            'user_id' => Auth::id(),
            'user_name' => auth()->user()->name ?? 'System',
            'event_type' => 'reminder_created',
            'model_type' => LeadReminder::class,
            'model_id' => $reminder->id,
            'changes' => json_encode([
                'info' => 'Erinnerung erstellt: ' . $reminder->title,
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Erinnerung wurde gespeichert.',
            'reminder' => $this->formatReminder($reminder),
        ]);
    }

    public function due()
    {
        $employeeId = auth()->user()->name;
        $now = now();
        $today = $now->toDateString();
        $time = $now->format('H:i');

        $items = LeadReminder::query()
            ->with(['customer:id,name,lastname,firma', 'alternative:id,object_name,street,postcode,city', 'product:id,article_group,initial', 'responsibleEmployee:id,name,lastname'])
            ->where('status', 'open')
            ->where(function ($q) use ($employeeId) {
                $q->whereNull('responsible_employee_id')
                    ->orWhere('responsible_employee_id', $employeeId);
            })
            ->where(function ($q) use ($today, $time) {
                $q->whereDate('reminder_date', '<', $today)
                    ->orWhere(function ($sub) use ($today, $time) {
                        $sub->whereDate('reminder_date', $today)
                            ->where(function ($t) use ($time) {
                                $t->whereNull('reminder_time')
                                    ->orWhere('reminder_time', '<=', $time);
                            });
                    });
            })
            ->whereNull('notified_at')
            ->orderBy('reminder_date')
            ->orderBy('reminder_time')
            ->limit(10)
            ->get();

        LeadReminder::whereIn('id', $items->pluck('id'))->update([
            'notified_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'items' => $items->map(fn($item) => $this->formatReminder($item))->values(),
        ]);
    }

    public function done(LeadReminder $reminder)
    {
        $reminder->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        DB::table('lead_activity_logs')->insert([
            'new_leads_id' => $reminder->customer_id,
            'alternative_id' => $reminder->alternative_id,
            'product_id' => $reminder->product_id,
            'user_id' => Auth::id(),
            'user_name' => auth()->user()->name ?? 'System',
            'event_type' => 'reminder_done',
            'model_type' => LeadReminder::class,
            'model_id' => $reminder->id,
            'changes' => json_encode([
                'info' => 'Erinnerung erledigt: ' . $reminder->title,
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Erinnerung wurde erledigt.',
        ]);
    }

    public function context(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = $data['alternative_id'] ?? null;
        $productId = $data['product_id'] ?? null;

        $activities = $this->activityQuery($customerId, $alternativeId, $productId)
            ->orderBy('l.created_at')
            ->limit(100)
            ->get()
            ->map(fn($item) => $this->formatActivity($item));

        $reminders = LeadReminder::query()
            ->with(['responsibleEmployee:id,name,lastname', 'customer:id,name,lastname,firma', 'alternative:id,object_name,street,postcode,city', 'product:id,article_group,initial'])
            ->where('customer_id', $customerId)
            ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->where('status', 'open')
            ->orderBy('reminder_date')
            ->orderBy('reminder_time')
            ->get()
            ->map(fn($item) => $this->formatReminder($item));

        return response()->json([
            'status' => 'success',
            'activities' => $activities,
            'reminders' => $reminders,
        ]);
    }

    public function cardSummaries(Request $request)
    {
        $data = $request->validate([
            'contexts' => ['required', 'array'],
            'contexts.*.lead_product_list_id' => ['nullable', 'integer'],
            'contexts.*.customer_id' => ['required', 'integer'],
            'contexts.*.alternative_id' => ['nullable', 'integer'],
            'contexts.*.product_id' => ['nullable', 'integer'],
        ]);

        $items = [];

        foreach ($data['contexts'] as $index => $ctx) {
            $customerId = (int) $ctx['customer_id'];
            $alternativeId = $ctx['alternative_id'] ?? null;
            $productId = $ctx['product_id'] ?? null;
            $leadProductListId = $ctx['lead_product_list_id'] ?? null;
            $key = $leadProductListId ?: ($customerId . ':' . ($alternativeId ?: '') . ':' . ($productId ?: ''));

            $reminders = LeadReminder::query()
                ->with(['responsibleEmployee:id,name,lastname'])
                ->where('customer_id', $customerId)
                ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
                ->when($productId, fn($q) => $q->where('product_id', $productId))
                ->where('status', 'open')
                ->orderBy('reminder_date')
                ->orderBy('reminder_time')
                ->limit(10)
                ->get()
                ->map(fn($item) => $this->formatReminder($item))
                ->values();

            $activitiesCount = $this->activityQuery($customerId, $alternativeId, $productId)->count();

            $items[$key] = [
                'index' => $index,
                'lead_product_list_id' => $leadProductListId,
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'product_id' => $productId,
                'reminders_count' => $reminders->count(),
                'activities_count' => $activitiesCount,
                'reminders' => $reminders,
            ];
        }

        return response()->json([
            'status' => 'success',
            'items' => $items,
        ]);
    }

    private function activityQuery(int $customerId, $alternativeId = null, $productId = null)
    {
        return DB::table('lead_activity_logs as l')
            ->leftJoin('employees as e', 'e.id', '=', 'l.user_name')
            ->where('l.new_leads_id', $customerId)
            ->when($alternativeId, fn($q) => $q->where('l.alternative_id', $alternativeId))
            ->when($productId, fn($q) => $q->where('l.product_id', $productId))
            ->select(
                'l.id',
                'l.event_type',
                'l.model_type',
                'l.model_id',
                'l.changes',
                'l.created_at',
                DB::raw("CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,'')) as employee_name")
            );
    }

    private function formatActivity($item): array
    {
        $changes = json_decode($item->changes ?? '{}', true) ?: [];
        $date = Carbon::parse($item->created_at);

        return [
            'id' => $item->id,
            'type_raw' => $item->event_type,
            'type' => $this->eventLabel($item->event_type),
            'type_label' => $this->eventLabel($item->event_type),
            'text' => $changes['info'] ?? $this->eventLabel($item->event_type),
            'employee' => trim((string) $item->employee_name) ?: 'System',
            'date' => $date->format('d.m.Y, H:i') . ' Uhr',
            'date_human' => $date->format('d.m.Y, H:i') . ' Uhr',
            'date_raw' => $date->toIso8601String(),
            'created_at' => $date->toIso8601String(),
        ];
    }

    private function formatReminder(LeadReminder $item): array
    {
        $date = $item->reminder_date ? Carbon::parse($item->reminder_date)->format('d.m.Y') : '';
        $time = $item->reminder_time ? Carbon::parse($item->reminder_time)->format('H:i') : '';
        $dueText = trim($date . ($time ? ', ' . $time . ' Uhr' : ''));
        $employeeName = $item->responsibleEmployee
            ? trim(($item->responsibleEmployee->name ?? '') . ' ' . ($item->responsibleEmployee->lastname ?? ''))
            : 'Nicht zugewiesen';

        return [
            'id' => $item->id,
            'lead_product_list_id' => $item->lead_product_list_id,
            'customer_id' => $item->customer_id,
            'alternative_id' => $item->alternative_id,
            'product_id' => $item->product_id,
            'department_id' => $item->department_id,
            'responsible_employee_id' => $item->responsible_employee_id,
            'title' => $item->title,
            'description' => $item->description,
            'reminder_date' => optional($item->reminder_date)->format('Y-m-d') ?: (is_string($item->reminder_date) ? $item->reminder_date : null),
            'reminder_time' => $item->reminder_time ? Carbon::parse($item->reminder_time)->format('H:i') : null,
            'due_text' => $dueText,
            'priority' => $item->priority ?: 'normal',
            'priority_label' => $this->priorityLabel($item->priority),
            'status' => $item->status ?: 'open',
            'status_label' => $this->statusLabel($item->status),
            'employee' => $employeeName,
            'responsible_employee_name' => $employeeName,
        ];
    }

    private function priorityLabel(?string $priority): string
    {
        return match (strtolower((string) $priority)) {
            'important' => 'Wichtig',
            'critical' => 'Kritisch',
            default => 'Normal',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'done' => 'Erledigt',
            'cancelled' => 'Abgebrochen',
            'in_progress' => 'In Bearbeitung',
            'overdue' => 'Überfällig',
            default => 'Offen',
        };
    }

    private function eventLabel(?string $event): string
    {
        return match (strtolower((string) $event)) {
            'created' => 'Erstellt',
            'updated' => 'Aktualisiert',
            'deleted' => 'Gelöscht',
            'reminder_created' => 'Erinnerung erstellt',
            'reminder_done' => 'Erinnerung erledigt',
            'stage_changed', 'updated_stage' => 'Phase geändert',
            'status_changed' => 'Status geändert',
            'moved' => 'Verschoben',
            default => 'Aktivität',
        };
    }

    public function overdueCenter()
    {
        $employeeId = auth()->user()->name;
        $limitTime = now()->subDay();

        $items = LeadReminder::query()
            ->with([
                'customer:id,name,lastname,firma,customer_no,street,postcode,city',
                'alternative:id,lead_id,object_name,street,postcode,city,full_address',
                'product:id,article_group,initial',
                'responsibleEmployee:id,name,lastname,image',
            ])
            ->where('status', 'open')
            ->where(function ($q) use ($employeeId) {
                $q->whereNull('responsible_employee_id')
                    ->orWhere('responsible_employee_id', $employeeId);
            })
            ->whereRaw(
                "TIMESTAMP(reminder_date, COALESCE(reminder_time, '00:00:00')) <= ?",
                [$limitTime->format('Y-m-d H:i:s')]
            )
            ->orderBy('reminder_date')
            ->orderBy('reminder_time')
            ->limit(100)
            ->get()
            ->map(function ($r) {
                $due = Carbon::parse($r->reminder_date->format('Y-m-d') . ' ' . ($r->reminder_time ?: '00:00'));

                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'description' => $r->description,
                    'priority' => $r->priority,
                    'priority_label' => match ($r->priority) {
                        'important' => 'Wichtig',
                        'critical' => 'Kritisch',
                        default => 'Normal',
                    },
                    'due_label' => $due->format('d.m.Y, H:i') . ' Uhr',
                    'overdue_label' => 'Seit ' . $due->diffForHumans(now(), true) . ' überfällig',
                    'customer_id' => $r->customer_id,
                    'alternative_id' => $r->alternative_id,
                    'product_id' => $r->product_id,
                    'lead_product_list_id' => $r->lead_product_list_id,
                    'customer_name' => $r->customer?->firma ?: trim(($r->customer?->name ?? '') . ' ' . ($r->customer?->lastname ?? '')),
                    'customer_no' => $r->customer?->customer_no,
                    'object_name' => $r->alternative?->object_name,
                    'address' => $r->alternative?->full_address
                        ?: trim(($r->alternative?->street ?? '') . ', ' . ($r->alternative?->postcode ?? '') . ' ' . ($r->alternative?->city ?? '')),
                    'product_name' => $r->product?->article_group,
                    'product_initial' => $r->product?->initial,
                    'employee_name' => trim(($r->responsibleEmployee?->name ?? '') . ' ' . ($r->responsibleEmployee?->lastname ?? '')),
                ];
            });

        return response()->json([
            'status' => 'success',
            'count' => $items->count(),
            'items' => $items,
        ]);
    }
}
