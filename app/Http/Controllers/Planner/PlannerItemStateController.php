<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\PlannerItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PlannerItemStateController extends Controller
{
    public function statusesByPlan(Request $request, int $planId)
    {
        $items = PlannerItem::query()
            ->where('plan_id', $planId)
            ->get(['id','status','started_at','paused_at','stopped_at','pause_reason','last_status_reason','last_status_changed_at','last_status_changed_by_employee_id','meta']);

        // Optional: if you want employee name, store it in meta on change (we do below).
        $data = $items->map(function (PlannerItem $it) {
            $lastEvent = $this->getLastStatusEvent($it);

            return [
                'id' => $it->id,
                'status' => $it->status,
                'started_at' => optional($it->started_at)->toISOString(),
                'paused_at' => optional($it->paused_at)->toISOString(),
                'stopped_at' => optional($it->stopped_at)->toISOString(),
                'last_status_changed_at' => optional($it->last_status_changed_at)->toISOString(),

                'reason' => $it->last_status_reason ?: $it->pause_reason,
                'by_employee_id' => $it->last_status_changed_by_employee_id,

                // from meta history (preferred)
                'last_event' => $lastEvent,
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'data' => [
                'plan_id' => $planId,
                'items' => $data,
            ],
        ]);
    }

    public function play(Request $request, int $planId, int $itemId)
    {
        $reason = $this->validateReason($request);
        $item = $this->findPlanItem($planId, $itemId);

        $now = now();
        [$employeeId, $employeeName] = $this->actorIdentity();

        $item->forceFill([
            'status' => 'in-progress',
            'started_at' => $item->started_at ?: $now,
            'paused_at' => null,
            'stopped_at' => null,
            'pause_reason' => null,

            'last_status_changed_at' => $now,
            'last_status_changed_by_employee_id' => $employeeId,
            'last_status_reason' => $reason,
        ]);

        $this->appendStatusEvent($item, 'play', $reason, $employeeId, $employeeName, $now);
        $item->save();

        return $this->okItem($item);
    }

    public function pause(Request $request, int $planId, int $itemId)
    {
        $reason = $this->validateReason($request);
        $item = $this->findPlanItem($planId, $itemId);

        $now = now();
        [$employeeId, $employeeName] = $this->actorIdentity();

        $item->forceFill([
            'status' => 'paused',
            'paused_at' => $now,
            'pause_reason' => $reason,

            'last_status_changed_at' => $now,
            'last_status_changed_by_employee_id' => $employeeId,
            'last_status_reason' => $reason,
        ]);

        $this->appendStatusEvent($item, 'pause', $reason, $employeeId, $employeeName, $now);
        $item->save();

        return $this->okItem($item);
    }

    public function stop(Request $request, int $planId, int $itemId)
    {
        $reason = $this->validateReason($request);
        $item = $this->findPlanItem($planId, $itemId);

        $now = now();
        [$employeeId, $employeeName] = $this->actorIdentity();

        $item->forceFill([
            'status' => 'stopped',
            'stopped_at' => $now,
            'paused_at' => null,

            'last_status_changed_at' => $now,
            'last_status_changed_by_employee_id' => $employeeId,
            'last_status_reason' => $reason,
        ]);

        $this->appendStatusEvent($item, 'stop', $reason, $employeeId, $employeeName, $now);
        $item->save();

        return $this->okItem($item);
    }

    // --------------------
    // Helpers
    // --------------------

    private function validateReason(Request $request): string
    {
        $data = $request->validate([
            'reason' => ['required','string','min:2','max:2000'],
        ]);
        return trim($data['reason']);
    }

    private function findPlanItem(int $planId, int $itemId): PlannerItem
    {
        return PlannerItem::query()
            ->where('plan_id', $planId)
            ->where('id', $itemId)
            ->firstOrFail();
    }

    private function actorIdentity(): array
    {
        $user = Auth::user();

        $employeeId =
            $user?->employee_id ??
            $user?->employee?->id ??
            null;

        $employeeName =
            $user?->employee?->full_name ??
            $user?->name ??
            '—';

        return [$employeeId, $employeeName];
    }

    private function appendStatusEvent(PlannerItem $item, string $action, string $reason, $employeeId, string $employeeName, Carbon $at): void
    {
        $meta = is_array($item->meta) ? $item->meta : [];
        $meta['status_events'] = is_array($meta['status_events'] ?? null) ? $meta['status_events'] : [];

        $meta['status_events'][] = [
            'action' => $action,                 // play | pause | stop
            'reason' => $reason,
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'user_id' => Auth::id(),
            'at' => $at->toISOString(),
            'ip' => request()->ip(),
        ];

        // keep last event fast-access
        $meta['last_status_event'] = end($meta['status_events']);

        $item->meta = $meta;
    }

    private function getLastStatusEvent(PlannerItem $item): ?array
    {
        $meta = is_array($item->meta) ? $item->meta : [];
        if (is_array($meta['last_status_event'] ?? null)) return $meta['last_status_event'];

        $events = $meta['status_events'] ?? null;
        if (is_array($events) && count($events)) return $events[count($events) - 1];

        return null;
    }

    private function okItem(PlannerItem $item)
    {
        $item->refresh();

        $meta = is_array($item->meta) ? $item->meta : [];
        $last = $meta['last_status_event'] ?? null;

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $item->id,
                'plan_id' => $item->plan_id,
                'status' => $item->status,
                'started_at' => optional($item->started_at)->toISOString(),
                'paused_at' => optional($item->paused_at)->toISOString(),
                'stopped_at' => optional($item->stopped_at)->toISOString(),
                'last_status_changed_at' => optional($item->last_status_changed_at)->toISOString(),
                'reason' => $item->last_status_reason ?: $item->pause_reason,
                'by_employee_id' => $item->last_status_changed_by_employee_id,
                'last_event' => is_array($last) ? $last : null,
            ],
        ]);
    }
}
