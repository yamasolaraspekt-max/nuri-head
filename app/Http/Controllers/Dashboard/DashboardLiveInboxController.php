<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;

use App\Models\DashboardLiveActivity;
use Illuminate\Http\Request;

class DashboardLiveInboxController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = (string) auth()->user()->name;

        $query = DashboardLiveActivity::query()
            ->where('employee_id', $employeeId);

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filter === 'unread') {
            $query->whereNull('read_at');
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $items = $query
            ->latest()
            ->limit(60)
            ->get()
            ->map(fn($item) => $this->formatActivity($item));

        $unreadCount = DashboardLiveActivity::where('employee_id', $employeeId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function markRead($id)
    {
        $activity = $this->findMine($id);

        $activity->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'activity' => $this->formatActivity($activity->fresh()),
        ]);
    }

    public function markUnread($id)
    {
        $activity = $this->findMine($id);

        $activity->update([
            'read_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'activity' => $this->formatActivity($activity->fresh()),
        ]);
    }

    public function markAllRead()
    {
        $employeeId = (string) auth()->user()->name;

        DashboardLiveActivity::where('employee_id', $employeeId)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    }

    private function findMine($id): DashboardLiveActivity
    {
        return DashboardLiveActivity::where('employee_id', (string) auth()->user()->name)
            ->where('id', $id)
            ->firstOrFail();
    }

    private function formatActivity(DashboardLiveActivity $item): array
    {
        return [
            'id' => $item->id,
            'type' => $item->type,
            'action' => $item->action,
            'title' => $item->title,
            'message' => $item->message,
            'url' => $item->url,
            'payload' => $item->payload,
            'read_at' => optional($item->read_at)->toISOString(),
            'is_read' => (bool) $item->read_at,
            'created_at' => optional($item->created_at)->toISOString(),
            'created_human' => optional($item->created_at)->diffForHumans(),
        ];
    }
}