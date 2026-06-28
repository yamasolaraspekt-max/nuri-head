<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserDashboardShortcut;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UserDashboardShortcutController extends Controller
{
    private function employeeId(): ?int
    {
        $value = auth()->user()?->name;

        return is_numeric($value) ? (int) $value : null;
    }

    private function truthyPermission($query, string $column): void
    {
        $query->where($column, 1)
            ->orWhere($column, '1')
            ->orWhere($column, 'on')
            ->orWhere($column, true);
    }

    private function hasPermission(?string $permissionKey, string $action = 'is_read'): bool
    {
        if (!$permissionKey) {
            return true;
        }

        $employeeId = $this->employeeId();

        if (!$employeeId) {
            return false;
        }

        return DB::table('user_rolls')
            ->where('user_id', $employeeId)
            ->where('item_id', $permissionKey)
            ->where(function ($query) use ($action) {
                $this->truthyPermission($query, $action);
            })
            ->exists();
    }

    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (!UserDashboardShortcut::where('user_id', $user->id)->exists()) {
            $this->createDefaultShortcutsForUser($user->id, $this->employeeId());
        }

        $shortcuts = UserDashboardShortcut::query()
            ->where('user_id', $user->id)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($shortcut) {
                return $this->hasPermission(
                    $shortcut->permission_key,
                    $shortcut->permission_action ?: 'is_read'
                );
            })
            ->values()
            ->map(fn($shortcut) => [
                'id' => $shortcut->id,
                'label' => $shortcut->label,
                'subtitle' => $shortcut->subtitle,
                'icon' => $shortcut->icon,
                'url' => $shortcut->url,
                'permissionKey' => $shortcut->permission_key,
                'permissionAction' => $shortcut->permission_action,
                'sortOrder' => $shortcut->sort_order,
            ]);

        return response()->json([
            'success' => true,
            'shortcuts' => $shortcuts,
        ]);
    }

    private function createDefaultShortcutsForUser(int $userId, ?int $employeeId): void
    {
        $defaults = collect($this->shortcutRegistry())
            ->filter(function ($item) {
                return $this->hasPermission(
                    $item['permission_key'] ?? null,
                    $item['permission_action'] ?? 'is_read'
                );
            })
            ->take(6)
            ->values();

        foreach ($defaults as $index => $item) {
            UserDashboardShortcut::create([
                'user_id' => $userId,
                'employee_id' => $employeeId,
                'label' => $item['label'],
                'subtitle' => $item['subtitle'] ?? null,
                'icon' => $item['icon'] ?? 'zap',
                'url' => $item['url'] ?? '#',
                'permission_key' => $item['permission_key'] ?? null,
                'permission_action' => $item['permission_action'] ?? 'is_read',
                'is_visible' => true,
                'sort_order' => $index + 1,
                'meta' => [],
            ]);
        }
    }
    public function available(): JsonResponse
    {
        $items = collect($this->shortcutRegistry())
            ->filter(function ($item) {
                return $this->hasPermission(
                    $item['permission_key'] ?? null,
                    $item['permission_action'] ?? 'is_read'
                );
            })
            ->values();

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:160'],
            'icon' => ['nullable', 'string', 'max:80'],
            'url' => ['required', 'string', 'max:500'],
            'permission_key' => ['nullable', 'string', 'max:120'],
            'permission_action' => [
                'nullable',
                'string',
                Rule::in(['is_read', 'is_add', 'is_update', 'is_delete']),
            ],
        ]);

        if (
            !$this->hasPermission(
                $validated['permission_key'] ?? null,
                $validated['permission_action'] ?? 'is_read'
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Berechtigung für diesen Schnellzugriff.',
            ], 403);
        }

        $user = auth()->user();

        $sortOrder = UserDashboardShortcut::query()
            ->where('user_id', $user->id)
            ->max('sort_order');

        $shortcut = UserDashboardShortcut::create([
            'user_id' => $user->id,
            'employee_id' => $this->employeeId(),
            'label' => $validated['label'],
            'subtitle' => $validated['subtitle'] ?? null,
            'icon' => $validated['icon'] ?? 'zap',
            'url' => $validated['url'],
            'permission_key' => $validated['permission_key'] ?? null,
            'permission_action' => $validated['permission_action'] ?? 'is_read',
            'is_visible' => true,
            'sort_order' => ((int) $sortOrder) + 1,
            'meta' => [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schnellzugriff gespeichert.',
            'shortcut' => $shortcut,
        ]);
    }

    public function update(Request $request, UserDashboardShortcut $shortcut): JsonResponse
    {
        $this->authorizeShortcut($shortcut);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:160'],
            'icon' => ['nullable', 'string', 'max:80'],
            'url' => ['required', 'string', 'max:500'],
            'permission_key' => ['nullable', 'string', 'max:120'],
            'permission_action' => [
                'nullable',
                'string',
                Rule::in(['is_read', 'is_add', 'is_update', 'is_delete']),
            ],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        if (
            !$this->hasPermission(
                $validated['permission_key'] ?? null,
                $validated['permission_action'] ?? 'is_read'
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Berechtigung für diesen Schnellzugriff.',
            ], 403);
        }

        $shortcut->update([
            'label' => $validated['label'],
            'subtitle' => $validated['subtitle'] ?? null,
            'icon' => $validated['icon'] ?? 'zap',
            'url' => $validated['url'],
            'permission_key' => $validated['permission_key'] ?? null,
            'permission_action' => $validated['permission_action'] ?? 'is_read',
            'is_visible' => $validated['is_visible'] ?? $shortcut->is_visible,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schnellzugriff aktualisiert.',
            'shortcut' => $shortcut->fresh(),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:user_dashboard_shortcuts,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $user = auth()->user();

        DB::transaction(function () use ($validated, $user) {
            foreach ($validated['items'] as $item) {
                UserDashboardShortcut::query()
                    ->where('id', $item['id'])
                    ->where('user_id', $user->id)
                    ->update([
                        'sort_order' => $item['sort_order'],
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Reihenfolge gespeichert.',
        ]);
    }

    public function destroy(UserDashboardShortcut $shortcut): JsonResponse
    {
        $this->authorizeShortcut($shortcut);

        $shortcut->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schnellzugriff entfernt.',
        ]);
    }

    private function authorizeShortcut(UserDashboardShortcut $shortcut): void
    {
        abort_if($shortcut->user_id !== auth()->id(), 403);
    }

    private function shortcutRegistry(): array
    {
        return [
            [
                'label' => 'Anfrage',
                'subtitle' => 'Neu aufnehmen',
                'icon' => 'inbox',
                'url' => Route::has('inquiry.create') ? route('inquiry.create') : url('inquiry_create'),
                'permission_key' => 'Inquiry',
                'permission_action' => 'is_add',
            ],
            [
                'label' => 'Meine Anfragen',
                'subtitle' => 'Eigene Liste öffnen',
                'icon' => 'user-check',
                'url' => Route::has('my.inquiry.view') ? route('my.inquiry.view') : url('my-inquiry'),
                'permission_key' => 'Inquiry',
                'permission_action' => 'is_read',
            ],
            [
                'label' => 'Kunde',
                'subtitle' => 'Neu anlegen',
                'icon' => 'user-plus',
                'url' => url('new_lead_create'),
                'permission_key' => 'Customer',
                'permission_action' => 'is_add',
            ],
            [
                'label' => 'Kundenliste',
                'subtitle' => 'Kunden öffnen',
                'icon' => 'users',
                'url' => url('new_lead_view'),
                'permission_key' => 'Customer',
                'permission_action' => 'is_read',
            ],
            [
                'label' => 'Ticket',
                'subtitle' => 'Problem öffnen',
                'icon' => 'life-buoy',
                'url' => url('problem_create'),
                'permission_key' => 'Problem',
                'permission_action' => 'is_add',
            ],
            [
                'label' => 'Ticketliste',
                'subtitle' => 'Offene Tickets',
                'icon' => 'list',
                'url' => url('problem_view'),
                'permission_key' => 'Problem',
                'permission_action' => 'is_read',
            ],
            [
                'label' => 'Kalender',
                'subtitle' => 'Mein Kalender',
                'icon' => 'calendar-days',
                'url' => url('tasks/calendar/personal'),
                'permission_key' => null,
                'permission_action' => 'is_read',
            ],
            [
                'label' => 'Aufgaben',
                'subtitle' => 'Meine Aufgaben',
                'icon' => 'check-square',
                'url' => url('personal/task/' . $this->employeeId()),
                'permission_key' => null,
                'permission_action' => 'is_read',
            ],
            [
                'label' => 'Angebote',
                'subtitle' => 'Angebotsliste',
                'icon' => 'clipboard',
                'url' => url('admin/offers'),
                'permission_key' => null,
                'permission_action' => 'is_read',
            ],
            [
                'label' => 'Aufträge',
                'subtitle' => 'Auftragsliste',
                'icon' => 'briefcase',
                'url' => Route::has('deal.all.list') ? route('deal.all.list') : url('deal'),
                'permission_key' => null,
                'permission_action' => 'is_read',
            ],
        ];
    }
}