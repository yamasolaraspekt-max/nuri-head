<?php

namespace App\Http\Controllers\Admin;

use App\Events\SystemWarningUpdated;
use App\Http\Controllers\Controller;
use App\Models\SystemWarning;
use App\Models\SystemWarningHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemWarningController extends Controller
{
    public function index(): View
    {
        $warning = SystemWarning::current();

        $histories = SystemWarningHistory::query()
            ->latest()
            ->limit(30)
            ->get();

        return view('admin.system-warning.index', compact('warning', 'histories'));
    }

    public function current(): JsonResponse
    {
        $warning = SystemWarning::current();

        return response()->json([
            'success' => true,
            'warning' => $warning->publicPayload(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
            'type' => ['required', 'string', 'in:development,uploading,fixing,maintenance'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:3000'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'theme' => ['required', 'string', 'in:amber,blue,red,green,purple'],
            'can_close' => ['required', 'boolean'],
            'show_backdrop' => ['required', 'boolean'],
        ]);

        $warning = SystemWarning::current();

        $wasActive = $warning->is_active;

        $warning->fill([
            'is_active' => $validated['is_active'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'message' => $validated['message'] ?? null,
            'button_text' => $validated['button_text'] ?: 'Verstanden',
            'theme' => $validated['theme'],
            'can_close' => $validated['can_close'],
            'show_backdrop' => $validated['show_backdrop'],
            'updated_by' => auth()->id(),
        ]);

        if (!$wasActive && $warning->is_active) {
            $warning->started_at = now();
            $warning->ended_at = null;
            $action = 'enabled';
        } elseif ($wasActive && !$warning->is_active) {
            $warning->ended_at = now();
            $action = 'disabled';
        } else {
            $action = 'updated';
        }

        if (!$warning->created_by) {
            $warning->created_by = auth()->id();
        }

        $warning->save();

        SystemWarningHistory::create([
            'system_warning_id' => $warning->id,
            'action' => $action,
            'type' => $warning->type,
            'title' => $warning->title,
            'message' => $warning->message,
            'is_active' => $warning->is_active,
            'changed_by' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        broadcast(new SystemWarningUpdated($warning));

        return response()->json([
            'success' => true,
            'message' => 'Systemhinweis wurde aktualisiert.',
            'warning' => $warning->publicPayload(),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $warning = SystemWarning::current();

        $wasActive = $warning->is_active;
        $warning->is_active = $request->boolean('is_active');
        $warning->updated_by = auth()->id();

        if (!$wasActive && $warning->is_active) {
            $warning->started_at = now();
            $warning->ended_at = null;
            $action = 'enabled';
        } elseif ($wasActive && !$warning->is_active) {
            $warning->ended_at = now();
            $action = 'disabled';
        } else {
            $action = 'updated';
        }

        $warning->save();

        SystemWarningHistory::create([
            'system_warning_id' => $warning->id,
            'action' => $action,
            'type' => $warning->type,
            'title' => $warning->title,
            'message' => $warning->message,
            'is_active' => $warning->is_active,
            'changed_by' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        broadcast(new SystemWarningUpdated($warning));

        return response()->json([
            'success' => true,
            'message' => $warning->is_active
                ? 'Systemhinweis wurde aktiviert.'
                : 'Systemhinweis wurde deaktiviert.',
            'warning' => $warning->publicPayload(),
        ]);
    }
}