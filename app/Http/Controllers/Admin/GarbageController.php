<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SoftDeletedGarbageCollector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GarbageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function ensureDeletePermission(): void
    {
        $user = auth()->user();

        abort_if(!$user, 403);

        /**
         * In your app, auth()->user()->name stores the employee ID.
         * So we check user_rolls.user_id against auth()->user()->name.
         */
        $hasPermission = DB::table('user_rolls')
            ->where('user_id', $user->name)
            ->where('item_id', 'Administrator')
            ->where(function ($query) {
                $query->where('is_delete', 1)
                    ->orWhere('is_delete', '1')
                    ->orWhere('is_delete', 'on')
                    ->orWhere('is_delete', true);
            })
            ->exists();

        abort_unless($hasPermission, 403, 'You do not have permission to delete garbage records.');
    }

    public function index(Request $request, SoftDeletedGarbageCollector $collector)
    {
        $this->ensureDeletePermission();

        $mode = $request->input('mode', 'older');
        $months = max((int) $request->input('months', 2), 1);

        $all = $mode === 'all';
        $olderThan = $all ? null : now()->subMonths($months);

        $result = $collector->preview(
            olderThan: $olderThan,
            all: $all
        );

        $tables = collect($result['tables'])
            ->map(function ($stats, $table) {
                return [
                    'table' => $table,
                    'count' => $stats['count'],
                    'oldest_deleted_at' => $stats['oldest_deleted_at'],
                    'newest_deleted_at' => $stats['newest_deleted_at'],
                ];
            })
            ->sortByDesc('count')
            ->values();

        $totalTables = $tables->count();
        $tablesWithGarbage = $tables->where('count', '>', 0)->count();
        $totalGarbage = $tables->sum('count');

        return view('admin.garbage.index', [
            'tables' => $tables,
            'result' => $result,
            'mode' => $mode,
            'months' => $months,
            'all' => $all,
            'olderThan' => $olderThan,
            'totalTables' => $totalTables,
            'tablesWithGarbage' => $tablesWithGarbage,
            'totalGarbage' => $totalGarbage,
        ]);
    }

    public function deleteTable(Request $request, SoftDeletedGarbageCollector $collector)
    {
        $this->ensureDeletePermission();

        $validated = $request->validate([
            'table' => ['required', 'string'],
            'mode' => ['nullable', 'in:older,all'],
            'months' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        $mode = $validated['mode'] ?? 'older';
        $months = (int) ($validated['months'] ?? 2);

        $all = $mode === 'all';
        $olderThan = $all ? null : now()->subMonths($months);

        $result = $collector->purge(
            olderThan: $olderThan,
            all: $all,
            onlyTables: [$validated['table']]
        );

        $deleted = $result['tables'][$validated['table']] ?? 0;

        if (!empty($result['errors'])) {
            return back()->with('error', 'Could not delete table garbage: ' . implode(' | ', $result['errors']));
        }

        return back()->with('success', "Garbage deleted from {$validated['table']}. Total deleted: {$deleted}");
    }

    public function bulkDelete(Request $request, SoftDeletedGarbageCollector $collector)
    {
        $this->ensureDeletePermission();

        $validated = $request->validate([
            'tables' => ['required', 'array', 'min:1'],
            'tables.*' => ['required', 'string'],
            'mode' => ['nullable', 'in:older,all'],
            'months' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        $mode = $validated['mode'] ?? 'older';
        $months = (int) ($validated['months'] ?? 2);

        $all = $mode === 'all';
        $olderThan = $all ? null : now()->subMonths($months);

        $result = $collector->purge(
            olderThan: $olderThan,
            all: $all,
            onlyTables: $validated['tables']
        );

        if (!empty($result['errors'])) {
            return back()
                ->with('error', 'Some tables could not be cleaned: ' . implode(' | ', $result['errors']))
                ->with('garbage_result', $result);
        }

        return back()
            ->with('success', 'Selected garbage deleted. Total deleted: ' . $result['total'])
            ->with('garbage_result', $result);
    }

    public function deleteAll(Request $request, SoftDeletedGarbageCollector $collector)
    {
        $this->ensureDeletePermission();

        $validated = $request->validate([
            'mode' => ['nullable', 'in:older,all'],
            'months' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        $mode = $validated['mode'] ?? 'older';
        $months = (int) ($validated['months'] ?? 2);

        $all = $mode === 'all';
        $olderThan = $all ? null : now()->subMonths($months);

        $result = $collector->purge(
            olderThan: $olderThan,
            all: $all
        );

        if (!empty($result['errors'])) {
            return back()
                ->with('error', 'Some tables could not be cleaned: ' . implode(' | ', $result['errors']))
                ->with('garbage_result', $result);
        }

        return back()
            ->with('success', 'All garbage deleted. Total deleted: ' . $result['total'])
            ->with('garbage_result', $result);
    }
}