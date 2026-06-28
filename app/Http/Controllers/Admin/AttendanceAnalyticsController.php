<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Employee;

class AttendanceAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Employees for Select2
        $employees = Employee::query()
            ->select(['id','name','lastname'])
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        return view('admin.attendance.analytics', [
            'employees' => $employees,
            'googleMapsKey' => config('services.google.maps_key') ?? env('GOOGLE_MAPS_KEY'),
        ]);
    }

    public function fetch(Request $request)
    {
        $q          = trim((string) $request->get('q', ''));
        $employeeId = $request->get('employee_id');
        $from       = (string) $request->get('from', '');
        $to         = (string) $request->get('to', '');
        $month      = (string) $request->get('month', ''); // YYYY-MM
        $status     = (string) $request->get('status', ''); // login/logout/work_start...
        $page       = max(1, (int) $request->get('page', 1));
        $perPage    = min(100, max(10, (int) $request->get('per_page', 20)));

        // Default: current month
        $rangeFrom = null;
        $rangeTo   = null;

        if ($month !== '') {
            try {
                $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
                $rangeFrom = $start->toDateString();
                $rangeTo   = $end->toDateString();
            } catch (\Throwable $e) {
                // ignore invalid month
            }
        }

        // If no month (or invalid), use from/to if provided
        if (!$rangeFrom || !$rangeTo) {
            if ($from !== '' && $to !== '') {
                $rangeFrom = $from;
                $rangeTo   = $to;
            } elseif ($from !== '' && $to === '') {
                // specific day
                $rangeFrom = $from;
                $rangeTo   = $from;
            } elseif ($from === '' && $to !== '') {
                $rangeFrom = $to;
                $rangeTo   = $to;
            } else {
                $rangeFrom = now()->startOfMonth()->toDateString();
                $rangeTo   = now()->endOfMonth()->toDateString();
            }
        }

        $base = Attendance::query()
            ->with(['employee:id,name,lastname'])
            ->whereBetween('date', [$rangeFrom, $rangeTo])
            ->when($employeeId, fn($qq) => $qq->where('employee_id', $employeeId))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->whereHas('employee', function ($e) use ($q) {
                    $e->where(DB::raw("CONCAT(COALESCE(name,''),' ',COALESCE(lastname,''))"), 'LIKE', "%{$q}%")
                      ->orWhere('name', 'LIKE', "%{$q}%")
                      ->orWhere('lastname', 'LIKE', "%{$q}%");
                });
            })
            ->when($status !== '', function ($qq) use ($status) {
                $qq->where(function ($w) use ($status) {
                    $w->where('check_in_status', $status)
                      ->orWhere('check_out_status', $status);
                });
            });

        // Stats (clone query)
        $statsQ = (clone $base);

        $totalRows     = (clone $statsQ)->count();
        $uniqueEmp     = (clone $statsQ)->distinct('employee_id')->count('employee_id');
        $withCheckIn   = (clone $statsQ)->whereNotNull('check_in')->count();
        $withCheckOut  = (clone $statsQ)->whereNotNull('check_out')->count();
        $missingIn     = max(0, $totalRows - $withCheckIn);
        $missingOut    = max(0, $totalRows - $withCheckOut);

        // Avg hours (only rows with both timestamps)
        $avgSeconds = (clone $statsQ)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, check_in, check_out)) as avg_sec')
            ->value('avg_sec');

        $avgHours = $avgSeconds ? round(((float)$avgSeconds) / 3600, 2) : 0;

        // Pagination
        $rows = (clone $base)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->forPage($page, $perPage)
            ->get();

        $mapped = $rows->map(function ($a) {
            $empName = trim(($a->employee->name ?? '').' '.($a->employee->lastname ?? ''));
            $metaArr = [];

            if (is_array($a->meta)) {
                $metaArr = $a->meta;
            } elseif (is_string($a->meta) && $a->meta !== '') {
                $metaArr = json_decode($a->meta, true) ?: [];
            }

            // "reason" does not exist in schema → try meta.reason/meta.note/meta.message
            $reason = $metaArr['reason'] ?? $metaArr['note'] ?? $metaArr['message'] ?? null;

            $ip = $metaArr['ip'] ?? ($metaArr['last']['ip'] ?? null);
            $ua = $metaArr['ua'] ?? ($metaArr['last']['ua'] ?? null);

            $durHours = null;
            if ($a->check_in && $a->check_out) {
                $durHours = round(Carbon::parse($a->check_in)->diffInSeconds(Carbon::parse($a->check_out)) / 3600, 2);
            }

            return [
                'id' => $a->id,
                'employee_id' => $a->employee_id,
                'employee_name' => $empName ?: ('Mitarbeiter #'.$a->employee_id),

                'date' => (string) $a->date,

                'check_in' => $a->check_in ? Carbon::parse($a->check_in)->format('Y-m-d H:i:s') : null,
                'check_out' => $a->check_out ? Carbon::parse($a->check_out)->format('Y-m-d H:i:s') : null,

                'check_in_status' => $a->check_in_status,
                'check_out_status' => $a->check_out_status,

                'check_in_lat' => $a->check_in_lat,
                'check_in_lng' => $a->check_in_lng,
                'check_in_location' => $a->check_in_location,
                'check_in_accuracy' => $a->check_in_accuracy,

                'check_out_lat' => $a->check_out_lat,
                'check_out_lng' => $a->check_out_lng,
                'check_out_location' => $a->check_out_location,
                'check_out_accuracy' => $a->check_out_accuracy,

                'reason' => $reason,
                'ip' => $ip,
                'ua' => $ua,

                'duration_hours' => $durHours,
                'meta' => $metaArr,
                'created_at' => $a->created_at ? $a->created_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $hasMore = ($page * $perPage) < $totalRows;

        return response()->json([
            'ok' => true,
            'range' => [
                'from' => $rangeFrom,
                'to' => $rangeTo,
            ],
            'stats' => [
                'total' => $totalRows,
                'unique_employees' => $uniqueEmp,
                'with_check_in' => $withCheckIn,
                'with_check_out' => $withCheckOut,
                'missing_check_in' => $missingIn,
                'missing_check_out' => $missingOut,
                'avg_hours' => $avgHours,
            ],
            'rows' => $mapped,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalRows,
                'has_more' => $hasMore,
            ],
        ]);
    }
}
