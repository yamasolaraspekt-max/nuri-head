<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AddEmployeeToProject;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeProblem;
use App\Models\EmployeeSick;
use App\Models\EmployeesPersonalTask;
use App\Models\Leave;
use App\Models\MainAppointmentEmployee;
use App\Models\TimeManagementPlan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeCapacityStateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * AJAX / JSON capacity data.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'day');
        $date = Carbon::parse($request->input('date', now()));
        $range = $this->getDateRange($date, $period);

        $employees = $this->employeeBaseQuery($request)->get();

        $result = [];
        $montageCount = 0;
        $officeCount = 0;
        $unknownCount = 0;
        $leaveCount = 0;
        $sickCount = 0;

        foreach ($employees as $employee) {
            $dist = $this->computeEmployeeOfficeMontage($employee);
            $employeeType = $dist['type'];

            if ($employeeType === 'Montage') {
                $montageCount++;
            } elseif ($employeeType === 'Office') {
                $officeCount++;
            } else {
                $unknownCount++;
            }

            $appointments = $this->fetchAppointments($employee->id, $range);
            $tasks = $this->fetchTasks($employee->id, $range);
            $projects = $this->fetchProjects($employee->id, $range);
            $problems = $this->fetchProblems($employee->id, $range);
            $leaves = $this->fetchLeaves($employee->id, $range);
            $sicks = $this->fetchSicks($employee->id, $range);

            if ($leaves->isNotEmpty()) {
                $leaveCount++;
            }

            if ($sicks->isNotEmpty()) {
                $sickCount++;
            }

            $allItems = collect()
                ->merge($appointments)
                ->merge($tasks)
                ->merge($projects)
                ->merge($problems)
                ->values();

            Log::info('Capacity employee debug', [
                'employee_id' => $employee->id,
                'range_from' => $range['from'],
                'range_to' => $range['to'],
                'appointments' => $appointments->count(),
                'tasks' => $tasks->count(),
                'projects' => $projects->count(),
                'problems' => $problems->count(),
                'leaves' => $leaves->count(),
                'sicks' => $sicks->count(),
            ]);

            $calendar = collect($range['period'])->map(function (Carbon $day) use ($employee, $allItems, $leaves, $sicks) {
                $dateStr = $day->format('Y-m-d');

                $isLeave = $this->collectionHasDateOverlap($leaves, $dateStr);
                $isSick = $this->collectionHasDateOverlap($sicks, $dateStr);

                $capacity = ($isLeave || $isSick)
                    ? 0.0
                    : $this->dailyNetHoursForEmployee($employee, $day);

                $items = $allItems
                    ->where('date', $dateStr)
                    ->values();

                $busyHours = (float) $items->sum('hours');

                return [
                    'date' => $dateStr,
                    'working_hours' => round($capacity, 2),
                    'busy_hours' => round($busyHours, 2),
                    'free_hours' => round(max(0, $capacity - $busyHours), 2),
                    'is_leave' => $isLeave,
                    'is_sick' => $isSick,
                    'items' => $items,
                ];
            })->values();

            $calendarData = $period === 'year'
                ? $calendar
                    ->groupBy(fn($d) => Carbon::parse($d['date'])->format('F'))
                    ->map(function ($days, $month) {
                        return [
                            'month' => $month,
                            'working_hours' => round($days->sum('working_hours'), 2),
                            'busy_hours' => round($days->sum('busy_hours'), 2),
                            'free_hours' => round($days->sum('free_hours'), 2),
                            'leave_days' => $days->where('is_leave', true)->count(),
                            'sick_days' => $days->where('is_sick', true)->count(),
                            'items' => $days->flatMap(fn($d) => $d['items'])->values(),
                        ];
                    })
                    ->values()
                : $calendar;

            $totalCapacityForPeriod = (float) $calendar->sum('working_hours');

            $officeHours = $totalCapacityForPeriod * $dist['office_share'];
            $montageHours = $totalCapacityForPeriod * $dist['montage_share'];

            $deptPositions = $employee->departmentPositions->map(function ($pos) {
                return [
                    'department' => optional($pos->department)->department_name,
                    'position' => optional($pos->position)->position,
                    'percent' => (float) ($pos->percent ?? 0),
                    'montage_percent' => (float) ($pos->montage_percent ?? 0),
                    'office_percent' => (float) ($pos->office_percent ?? 0),
                    'working_hours' => (float) ($pos->working_hours ?? 0),
                    'main' => $pos->main,
                ];
            })->values();

            $result[] = [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $this->employeeName($employee),
                    'image' => asset('images/employee/' . ($employee->image ?: 'default.jpg')),
                    'working_hour' => round($this->dailyNetHoursForEmployee($employee, $date), 2),
                    'department' => optional(optional($employee->employeeDepartments->first())->department)->department_name,
                    'position' => optional(optional($employee->departmentPositions->first())->position)->position,
                    'type' => $employeeType,
                    'department_positions' => $deptPositions,
                    'office_percent_total' => $dist['office_percent'],
                    'montage_percent_total' => $dist['montage_percent'],
                    'office_hours' => round($officeHours, 2),
                    'montage_hours' => round($montageHours, 2),
                ],
                'calendar' => $calendarData,
            ];
        }

        $employeesSorted = collect($result)
            ->sortBy(fn($item) => match ($item['employee']['type']) {
                'Montage' => 1,
                'Office' => 2,
                default => 3,
            })
            ->values();

        return response()->json([
            'employees' => $employeesSorted,
            'period' => $period,
            'from' => $range['from'],
            'to' => $range['to'],
            'stats' => [
                'total' => $employees->count(),
                'montage' => $montageCount,
                'office' => $officeCount,
                'unknown' => $unknownCount,
                'leave' => $leaveCount,
                'sick' => $sickCount,
            ],
        ]);
    }

    /**
     * Main capacity page.
     */
    public function view()
    {
        $data['employees'] = Employee::with([
            'employeeDepartments.department:id,department_name',
            'departmentPositions.position:id,position,status',
            'departmentPositions.department:id,department_name',
        ])
            ->select('id', 'title', 'name', 'lastname', 'image', 'gender', 'status', 'branch')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['departments'] = Department::select('id', 'department_name')
            ->orderBy('department_name')
            ->get();

        $data['branches'] = DB::table('branches')
            ->select('branches.id', 'branches.branch as branch_name')
            ->orderBy('branches.branch')
            ->get();

        return view('admin.capacity.index', $data);
    }

    /**
     * Summary chart endpoint.
     */
    public function summary(Request $request)
    {
        $period = $request->input('period', 'day');
        $date = Carbon::parse($request->input('date', now()));
        $range = $this->getDateRange($date, $period);

        $employees = $this->employeeBaseQuery($request)->get();

        $totalHours = 0.0;
        $busyHours = 0.0;
        $labels = [];
        $busySeries = [];
        $freeSeries = [];
        $totalSeries = [];

        $periodDays = collect($range['period']);
        $chunkSize = max(1, (int) ceil($periodDays->count() / 6));
        $chunks = $periodDays->chunk($chunkSize);

        foreach ($chunks as $chunk) {
            $chunkTotal = 0.0;
            $chunkBusy = 0.0;

            $from = $chunk->first()->format('Y-m-d');
            $to = $chunk->last()->format('Y-m-d');

            $chunkRange = [
                'from' => $from,
                'to' => $to,
                'period' => $chunk->values(),
            ];

            foreach ($employees as $employee) {
                $leaves = $this->fetchLeaves($employee->id, $chunkRange);
                $sicks = $this->fetchSicks($employee->id, $chunkRange);

                foreach ($chunk as $day) {
                    $dateStr = $day->format('Y-m-d');

                    $isLeave = $this->collectionHasDateOverlap($leaves, $dateStr);
                    $isSick = $this->collectionHasDateOverlap($sicks, $dateStr);

                    $capacity = ($isLeave || $isSick)
                        ? 0.0
                        : $this->dailyNetHoursForEmployee($employee, $day);

                    $chunkTotal += $capacity;
                }

                $chunkBusy += $this->busyHoursForEmployeeInRange($employee->id, $chunkRange);
            }

            $totalHours += $chunkTotal;
            $busyHours += $chunkBusy;

            $totalSeries[] = round($chunkTotal, 2);
            $busySeries[] = round($chunkBusy, 2);
            $freeSeries[] = round(max(0, $chunkTotal - $chunkBusy), 2);
            $labels[] = $chunk->first()->format('d.m');
        }

        $freeHours = max(0, $totalHours - $busyHours);
        $utilization = $totalHours > 0
            ? round(($busyHours / $totalHours) * 100, 2)
            : 0.0;

        return response()->json([
            'total_hours' => round($totalHours, 2),
            'total_busy' => round($busyHours, 2),
            'total_free' => round($freeHours, 2),
            'utilization_percent' => $utilization,
            'chart' => [
                'labels' => $labels,
                'total' => $totalSeries,
                'busy' => $busySeries,
                'free' => $freeSeries,
            ],
        ]);
    }

    /**
     * Terminal page.
     */
    public function terminal(Request $request)
    {
        $period = $request->input('period', 'day');
        $date = Carbon::parse($request->input('date', Carbon::today()));
        $range = $this->getDateRange($date, $period);

        $employees = $this->employeeBaseQuery($request)
            ->whereNull('deleted_at')
            ->get();

        $result = [];
        $montageCount = 0;
        $officeCount = 0;
        $unknownCount = 0;
        $leaveCount = 0;
        $sickCount = 0;

        $totalHours = 0.0;
        $usedHours = 0.0;

        $montageHours = 0.0;
        $officeHours = 0.0;
        $unknownHours = 0.0;
        $leaveHours = 0.0;
        $sickHours = 0.0;

        $todayStr = $date->format('Y-m-d');

        foreach ($employees as $employee) {
            $dist = $this->computeEmployeeOfficeMontage($employee);
            $employeeType = $dist['type'];

            if ($employeeType === 'Montage') {
                $montageCount++;
            } elseif ($employeeType === 'Office') {
                $officeCount++;
            } else {
                $unknownCount++;
            }

            $todayNet = $this->dailyNetHoursForEmployee($employee, $date);

            $leaves = $this->fetchLeaves($employee->id, $range);
            $sicks = $this->fetchSicks($employee->id, $range);

            $isLeave = $this->collectionHasDateOverlap($leaves, $todayStr);
            $isSick = $this->collectionHasDateOverlap($sicks, $todayStr);

            $todayCapacity = ($isLeave || $isSick) ? 0.0 : $todayNet;

            if ($isLeave) {
                $leaveCount++;
                $leaveHours += $todayNet;
            }

            if ($isSick) {
                $sickCount++;
                $sickHours += $todayNet;
            }

            switch ($employeeType) {
                case 'Montage':
                    $montageHours += $todayCapacity;
                    break;

                case 'Office':
                    $officeHours += $todayCapacity;
                    break;

                default:
                    $unknownHours += $todayCapacity;
                    break;
            }

            $appointments = $this->fetchAppointments($employee->id, $range);
            $tasks = $this->fetchTasks($employee->id, $range);
            $projects = $this->fetchProjects($employee->id, $range);
            $problems = $this->fetchProblems($employee->id, $range);

            $allItems = collect()
                ->merge($appointments)
                ->merge($tasks)
                ->merge($projects)
                ->merge($problems)
                ->values();

            $todayItems = $allItems
                ->where('date', $todayStr)
                ->values();

            $busyHours = (float) $todayItems->sum('hours');

            $result[] = [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $this->employeeName($employee),
                    'image' => asset('images/employee/' . ($employee->image ?: 'default.jpg')),
                    'working_hour' => round($todayCapacity, 2),
                    'department' => optional(optional($employee->employeeDepartments->first())->department)->department_name,
                    'position' => optional(optional($employee->departmentPositions->first())->position)->position,
                    'type' => $employeeType,
                ],
                'today' => [
                    'date' => $todayStr,
                    'working_hours' => round($todayCapacity, 2),
                    'busy_hours' => round($busyHours, 2),
                    'free_hours' => round(max(0, $todayCapacity - $busyHours), 2),
                    'is_leave' => $isLeave,
                    'is_sick' => $isSick,
                    'items' => $todayItems,
                ],
            ];

            $totalHours += $todayCapacity;
            $usedHours += $busyHours;
        }

        $freeHours = max(0, $totalHours - $usedHours);
        $loadPercentage = $totalHours > 0
            ? round(($usedHours / $totalHours) * 100, 2)
            : 0.0;

        $availableCount = $employees->count();

        $employeesList = Employee::select('id', 'name', 'lastname', 'image')
            ->where('status', '!=', 'Deactive')
            ->orderBy('name')
            ->get();

        $departments = Department::orderBy('department_name')->get();
        $branches = Branch::orderBy('branch')->get();

        return view('admin.capacity.terminal', compact(
            'result',
            'totalHours',
            'usedHours',
            'freeHours',
            'loadPercentage',
            'montageCount',
            'officeCount',
            'unknownCount',
            'leaveCount',
            'sickCount',
            'availableCount',
            'employeesList',
            'departments',
            'branches',
            'montageHours',
            'officeHours',
            'unknownHours',
            'leaveHours',
            'sickHours',
            'period',
            'date'
        ));
    }

    /**
     * Shared employee query.
     */
    private function employeeBaseQuery(Request $request)
    {
        $query = Employee::with([
            'departmentPositions.position',
            'departmentPositions.department',
            'employeeDepartments.department',
        ])
            ->where('status', '!=', 'Deactive');

        if ($request->filled('office')) {
            $query->whereHas('departmentPositions', function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    if ($request->office === 'Montage') {
                        $sub->where('montage_percent', '>', 50);
                    } elseif ($request->office === 'Office') {
                        $sub->where('office_percent', '>', 50);
                    }
                });
            });
        }

        if ($request->filled('department')) {
            $query->whereHas('employeeDepartments', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }

        if ($request->filled('employee_id')) {
            $query->where('id', $request->employee_id);
        }

        return $query;
    }

    /**
     * Time management planned hours.
     */
    private function timeManagementHoursForEmployeeOnDate(Employee $employee, Carbon $day): ?float
    {
        $plan = TimeManagementPlan::where('employee_id', $employee->id)
            ->where('year', $day->year)
            ->where('month', $day->month)
            ->first();

        if (!$plan) {
            return null;
        }

        $entry = $plan->entries()
            ->whereDate('work_date', $day->toDateString())
            ->first();

        if (!$entry) {
            return 0.0;
        }

        return (float) ($entry->hours ?? 0);
    }

    /**
     * Appointments assigned to employee.
     */
    private function fetchAppointments($employeeId, array $range): Collection
    {
        return MainAppointmentEmployee::where('employee_id', $employeeId)
            ->whereHas('appointment', function ($q) use ($range) {
                $q->whereDate('start_date', '>=', $range['from'])
                    ->whereDate('start_date', '<=', $range['to']);
            })
            ->with('appointment')
            ->get()
            ->filter(fn($a) => $a->appointment)
            ->map(function ($a) {
                $appointment = $a->appointment;

                return [
                    'id' => $appointment->id,
                    'type' => 'Appointment',
                    'date' => $this->normalizeDate($appointment->start_date),
                    'start' => $appointment->start_time,
                    'end' => $appointment->end_time,
                    'hours' => $this->calculateHours($appointment->start_time, $appointment->end_time),
                    'title' => $appointment->name,
                    'location' => $appointment->full_address,
                    'note' => $appointment->note,
                    'color' => $appointment->color,
                ];
            })
            ->values();
    }

    /**
     * Personal tasks assigned to employee.
     */
    private function fetchTasks($employeeId, array $range): Collection
    {
        return EmployeesPersonalTask::where('employee_id', $employeeId)
            ->whereHas('task', function ($q) use ($range) {
                $q->whereDate('start_date', '>=', $range['from'])
                    ->whereDate('start_date', '<=', $range['to']);
            })
            ->with('task')
            ->get()
            ->filter(fn($t) => $t->task)
            ->map(function ($t) {
                $task = $t->task;

                return [
                    'id' => $task->id,
                    'type' => 'Task',
                    'date' => $this->normalizeDate($task->start_date),
                    'hours' => (float) ($task->total_time ?? $t->total_hour ?? 0),
                    'title' => $task->task_title,
                    'note' => $task->description,
                    'progress' => $task->progress,
                    'color' => $task->color,
                ];
            })
            ->values();
    }

    /**
     * Projects assigned to employee.
     */
    private function fetchProjects($employeeId, array $range): Collection
    {
        return AddEmployeeToProject::where('employee_id', $employeeId)
            ->whereHas('project', function ($q) use ($range) {
                $q->whereDate('project_start', '>=', $range['from'])
                    ->whereDate('project_start', '<=', $range['to']);
            })
            ->with('project')
            ->get()
            ->filter(fn($p) => $p->project)
            ->map(function ($p) {
                $project = $p->project;

                return [
                    'id' => $project->id,
                    'type' => 'Project',
                    'date' => $this->normalizeDate($project->project_start),
                    'hours' => (float) ($project->total_time ?? 0),
                    'title' => $project->project_name ?? $project->name ?? 'Projektarbeit',
                    'note' => $project->status_msg,
                    'progress' => $project->progress,
                    'color' => $project->color,
                ];
            })
            ->values();
    }

    /**
     * Tickets / problems assigned to employee.
     */
    private function fetchProblems($employeeId, array $range): Collection
    {
        return EmployeeProblem::where('employee_id', $employeeId)
            ->whereHas('problem', function ($q) use ($range) {
                $q->whereDate('date', '>=', $range['from'])
                    ->whereDate('date', '<=', $range['to']);
            })
            ->with('problem')
            ->get()
            ->filter(fn($p) => $p->problem)
            ->map(function ($p) {
                $problem = $p->problem;

                return [
                    'id' => $problem->id,
                    'type' => 'Ticket',
                    'date' => $this->normalizeDate($problem->date),
                    'hours' => (float) ($problem->total_time ?? 0),
                    'title' => 'Ticket #' . ($problem->ticket_no ?? $problem->id),
                    'note' => $problem->problem,
                    'color' => null,
                ];
            })
            ->values();
    }

    /**
     * Approved leaves that overlap selected range.
     */
    private function fetchLeaves($employeeId, array $range): Collection
    {
        return Leave::where('emp_id', $employeeId)
            ->where('approved', 'Yes')
            ->whereDate('start_date', '<=', $range['to'])
            ->whereDate('end_date', '>=', $range['from'])
            ->get();
    }

    /**
     * Sick records that overlap selected range.
     */
    private function fetchSicks($employeeId, array $range): Collection
    {
        return EmployeeSick::where('emp_id', $employeeId)
            ->whereDate('start_date', '<=', $range['to'])
            ->whereDate('end_date', '>=', $range['from'])
            ->get();
    }

    /**
     * Total busy hours for summary.
     */
    private function busyHoursForEmployeeInRange($employeeId, array $range): float
    {
        $appointments = $this->fetchAppointments($employeeId, $range)->sum('hours');
        $tasks = $this->fetchTasks($employeeId, $range)->sum('hours');
        $projects = $this->fetchProjects($employeeId, $range)->sum('hours');
        $problems = $this->fetchProblems($employeeId, $range)->sum('hours');

        return (float) ($appointments + $tasks + $projects + $problems);
    }

    /**
     * Date range by period.
     */
    private function getDateRange(Carbon $date, string $period): array
    {
        $dates = match ($period) {
            'week' => [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()],
            'month' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
            'year' => [$date->copy()->startOfYear(), $date->copy()->endOfYear()],
            default => [$date->copy()->startOfDay(), $date->copy()->endOfDay()],
        };

        return [
            'from' => $dates[0]->format('Y-m-d'),
            'to' => $dates[1]->format('Y-m-d'),
            'period' => collect(CarbonPeriod::create($dates[0]->copy()->startOfDay(), $dates[1]->copy()->startOfDay())),
        ];
    }

    /**
     * Normalize any date/datetime/Carbon to Y-m-d.
     */
    private function normalizeDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * Check leave/sick collection for a concrete day.
     */
    private function collectionHasDateOverlap(Collection $records, string $dateStr): bool
    {
        return $records->contains(function ($record) use ($dateStr) {
            $start = $this->normalizeDate($record->start_date ?? null);
            $end = $this->normalizeDate($record->end_date ?? null);

            if (!$start || !$end) {
                return false;
            }

            return $dateStr >= $start && $dateStr <= $end;
        });
    }

    /**
     * Calculate hours from start and end time.
     */
    private function calculateHours($start, $end): float
    {
        if (!$start || !$end) {
            return 0.0;
        }

        try {
            return max(0.0, Carbon::parse($start)->floatDiffInHours(Carbon::parse($end)));
        } catch (\Throwable $e) {
            Log::warning('Capacity calculateHours failed', [
                'start' => $start,
                'end' => $end,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Employee display name.
     */
    private function employeeName(Employee $employee): string
    {
        return trim(collect([
            $employee->title,
            $employee->name,
            $employee->lastname,
        ])->filter()->join(' ')) ?: 'Employee #' . $employee->id;
    }

    /**
     * Büro / Montage distribution.
     */
    private function computeEmployeeOfficeMontage(Employee $employee): array
    {
        $positions = $employee->departmentPositions ?? collect();

        $montagePercent = $positions->sum(fn($p) => (float) ($p->montage_percent ?? 0));
        $officePercent = $positions->sum(fn($p) => (float) ($p->office_percent ?? 0));

        $totalPercent = $montagePercent + $officePercent;

        if ($totalPercent <= 0) {
            return [
                'montage_percent' => 0.0,
                'office_percent' => 0.0,
                'montage_share' => 0.0,
                'office_share' => 0.0,
                'type' => 'Unbekannt',
            ];
        }

        $montageShare = $montagePercent / $totalPercent;
        $officeShare = $officePercent / $totalPercent;

        return [
            'montage_percent' => round($montagePercent, 2),
            'office_percent' => round($officePercent, 2),
            'montage_share' => $montageShare,
            'office_share' => $officeShare,
            'type' => $montageShare >= $officeShare ? 'Montage' : 'Office',
        ];
    }

    /**
     * German legal break approximation.
     */
    private function legalBreakMinutesForHours(float $grossHours): int
    {
        if ($grossHours <= 6) {
            return 0;
        }

        if ($grossHours <= 9) {
            return 30;
        }

        return 45;
    }

    /**
     * Gross working hours for one day.
     */
    private function dailyGrossHoursForEmployee(Employee $employee, Carbon $day): float
    {
        if (!empty($employee->daily_start_time) && !empty($employee->daily_end_time)) {
            try {
                $start = Carbon::parse($employee->daily_start_time);
                $end = Carbon::parse($employee->daily_end_time);

                return max(0.0, $start->floatDiffInHours($end));
            } catch (\Throwable $e) {
                Log::warning('Capacity dailyGrossHoursForEmployee failed', [
                    'employee_id' => $employee->id,
                    'start' => $employee->daily_start_time,
                    'end' => $employee->daily_end_time,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($employee->working_hour)) {
            return max(0.0, (float) $employee->working_hour / 5.0);
        }

        return 8.0;
    }

    /**
     * Net working hours for one day.
     */
    private function dailyNetHoursForEmployee(Employee $employee, Carbon $day): float
    {
        $tmHours = $this->timeManagementHoursForEmployeeOnDate($employee, $day);

        if ($tmHours !== null) {
            return max(0.0, (float) $tmHours);
        }

        if ($day->isWeekend()) {
            return 0.0;
        }

        $gross = $this->dailyGrossHoursForEmployee($employee, $day);
        $breakMin = $this->legalBreakMinutesForHours($gross);

        return max(0.0, $gross - ($breakMin / 60.0));
    }
}