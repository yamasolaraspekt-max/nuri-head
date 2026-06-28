<?php

namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\DailyReportTime;
use App\Models\Employee;
use App\Models\NewLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DailyReportWorkPlace;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\AppointmentReport;
use App\Models\PersonalTaskComment;
use App\Models\TicketReport;
use App\Models\InquiryReport;
use App\Models\MainAppointment;
use App\Models\PersonalTask;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Offer;


use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('admin.daily_report.report.report');
    }


    // In App\Http\Controllers\DailyReportController.php

    public function report($employee_id, $start_date = null, $end_date = null)
    {
        $start_date = $start_date ?? Carbon::today()->toDateString();
        $end_date = $end_date ?? $start_date;

        // Use the unified builder
        $reportData = $this->buildReportEntries($employee_id, $start_date, $end_date);

        $customers = DB::table('new_leads')
            ->whereNotIn('status', ['junk', 'Junk', 'deleted'])
            ->whereNull('deleted_at')
            ->get();

        $customerWorkOptions = $this->customerWorkOptionsFor($customers->pluck('id')->all());

        $fullName = DB::table('employees')
            ->where('status', '!=', 'Inactive')
            ->where('id', $employee_id)
            ->select('id', 'name', 'lastname')
            ->first();

        $employee_name = $fullName ? $fullName->name . ' ' . $fullName->lastname : 'Unknown';

        return view('admin.daily_report.report.report', [
            'employee_id' => $employee_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'entries' => $reportData['entries'],
            'customers' => $customers,
            'customerWorkOptions' => $customerWorkOptions,
            'employee_name' => $employee_name,
            'expectedHoursForDay' => $reportData['expected'],
        ]);
    }

    // Add this method inside App\Http\Controllers\Report\DailyReportController
    public function monthAnalytics($employee_id): \Illuminate\Http\JsonResponse
    {
        $date = request()->query('date') ?: now()->toDateString();
        $selected = \Carbon\Carbon::parse($date);

        $monthStart = $selected->copy()->startOfMonth();
        $monthEnd = $selected->copy()->endOfMonth();
        $today = now()->startOfDay();

        $rows = \App\Models\DailyReportTime::query()
            ->where('employee_id', $employee_id)
            ->whereBetween('report_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->selectRaw('DATE(report_date) as report_day, SUM(ABS(COALESCE(hours_spent,0))) as worked_hours, COUNT(*) as rows_count')
            ->groupByRaw('DATE(report_date)')
            ->get()
            ->keyBy('report_day');

        $days = [];
        $totalDays = $monthStart->daysInMonth;
        $elapsedDays = 0;
        $requiredDays = 0;
        $reportedDays = 0;
        $missingDays = 0;
        $futureDays = 0;
        $offDays = 0;
        $workedHours = 0.0;
        $expectedHours = 0.0;
        $missingHours = 0.0;

        for ($day = $monthStart->copy(); $day->lte($monthEnd); $day->addDay()) {
            $iso = $day->toDateString();
            $isFuture = $day->startOfDay()->gt($today);

            $expected = (float) $this->expectedHoursForDay((int) $employee_id, $day->copy());
            $isOffDay = $expected <= 0;
            $worked = isset($rows[$iso]) ? (float) $rows[$iso]->worked_hours : 0.0;
            $hasReport = isset($rows[$iso]) && (int) $rows[$iso]->rows_count > 0;

            if ($isFuture) {
                $futureDays++;
            } else {
                $elapsedDays++;
            }

            if ($isOffDay) {
                $offDays++;
            }

            if (!$isFuture && !$isOffDay) {
                $requiredDays++;
                $expectedHours += $expected;

                if (!$hasReport) {
                    $missingDays++;
                }
            }

            if ($hasReport) {
                $reportedDays++;
            }

            $workedHours += $worked;

            if (!$isFuture && !$isOffDay) {
                $missingHours += max(0, $expected - $worked);
            }

            $statusLabel = 'Offen';
            if ($isFuture) {
                $statusLabel = 'Zukünftig';
            } elseif ($isOffDay) {
                $statusLabel = 'Frei';
            } elseif ($hasReport) {
                $statusLabel = 'Bericht vorhanden';
            }

            $days[] = [
                'date' => $iso,
                'day' => (int) $day->day,
                'date_label' => $day->format('d.m.Y'),
                'weekday_label' => $day->locale('de')->translatedFormat('D'),
                'has_report' => $hasReport,
                'is_future' => $isFuture,
                'is_off_day' => $isOffDay,
                'worked_hours' => round($worked, 2),
                'expected_hours' => round($expected, 2),
                'missing_hours' => round(!$isFuture && !$isOffDay ? max(0, $expected - $worked) : 0, 2),
                'status_label' => $statusLabel,
            ];
        }

        $coveragePercent = $requiredDays > 0
            ? round(($reportedDays / $requiredDays) * 100)
            : 0;

        return response()->json([
            'success' => true,
            'employee_id' => (int) $employee_id,
            'month' => $selected->format('Y-m'),
            'month_label' => $selected->locale('de')->translatedFormat('F Y'),
            'total_days' => $totalDays,
            'elapsed_days' => $elapsedDays,
            'future_days' => $futureDays,
            'off_days' => $offDays,
            'required_days' => $requiredDays,
            'reported_days' => $reportedDays,
            'missing_days' => $missingDays,
            'worked_hours' => round($workedHours, 2),
            'expected_hours' => round($expectedHours, 2),
            'missing_hours' => round($missingHours, 2),
            'coverage_percent' => $coveragePercent,
            'days' => $days,
        ]);
    }
    private function buildCustomerPivotSync(
        ?float $totalHours,
        array $ids,
        array $hoursMap,
        array $percentMap,
        array $noteMap = [],
        array $startTimeMap = [],
        array $endTimeMap = [],
        array $alternativeMap = [],
        array $leadProductListMap = [],
        array $productMap = []
    ): array {
        $ids = array_values(array_unique(array_filter($ids)));
        if (empty($ids)) {
            return [];
        }

        $pivotTable = 'daily_report_time_customers';
        $hasTable = Schema::hasTable($pivotTable);

        $hasShareStart = $hasTable && Schema::hasColumn($pivotTable, 'share_start_time');
        $hasShareEnd = $hasTable && Schema::hasColumn($pivotTable, 'share_end_time');
        $hasAlternative = $hasTable && Schema::hasColumn($pivotTable, 'alternative_id');
        $hasLeadProduct = $hasTable && Schema::hasColumn($pivotTable, 'lead_product_list_id');
        $hasProduct = $hasTable && Schema::hasColumn($pivotTable, 'product_id');

        $valueFor = function (array $map, $key) {
            $key = (string) $key;
            $value = $map[$key] ?? $map[(int) $key] ?? null;
            return ($value === '' || $value === 'null') ? null : $value;
        };

        $buildMeta = function ($cid, array $row = []) use ($hasShareStart, $hasShareEnd, $hasAlternative, $hasLeadProduct, $hasProduct, $valueFor, $noteMap, $startTimeMap, $endTimeMap, $alternativeMap, $leadProductListMap, $productMap) {
            $cid = (string) $cid;

            $row['note'] = $noteMap[$cid] ?? $noteMap[(int) $cid] ?? null;

            if ($hasShareStart) {
                $row['share_start_time'] = $valueFor($startTimeMap, $cid);
            }

            if ($hasShareEnd) {
                $row['share_end_time'] = $valueFor($endTimeMap, $cid);
            }

            if ($hasAlternative) {
                $row['alternative_id'] = $valueFor($alternativeMap, $cid);
            }

            if ($hasLeadProduct) {
                $row['lead_product_list_id'] = $valueFor($leadProductListMap, $cid);
            }

            if ($hasProduct) {
                $row['product_id'] = $valueFor($productMap, $cid);
            }

            return $row;
        };

        $hasAnyShares =
            !empty(array_filter($hoursMap ?? [], fn($v) => $v !== null && $v !== '')) ||
            !empty(array_filter($percentMap ?? [], fn($v) => $v !== null && $v !== ''));

        $sync = [];

        if (!$hasAnyShares) {
            if ($totalHours && $totalHours > 0) {
                $per = round($totalHours / count($ids), 2);

                foreach ($ids as $cid) {
                    $sync[$cid] = $buildMeta($cid, [
                        'share_hours' => $per,
                        'share_percent' => null,
                    ]);
                }
            } else {
                $per = round(100 / count($ids), 2);

                foreach ($ids as $cid) {
                    $sync[$cid] = $buildMeta($cid, [
                        'share_hours' => null,
                        'share_percent' => $per,
                    ]);
                }
            }

            return $sync;
        }

        foreach ($ids as $cid) {
            $key = (string) $cid;
            $h = isset($hoursMap[$key]) && $hoursMap[$key] !== '' ? round((float) $hoursMap[$key], 2) : null;
            $p = isset($percentMap[$key]) && $percentMap[$key] !== '' ? round((float) $percentMap[$key], 2) : null;

            if (!is_null($h)) {
                $p = null;
            }

            $sync[$cid] = $buildMeta($cid, [
                'share_hours' => $h,
                'share_percent' => $p,
            ]);
        }

        return $sync;
    }

    private function customerWorkOptionsFor($customerIds): array
    {
        $ids = collect($customerIds)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        return DB::table('lead_product_lists as lpl')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'lpl.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->whereIn('lpl.customer_id', $ids)
            ->select(
                'lpl.id',
                'lpl.customer_id',
                'lpl.alternative_id',
                'lpl.product_id',
                'lpl.status',
                'lpl.work_status',
                'lpl.stage',
                DB::raw("COALESCE(NULLIF(alt.full_address, ''), CONCAT('Objekt #', alt.id), 'Kein Objekt') as object_label"),
                DB::raw("COALESCE(NULLIF(ag.article_group, ''), CONCAT('Produkt #', lpl.product_id), 'Kein Produkt') as product_label")
            )
            ->orderBy('lpl.customer_id')
            ->orderBy('object_label')
            ->orderBy('product_label')
            ->get()
            ->groupBy('customer_id')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    return [
                        'lead_product_list_id' => (int) $row->id,
                        'alternative_id' => $row->alternative_id ? (int) $row->alternative_id : null,
                        'product_id' => $row->product_id ? (int) $row->product_id : null,
                        'object_label' => $row->object_label,
                        'product_label' => $row->product_label,
                        'status' => $row->status,
                        'work_status' => $row->work_status,
                        'stage' => $row->stage,
                    ];
                })->values()->all();
            })
            ->toArray();
    }

    private function minutesBetween($start, $end, int $fallbackMinutes = 60): int
    {
        try {
            $startTime = $start instanceof Carbon ? $start->copy() : Carbon::parse((string) $start);
            $endTime = $end instanceof Carbon ? $end->copy() : Carbon::parse((string) $end);

            if ($endTime->lessThanOrEqualTo($startTime)) {
                $endTime = $endTime->copy()->addDay();
            }

            return max(0, (int) $startTime->diffInMinutes($endTime, false));
        } catch (\Throwable $e) {
            return max(0, $fallbackMinutes);
        }
    }

    private function hoursBetween($start, $end, float $fallbackHours = 1.0): float
    {
        $minutes = $this->minutesBetween($start, $end, (int) round($fallbackHours * 60));
        return round($minutes / 60, 2);
    }


    private function validateCustomerShareTimes(array $ids, array $startTimeMap, array $endTimeMap, float $rowHours): array
    {
        $ids = array_values(array_unique(array_filter($ids)));

        if (empty($ids)) {
            return ['ok' => true, 'message' => '', 'total' => 0.0];
        }

        $total = 0.0;

        foreach ($ids as $cid) {
            $cid = (string) $cid;
            $start = $startTimeMap[$cid] ?? null;
            $end = $endTimeMap[$cid] ?? null;

            if (!$start || !$end) {
                return [
                    'ok' => false,
                    'message' => 'Bitte bei jedem ausgewählten Kunden Start- und Endzeit eintragen.',
                    'total' => $total,
                ];
            }

            $hours = $this->hoursBetween($start, $end, 0.0);

            if ($hours <= 0) {
                return [
                    'ok' => false,
                    'message' => 'Die Kundenzeit muss größer als 0 Std. sein.',
                    'total' => $total,
                ];
            }

            $total += $hours;
        }

        if ($total > ($rowHours + 0.004)) {
            return [
                'ok' => false,
                'message' => 'Die Summe der Kundenzeiten (' . number_format($total, 2, ',', '.') . ' Std.) darf nicht größer sein als die Positionszeit (' . number_format($rowHours, 2, ',', '.') . ' Std.).',
                'total' => $total,
            ];
        }

        return ['ok' => true, 'message' => '', 'total' => round($total, 2)];
    }

    private function normalizeTypeCode(?string $type): string
    {
        $value = strtolower(trim((string) $type));

        return match ($value) {
            'task', 'aufgabe' => 'Task',
            'appointment', 'termin' => 'Appointment',
            'project', 'projekt' => 'Project',
            'offer', 'angebot' => 'Offer',
            'problem', 'ticket' => 'Problem',
            'pause' => 'Pause',
            'missing', 'fehlend' => 'Missing',
            'manual', 'manuell' => 'Manual',
            default => $type ? ucfirst($type) : 'Manual',
        };
    }

    private function normalizeEntryHours(array $entry): array
    {
        $start = $entry['time_start'] ?? null;
        $end = $entry['time_end'] ?? null;

        if ($start && $end) {
            $entry['hours'] = $this->hoursBetween($start, $end, (float) ($entry['hours'] ?? 1));
        } else {
            $entry['hours'] = max(0, (float) ($entry['hours'] ?? 0));
        }

        return $entry;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:daily_report_times,id',
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'hours' => 'required|numeric|min:0',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'address' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'reportable_type' => 'nullable|string|max:255',
            'reportable_id' => 'nullable|integer',
            'work_place_id' => 'nullable|exists:daily_report_work_places,id',
            'status' => 'nullable|string|max:255',
            'work_status' => 'nullable|string|max:255',

            'customer_ids' => 'array',
            'customer_ids.*' => 'integer|exists:new_leads,id',
            'share_hours' => 'array',
            'share_percent' => 'array',
            'customer_note' => 'array',
            'share_start_time' => 'array',
            'share_start_time.*' => 'nullable|date_format:H:i',
            'share_end_time' => 'array',
            'share_end_time.*' => 'nullable|date_format:H:i',
            'alternative_id' => 'array',
            'alternative_id.*' => 'nullable|integer|exists:lead_alternative_adds,id',
            'lead_product_list_id' => 'array',
            'lead_product_list_id.*' => 'nullable|integer|exists:lead_product_lists,id',
            'product_id' => 'array',
            'product_id.*' => 'nullable|integer|exists:article_groups,id',

            'billing_type' => 'nullable|in:billable,non_billable,internal',
            'activity_category' => 'nullable|string|max:50',
            'is_travel' => 'nullable|boolean',
            'related_report_action' => 'nullable|in:auto,add_mine,agree_existing,force_new',
        ]);

        DB::beginTransaction();

        try {
            $calculatedHours = $this->hoursBetween(
                $validated['start_time'],
                $validated['end_time'],
                (float) ($validated['hours'] ?? 1)
            );

            $customerShareValidation = $this->validateCustomerShareTimes(
                $request->input('customer_ids', []),
                $request->input('share_start_time', []),
                $request->input('share_end_time', []),
                (float) $calculatedHours
            );

            if (!$customerShareValidation['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => $customerShareValidation['message'],
                ], 422);
            }

            $wasExisting = !empty($validated['id']);
            $oldDescription = null;

            $entry = $wasExisting
                ? DailyReportTime::findOrFail($validated['id'])
                : new DailyReportTime();

            if ($wasExisting) {
                $oldDescription = (string) ($entry->description ?? '');
            }

            $entry->fill([
                'employee_id' => $validated['employee_id'],
                'report_date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'hours_spent' => $calculatedHours,
                'type' => $validated['type'] ?? null,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'lat' => $validated['lat'] ?? null,
                'lon' => $validated['lon'] ?? null,
                'ip' => $request->ip(),
                'reportable_type' => $validated['reportable_type'] ?? null,
                'reportable_id' => $validated['reportable_id'] ?? null,
                'work_place_id' => $validated['work_place_id'] ?? null,
                'status' => $validated['status'] ?? null,
                'work_status' => $validated['work_status'] ?? null,
                'customer_id' => null,
                'billing_type' => $validated['billing_type'] ?? null,
                'activity_category' => $validated['activity_category'] ?? null,
                'is_travel' => !empty($validated['is_travel']),
            ]);

            $entry->save();

            $sync = $this->buildCustomerPivotSync(
                (float) $calculatedHours,
                $validated['customer_ids'] ?? [],
                $request->input('share_hours', []),
                $request->input('share_percent', []),
                $request->input('customer_note', []),
                $request->input('share_start_time', []),
                $request->input('share_end_time', []),
                $request->input('alternative_id', []),
                $request->input('lead_product_list_id', []),
                $request->input('product_id', [])
            );

            if (!empty($sync)) {
                $entry->customers()->sync($sync);
            } else {
                $entry->customers()->detach();
            }

            /*
             * Create the real module report once from the daily report save.
             * For existing daily rows we create a new related report only if the report text changed.
             * This avoids duplicate module reports when the user only re-saves the same row.
             */
            $newDescription = (string) ($entry->description ?? '');
            $relatedReportAction = (string) $request->input('related_report_action', 'auto');
            $shouldCreateRelatedReport = trim($newDescription) !== '' && (
                !$wasExisting
                || trim((string) $oldDescription) !== trim($newDescription)
                || in_array($relatedReportAction, ['add_mine', 'agree_existing', 'force_new'], true)
            );

            $relatedReportSaved = false;
            if ($shouldCreateRelatedReport) {
                $entry->loadMissing('customers');
                $relatedReportSaved = $this->storeDailyReportIntoRelatedModule($entry, $request);

                if ($relatedReportSaved) {
                    $this->createDailyRelatedOverdueNotification($entry, (string) $entry->description);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $entry->id,
                'related_report_saved' => $relatedReportSaved,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Daily report row save failed', [
                'message' => $e->getMessage(),
                'request' => $request->except(['_token']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Speichern fehlgeschlagen: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     */

    public function storeStart(Request $request)
    {
        $employeeId = auth()->user()->name; // if your name is used as ID

        // Check if already exists today
        $report = DailyReport::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'start_date' => now()->toDateString()
            ],
            [
                'lat' => $request->lat,
                'lon' => $request->lon,
                'ip' => $request->ip(),
                'status' => 'started'
            ]
        );

        // Save time. The current DailyReportTime model uses employee_id/report_date,
        // not daily_report_id, so keep this compatible with the existing fillable fields.
        $time = DailyReportTime::create([
            'employee_id' => $employeeId,
            'work_place_id' => $request->work_place_id,
            'report_date' => now()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'end_time' => null,
            'hours_spent' => 0,
            'type' => 'Manual',
            'lat' => $request->lat,
            'lon' => $request->lon,
            'ip' => $request->ip(),
            'status' => 'started',
            'work_status' => 'started',
        ]);

        return response()->json([
            'status' => 'success',
            'id' => $time->id,
            'employee_id' => $employeeId
        ]);
    }


    public function storeEnd(Request $request)
    {
        $reportTime = DailyReportTime::find($request->id);

        if (!$reportTime) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $endTime = now()->format('H:i:s');
        $hours = $this->hoursBetween($reportTime->start_time, $endTime, (float) ($reportTime->hours_spent ?: 0));

        $reportTime->update([
            'end_time' => $endTime,
            'hours_spent' => $hours,
            'lat' => $request->lat,
            'lon' => $request->lon,
            'ip' => $request->ip(),
            'work_status' => $request->reason,
            'status' => 'paused'
        ]);

        DailyReport::where('employee_id', $reportTime->employee_id)
            ->whereDate('start_date', $reportTime->report_date ?? now()->toDateString())
            ->update(['status' => 'paused']);

        return response()->json(['status' => 'success', 'hours_spent' => $hours]);
    }


    public function getTime()
    {
        $data = DB::table('daily_reports')
            ->join('daily_report_times as daily', 'daily.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.employee_id', auth()->user()->name)
            ->whereDate('daily_reports.created_at', today())
            ->select(
                'daily_reports.start_date',
                'daily.start_time',
                'daily.end_time',
                'daily.id',
                'daily.status',
            )
            ->latest('daily_reports.id')
            ->first();

        return response()->json($data, 200);
    }



    public function checkTodayAttendance()
    {

        $today = Carbon::today()->toDateString();
        $employeeId = auth()->user()->name;

        $attendance = DailyReport::where('employee_id', $employeeId)
            ->whereDate('start_date', $today)
            ->where('status', 'start')
            ->first();
        Log::info('already recived: ', [$attendance]);

        return response()->json([
            'hasStarted' => !!$attendance,
            'report_id' => $attendance ? $attendance->id : null,
        ]);
    }


    public function startAttendance(Request $request)
    {

        Log::info('attendance start:', [$request->all()]);
        $employeeId = auth()->user()->name;

        DailyReport::create([
            'employee_id' => $employeeId,
            'work_place_id' => $request->work_place_id,
            'start_date' => Carbon::now()->toDateString(),
            'start_time' => Carbon::now()->toTimeString(),
            'status' => 'start',
            'lat' => $request->lat,
            'lon' => $request->lon,
            'ip' => $request->ip,
            'address' => null, // You can use Google Geocoding API if you want address
        ]);

        return response()->json(['success' => true]);
    }


    public function EmployeeList()
    {

        $allEvents = collect();

        // ✅ 1. PERSONAL TASKS
        $tasks = DB::table('employees_personal_tasks')
            ->leftJoin('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
            ->leftJoin('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
            ->select(
                'personal_tasks.id',
                'personal_tasks.task_title as title',
                'personal_tasks.priority',
                'emp.color as backgroundColor',
                'personal_tasks.task_status',
                'personal_tasks.public',
                'personal_tasks.description',
                'personal_tasks.due_date as start_date',
                'personal_tasks.due_date as end_date',
                'personal_tasks.due_time as start_time',
                'personal_tasks.due_time as end_time',
                'employees_personal_tasks.id as emp_personal_id',
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                DB::raw('NULL as customer_id'),
                DB::raw('"task" as type'),
                DB::raw('NULL as phone'),
                DB::raw('NULL as email'),
                DB::raw('NULL as city'),
                DB::raw('NULL as postcode'),
                DB::raw('NULL as street'),
                DB::raw('NULL as contact_id'),
                DB::raw('NULL as contact_type')
            )
            ->whereNull('personal_tasks.deleted_at')
            ->get();
        $allEvents = $allEvents->merge($tasks);

        // ✅ 2. APPOINTMENTS
        $appointments = DB::table('main_appointment_employees')
            ->leftJoin('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
            ->leftJoin('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
            ->select(
                'main_appointments.id',
                'main_appointments.name as title',
                'main_appointments.priority',
                'main_appointments.note as description',
                'emp.color as backgroundColor',
                'main_appointments.status as task_status',
                'main_appointments.public',
                'main_appointments.start_date',
                'main_appointments.end_date',
                DB::raw("COALESCE(main_appointments.start_time, '00:00:00') as start_time"),
                DB::raw("COALESCE(main_appointments.end_time, '23:59:59') as end_time"),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                DB::raw('NULL as emp_personal_id'),
                'main_appointments.customer_id',
                'main_appointments.phone',
                'main_appointments.email',
                'main_appointments.street',
                'main_appointments.postcode',
                'main_appointments.city',
                'main_appointments.contact_id',
                'main_appointments.contact_type',
                DB::raw('"appointment" as type')
            )
            ->whereNull('main_appointments.deleted_at')
            ->get();
        $allEvents = $allEvents->merge($appointments);

        // ✅ 3. TICKETS
        $tickets = DB::table('employee_problem')
            ->leftJoin('problems', 'problems.id', '=', 'employee_problem.problem_id')
            ->leftJoin('employees as emp', 'emp.id', '=', 'employee_problem.employee_id')
            ->select(
                'problems.id',
                'problems.problem as title',
                DB::raw('NULL as priority'),
                DB::raw('emp.color as backgroundColor'),
                'problems.status as task_status',
                DB::raw('NULL as public'),
                'problems.problem as description',
                'problems.date as start_date',
                'problems.date as end_date',
                DB::raw("'00:00:00' as start_time"),
                DB::raw("'23:59:59' as end_time"),
                DB::raw('NULL as emp_personal_id'),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                'problems.customer_id',
                DB::raw('NULL as phone'),
                DB::raw('NULL as email'),
                DB::raw('NULL as street'),
                DB::raw('NULL as city'),
                DB::raw('NULL as postcode'),
                DB::raw('NULL as contact_id'),
                DB::raw('NULL as contact_type'),
                DB::raw('"ticket" as type')
            )
            ->whereNull('problems.deleted_at')
            ->get();
        $allEvents = $allEvents->merge($tickets);

        // ✅ 4. OFFERS
        $offers = DB::table('offers')
            ->leftJoin('employees as emp', 'emp.id', '=', 'offers.created_for')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'offers.product_id')
            ->select(
                'offers.id',
                DB::raw("CONCAT('Angebot: ', article_groups.article_group) as title"),
                DB::raw('NULL as priority'),
                DB::raw('emp.color as backgroundColor'),
                'offers.status as task_status',
                DB::raw('NULL as public'),
                'offers.status_msg as description',
                DB::raw("DATE(offers.created_at) as start_date"),
                DB::raw("DATE(offers.created_at) as end_date"),
                DB::raw("'00:00:00' as start_time"),
                DB::raw("'23:59:59' as end_time"),
                DB::raw('NULL as emp_personal_id'),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                'offers.customer_id',
                DB::raw('NULL as phone'),
                DB::raw('NULL as email'),
                DB::raw('NULL as street'),
                DB::raw('NULL as city'),
                DB::raw('NULL as postcode'),
                DB::raw('NULL as contact_id'),
                DB::raw('NULL as contact_type'),
                DB::raw('"offer" as type')
            )
            ->whereNull('offers.deleted_at')
            ->get();
        $allEvents = $allEvents->merge($offers);

        // ✅ 5. PROJECTS
        $projects = DB::table('projects')
            ->leftJoin('employees as emp', 'emp.id', '=', 'projects.employee_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'projects.product_id')
            ->select(
                'projects.id',
                DB::raw("CONCAT('Projekt: ', article_groups.article_group) as title"),
                DB::raw('NULL as priority'),
                DB::raw('emp.color as backgroundColor'),
                'projects.status as task_status',
                DB::raw('NULL as public'),
                'projects.progress as description',
                'projects.project_start as start_date',
                'projects.end_date as end_date',
                'projects.start_time',
                'projects.end_time',
                DB::raw('NULL as emp_personal_id'),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                'projects.customer_id',
                DB::raw('NULL as phone'),
                DB::raw('NULL as email'),
                DB::raw('NULL as street'),
                DB::raw('NULL as city'),
                DB::raw('NULL as postcode'),
                DB::raw('NULL as contact_id'),
                DB::raw('NULL as contact_type'),
                DB::raw('"project" as type')
            )
            ->whereNull('projects.deleted_at')
            ->get();
        $allEvents = $allEvents->merge($projects);

        // ✅ 6. HOLIDAYS
        $holidays = DB::table('leaves')
            ->leftJoin('employees as emp', 'emp.id', '=', 'leaves.emp_id')
            ->select(
                'leaves.id',
                DB::raw('"Urlaub" as title'),
                DB::raw('NULL as priority'),
                'emp.color as backgroundColor',
                'leaves.status as task_status',
                DB::raw('NULL as public'),
                'leaves.description as description',
                'leaves.start_date',
                'leaves.end_date',
                DB::raw("'00:00:00' as start_time"),
                DB::raw("'23:59:59' as end_time"),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                DB::raw('NULL as emp_personal_id'),
                DB::raw('NULL as customer_id'),
                DB::raw('NULL as phone'),
                DB::raw('NULL as email'),
                DB::raw('NULL as street'),
                DB::raw('NULL as city'),
                DB::raw('NULL as postcode'),
                DB::raw('NULL as contact_id'),
                DB::raw('NULL as contact_type'),
                DB::raw('"holiday" as type')
            )
            ->where('leaves.approved', 'Yes')
            ->get();
        $allEvents = $allEvents->merge($holidays);

        // ✅ 7. SICK DAYS
        $sicks = DB::table('employee_sicks')
            ->leftJoin('employees as emp', 'emp.id', '=', 'employee_sicks.emp_id')
            ->select(
                'employee_sicks.id',
                DB::raw('"Krank" as title'),
                DB::raw('NULL as priority'),
                'emp.color as backgroundColor',
                DB::raw('NULL as task_status'),
                DB::raw('NULL as public'),
                'employee_sicks.status_msg as description',
                'employee_sicks.start_date',
                'employee_sicks.end_date',
                DB::raw("'00:00:00' as start_time"),
                DB::raw("'23:59:59' as end_time"),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                DB::raw('NULL as emp_personal_id'),
                DB::raw('NULL as customer_id'),
                DB::raw('NULL as phone'),
                DB::raw('NULL as email'),
                DB::raw('NULL as street'),
                DB::raw('NULL as city'),
                DB::raw('NULL as postcode'),
                DB::raw('NULL as contact_id'),
                DB::raw('NULL as contact_type'),
                DB::raw('"sick" as type')
            )
            ->get();
        $allEvents = $allEvents->merge($sicks);

        // ✅ GROUP BY EMPLOYEE
        $groupedByEmployee = $allEvents->groupBy('employee_id')->map(function ($events, $employeeId) {
            $employee = $events->first();

            return [
                'employee_id' => $employeeId,
                'name' => $employee->name,
                'lastname' => $employee->lastname,
                'image' => $employee->image,
                'gender' => $employee->gender,
                'daily_start_time' => $employee->daily_start_time,
                'daily_end_time' => $employee->daily_end_time,
                'working_hour' => $employee->working_hour,
                'events' => $events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'emp_personal_id' => $event->emp_personal_id,
                        'customer_id' => $event->customer_id,
                        'title' => $event->title,
                        'start_date' => $event->start_date,
                        'end_date' => $event->end_date,
                        'start_time' => $event->start_time,
                        'end_time' => $event->end_time,
                        'description' => $event->description,
                        'status' => $event->task_status,
                        'taskColor' => $event->backgroundColor,
                        'priority' => $event->priority,
                        'public_view' => $event->public,
                        'type' => $event->type,
                        'street' => $event->street,
                        'postcode' => $event->postcode,
                        'city' => $event->city,
                        'phone' => $event->phone,
                        'email' => $event->email,
                        'contact_id' => $event->contact_id,
                        'contact_type' => $event->contact_type,
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'data' => $groupedByEmployee
        ]);
    }

    public function getPlan($employee_id)
    {
        $today = Carbon::today();
        $allEvents = collect();

        // ✅ PERSONAL TASKS
        $tasks = DB::table('employees_personal_tasks')
            ->leftJoin('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
            ->leftJoin('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
            ->select(
                'personal_tasks.id',
                'personal_tasks.task_title as title',
                'personal_tasks.description',
                'personal_tasks.due_date as start_date',
                'personal_tasks.due_date as end_date',
                'personal_tasks.due_time as start_time',
                'personal_tasks.due_time as end_time',
                'personal_tasks.task_status as status',
                'employees_personal_tasks.id as emp_personal_id',
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                DB::raw("'task' as type"),
                DB::raw('NULL as full_address'),
                DB::raw('NULL as latitude'),
                DB::raw('NULL as longitude')
            )
            ->whereNull('personal_tasks.deleted_at')
            ->whereDate('personal_tasks.due_date', $today)
            ->where('employees_personal_tasks.employee_id', $employee_id)
            ->get();
        $allEvents = $allEvents->merge($tasks);

        // ✅ APPOINTMENTS
        $appointments = DB::table('main_appointment_employees')
            ->leftJoin('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
            ->leftJoin('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
            ->select(
                'main_appointments.id',
                'main_appointments.note as description',
                'main_appointments.start_date',
                'main_appointments.end_date',
                'main_appointments.full_address',
                'main_appointments.latitude',
                'main_appointments.longitude',
                'main_appointments.status',
                DB::raw("COALESCE(main_appointments.start_time, '00:00:00') as start_time"),
                DB::raw("COALESCE(main_appointments.end_time, '23:59:59') as end_time"),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour',
                DB::raw('NULL as emp_personal_id'),
                DB::raw("'appointment' as type")
            )
            ->whereNull('main_appointments.deleted_at')
            ->whereDate('main_appointments.start_date', $today)
            ->where('main_appointment_employees.employee_id', $employee_id)
            ->get();
        $allEvents = $allEvents->merge($appointments);

        // ✅ TICKETS
        $tickets = DB::table('employee_problem')
            ->leftJoin('problems', 'problems.id', '=', 'employee_problem.problem_id')
            ->leftJoin('employees as emp', 'emp.id', '=', 'employee_problem.employee_id')
            ->select(
                'problems.id',
                'problems.problem as title',
                'problems.solution as description',
                'problems.date as start_date',
                'problems.date as end_date',
                DB::raw("'00:00:00' as start_time"),
                DB::raw("'23:59:59' as end_time"),
                DB::raw('NULL as emp_personal_id'),
                DB::raw("'ticket' as type"),
                DB::raw('NULL as full_address'),
                DB::raw('NULL as latitude'),
                DB::raw('NULL as longitude'),
                'problems.status',
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour'
            )
            ->whereNull('problems.deleted_at')
            ->whereDate('problems.date', $today)
            ->where('employee_problem.employee_id', $employee_id)
            ->get();
        $allEvents = $allEvents->merge($tickets);

        // ✅ OFFERS
        $offers = DB::table('offers')
            ->leftJoin('employees as emp', 'emp.id', '=', 'offers.created_for')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'offers.product_id')
            ->select(
                'offers.id',
                DB::raw("CONCAT('Angebot: ', article_groups.article_group) as title"),
                'offers.status_msg as description',
                DB::raw("DATE(offers.created_at) as start_date"),
                DB::raw("DATE(offers.created_at) as end_date"),
                DB::raw("'00:00:00' as start_time"),
                DB::raw("'23:59:59' as end_time"),
                DB::raw('NULL as emp_personal_id'),
                DB::raw("'offer' as type"),
                DB::raw('NULL as full_address'),
                DB::raw('NULL as latitude'),
                DB::raw('NULL as longitude'),
                'offers.status',
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour'
            )
            ->whereNull('offers.deleted_at')
            ->whereDate('offers.created_at', $today)
            ->where('offers.created_for', $employee_id)
            ->get();
        $allEvents = $allEvents->merge($offers);

        // ✅ PROJECTS
        $projects = DB::table('projects')
            ->leftJoin('employees as emp', 'emp.id', '=', 'projects.employee_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'projects.product_id')
            ->select(
                'projects.id',
                DB::raw("CONCAT('Projekt: ', article_groups.article_group) as title"),
                'projects.progress as description',
                'projects.project_start as start_date',
                'projects.end_date as end_date',
                'projects.start_time',
                'projects.end_time',
                'projects.status',
                DB::raw('NULL as emp_personal_id'),
                DB::raw("'project' as type"),
                DB::raw('NULL as full_address'),
                DB::raw('NULL as latitude'),
                DB::raw('NULL as longitude'),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'emp.daily_start_time',
                'emp.daily_end_time',
                'emp.working_hour'
            )
            ->whereNull('projects.deleted_at')
            ->whereDate('projects.project_start', $today)
            ->where('projects.employee_id', $employee_id)
            ->get();
        $allEvents = $allEvents->merge($projects);

        // ✅ If no events, return empty
        if ($allEvents->isEmpty()) {
            return response()->json([
                'employee_id' => $employee_id,
                'worked_minutes' => 0,
                'expected_minutes' => 0,
                'remaining_minutes' => 0,
                'progress' => 0,
                'events' => []
            ]);
        }

        // ✅ Use first event as employee source
        $employee = $allEvents->first();

        // ✅ Expected time
        $dailyStart = $employee->daily_start_time ?? '07:30:00';
        $dailyEnd = $employee->daily_end_time ?? '16:00:00';
        $expectedMinutes = $this->minutesBetween($dailyStart, $dailyEnd);

        // ✅ Worked time
        $workedMinutes = $allEvents->sum(function ($e) {
            $start = Carbon::parse("{$e->start_date} {$e->start_time}");
            $end = Carbon::parse("{$e->end_date} {$e->end_time}");
            if ($start->equalTo($end)) {
                $end = $start->copy()->addHour();
            }
            return $this->minutesBetween($start, $end);
        });

        return response()->json([
            'employee_id' => $employee->employee_id,
            'name' => $employee->name,
            'lastname' => $employee->lastname,
            'image' => $employee->image,
            'gender' => $employee->gender,
            'worked_minutes' => $workedMinutes,
            'expected_minutes' => $expectedMinutes,
            'remaining_minutes' => max($expectedMinutes - $workedMinutes, 0),
            'progress' => round(($workedMinutes / $expectedMinutes) * 100, 1),
            'events' => $allEvents->values(),
        ]);
    }

    public function EmployeeListSearch(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'daily');
        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $selectedDate = $request->input('date') ?? Carbon::today()->format('Y-m-d');

        $carbonDate = Carbon::parse($selectedDate);
        $year = (int) $carbonDate->year;

        // In deinem Setup scheint auth()->user()->name die Employee-ID zu sein
        $authId = auth()->user()->name;
        $isAdmin = $this->isAdmin();

        // -------- Zeitraum (täglich / wöchentlich / monatlich) ----------
        [$start, $end] = match ($filter) {
            'weekly' => [$carbonDate->copy()->startOfWeek(), $carbonDate->copy()->endOfWeek()],
            'monthly' => [$carbonDate->copy()->startOfMonth(), $carbonDate->copy()->endOfMonth()],
            default => [$carbonDate, $carbonDate],
        };

        // -------- aktive Mitarbeiter laden ------------------------------
        $employees = DB::table('employees')
            ->select(
                'id',
                'name',
                'lastname',
                'image',
                'gender',
                'daily_start_time',
                'daily_end_time',
                'color',
                'status',
                'leave as annual_leave_days' // z.B. 30 Tage Jahresurlaub
            )
            ->where('status', 'Active')
            ->when(!$isAdmin, fn($q) => $q->where('id', $authId))
            ->get();

        $result = collect();

        foreach ($employees as $emp) {
            $empId = $emp->id;
            $events = collect();

            // ============================================================
            // 1) Aufgaben (personal_tasks)
            // ============================================================
            $tasks = DB::table('personal_tasks as pt')
                ->join('employees_personal_tasks as ept', 'ept.task_id', '=', 'pt.id')
                ->where('ept.employee_id', $empId)
                ->whereBetween('pt.start_date', [$start, $end])
                ->whereNull('pt.deleted_at')
                ->select(
                    'pt.id',
                    'pt.task_title as title',
                    'pt.start_date',
                    'pt.due_date as end_date',
                    'pt.due_time as end_time',
                    'pt.start_date as start_time',
                    DB::raw("'task' as type"),
                    DB::raw('NULL as full_address'),
                    DB::raw('NULL as latitude'),
                    DB::raw('NULL as longitude')
                )
                ->get();

            $events = $events->merge($tasks);

            // ============================================================
            // 2) Termine (main_appointments)
            // ============================================================
            $appointments = DB::table('main_appointments as ma')
                ->join('main_appointment_employees as mae', 'mae.appointment_id', '=', 'ma.id')
                ->where('mae.employee_id', $empId)
                ->whereBetween('ma.start_date', [$start, $end])
                ->whereNull('ma.deleted_at')
                ->select(
                    'ma.id',
                    'ma.name as title',
                    'ma.start_date',
                    'ma.end_date',
                    'ma.start_time',
                    'ma.end_time',
                    DB::raw("'appointment' as type"),
                    'ma.full_address',
                    'ma.latitude',
                    'ma.longitude'
                )
                ->get();

            $events = $events->merge($appointments);

            // ============================================================
            // 3) Projekte
            // ============================================================
            $projects = DB::table('projects as p')
                ->join('new_leads as c', 'c.id', '=', 'p.customer_id')
                ->where('p.employee_id', $empId)
                ->whereBetween('p.project_start', [$start, $end])
                ->whereNull('p.deleted_at')
                ->select(
                    'p.id',
                    DB::raw("CONCAT(c.name, ' ', c.lastname) as title"),
                    'p.project_start as start_date',
                    'p.end_date',
                    'p.start_time',
                    'p.end_time',
                    DB::raw("'project' as type"),
                    DB::raw('NULL as full_address'),
                    DB::raw('NULL as latitude'),
                    DB::raw('NULL as longitude')
                )
                ->get();

            $events = $events->merge($projects);

            // ============================================================
            // 4) Angebote
            // ============================================================
            $offers = DB::table('offers as o')
                ->join('new_leads as c', 'c.id', '=', 'o.customer_id')
                ->where('o.created_for', $empId)
                ->whereBetween('o.created_at', [$start, $end])
                ->whereNull('o.deleted_at')
                ->select(
                    'o.id',
                    DB::raw("CONCAT(c.name, ' ', c.lastname) as title"),
                    DB::raw('DATE(o.created_at) as start_date'),
                    DB::raw('DATE(o.created_at) as end_date'),
                    DB::raw('NULL as start_time'),
                    DB::raw('NULL as end_time'),
                    DB::raw("'offer' as type"),
                    DB::raw('NULL as full_address'),
                    DB::raw('NULL as latitude'),
                    DB::raw('NULL as longitude')
                )
                ->get();

            $events = $events->merge($offers);

            // ============================================================
            // 5) Tickets / Probleme
            // ============================================================
            $problems = DB::table('problems as p')
                ->join('employee_problem as ep', 'ep.problem_id', '=', 'p.id')
                ->where('ep.employee_id', $empId)
                ->whereBetween('p.date', [$start, $end])
                ->whereNull('p.deleted_at')
                ->select(
                    'p.id',
                    DB::raw("'Ticket' as title"),
                    'p.date as start_date',
                    'p.end_date',
                    DB::raw('NULL as start_time'),
                    DB::raw('NULL as end_time'),
                    DB::raw("'problem' as type"),
                    DB::raw('NULL as full_address'),
                    DB::raw('NULL as latitude'),
                    DB::raw('NULL as longitude')
                )
                ->get();

            $events = $events->merge($problems);

            // ============================================================
            // 6) Urlaub – hier passiert 17 / 30
            // ============================================================
            $annualLeaveTotal = (int) ($emp->annual_leave_days ?? 0); // z.B. 30 Tage im Profil

            // Alle Urlaube des Jahres holen
            $leaveEntries = DB::table('leaves')
                ->where('emp_id', $empId)
                ->whereYear('start_date', $year)
                ->get();

            // Benutzte Tage:
            // 1. bevorzugt "duration" (deine reale Urlaubsdauer)
            // 2. wenn 0, dann "leave_day / 10" (alte 10er-Skalierung)
            // 3. sonst aus Datum berechnen
            $leaveUsedDays = $leaveEntries->sum(function ($row) {
                if (!empty($row->duration)) {
                    return (float) $row->duration;
                }

                if (!empty($row->leave_day)) {
                    return (float) $row->leave_day / 10.0;
                }

                try {
                    $start = Carbon::parse($row->start_date);
                    $end = Carbon::parse($row->end_date);
                    return $start->diffInDays($end) + 1;
                } catch (\Throwable $e) {
                    return 0;
                }
            });

            // Resturlaub = Profil - genutzte Tage (niemals negativ)
            $leaveRemainingDays = max($annualLeaveTotal - $leaveUsedDays, 0);

            // Heute im Urlaub? (nur Zeitraum, kein Statusfilter)
            $onLeaveToday = DB::table('leaves')
                ->where('emp_id', $empId)
                ->whereDate('start_date', '<=', $selectedDate)
                ->whereDate('end_date', '>=', $selectedDate)
                ->exists();

            // ============================================================
            // 7) Kranktage (employee_sicks)
            // ============================================================
            $sickDays = DB::table('employee_sicks')
                ->where('emp_id', $empId)
                ->whereYear('start_date', $year)
                ->sum('total_days');

            $onSickToday = DB::table('employee_sicks')
                ->where('emp_id', $empId)
                ->whereDate('start_date', '<=', $selectedDate)
                ->whereDate('end_date', '>=', $selectedDate)
                ->exists();

            // ============================================================
            // 8) Wiederkehrende Abwesenheiten (employee_recurring_leaves)
            // ============================================================
            $recurringLeaves = DB::table('employee_recurring_leaves')
                ->where('employee_id', $empId)
                ->where('is_active', true)
                ->whereDate('start_date', '<=', $selectedDate)
                ->where(function ($q) use ($selectedDate) {
                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $selectedDate);
                })
                ->get();

            $recurringLeavesCount = $recurringLeaves->count();
            $recurringWeeklyDays = 0;

            foreach ($recurringLeaves as $rl) {
                // Daten können in weekdays ODER day_of_week liegen (JSON, z.B. [1,2])
                $daysJson = $rl->weekdays ?: $rl->day_of_week;
                $days = [];

                if ($daysJson) {
                    $decoded = json_decode($daysJson, true);
                    if (is_array($decoded)) {
                        $days = $decoded;
                    }
                }

                if ($rl->type === 'weekly') {
                    if (!empty($days)) {
                        $recurringWeeklyDays += count($days);   // [1,2] => 2 Tage / Woche
                    } elseif (!empty($rl->duration_days)) {
                        $recurringWeeklyDays += (int) $rl->duration_days;
                    }
                }
            }

            // ============================================================
            // 9) Arbeitszeit / Fortschritt
            // ============================================================
            $startTime = Carbon::parse($emp->daily_start_time ?? '07:30:00');
            $endTime = Carbon::parse($emp->daily_end_time ?? '16:00:00');

            if ($endTime <= $startTime) {
                $endTime = $startTime->copy()->addHours(8);
            }

            $expectedPerDay = $this->minutesBetween($startTime, $endTime);

            $days = match ($filter) {
                'weekly' => 5,
                'monthly' => 20,
                default => 1,
            };

            $totalExpected = $expectedPerDay * $days;

            $worked = $events->sum(function ($e) {
                try {
                    $start = Carbon::parse(($e->start_date ?? '') . ' ' . ($e->start_time ?? '00:00:00'));
                    $end = Carbon::parse(($e->end_date ?? $e->start_date ?? '') . ' ' . ($e->end_time ?? $e->start_time ?? '00:00:00'));

                    if ($end->greaterThan($start)) {
                        return $this->minutesBetween($start, $end);
                    }
                    return 60; // Fallback: 1 Stunde
                } catch (\Throwable $ex) {
                    return 60;
                }
            });

            $progress = round(($worked / max(1, $totalExpected)) * 100, 1);

            // ============================================================
            // 10) Ergebnis-Array für diesen Mitarbeiter
            // ============================================================
            $result->push([
                'employee_id' => $empId,
                'name' => $emp->name,
                'lastname' => $emp->lastname,
                'image' => $emp->image,
                'gender' => $emp->gender,
                'color' => $emp->color,
                'status' => $emp->status,

                // Urlaub / Krankheit / Recurring
                'annual_leave_days' => (float) $annualLeaveTotal,
                'leave_used_days' => (float) $leaveUsedDays,      // → z.B. 17
                'leave_remaining_days' => (float) $leaveRemainingDays, // → z.B. 13
                'sick_days' => (int) $sickDays,
                'on_leave_today' => (bool) $onLeaveToday,
                'on_sick_today' => (bool) $onSickToday,
                'recurring_leaves_count' => (int) $recurringLeavesCount,
                'recurring_weekly_days' => (int) $recurringWeeklyDays,

                // Arbeitszeit
                'worked_minutes' => (int) $worked,
                'expected_minutes' => (int) $totalExpected,
                'progress' => $progress,

                // Aktivitäten
                'events' => $events->values(),
            ]);
        }

        // -------- Suche nach Name / Nachname -----------------------------
        if (!empty($search)) {
            $result = $result->filter(function ($e) use ($search) {
                return stristr($e['name'], $search) || stristr($e['lastname'], $search);
            });
        }

        $total = $result->count();
        $paginated = $result->forPage($page, $perPage)->values();

        return response()->json([
            'data' => $paginated,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }


    private function isAdmin()
    {
        $employeeId = (int) auth()->user()->name;

        return session('force_admin_view') === true || DB::table('user_rolls')
            ->where('user_id', $employeeId)
            ->where('item_id', 'Administrator')
            ->exists();
    }


    public function verifyAdmin(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $user = DB::table('users')->where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            $hasAdminRole = DB::table('user_rolls')
                ->where('user_id', $user->name) // ✅ user.id = user_rolls.user_id
                ->where('item_id', 'Administrator')
                ->exists();

            if ($hasAdminRole) {
                Session::put('force_admin_view', true);
                Session::put('admin_verified_user_id', $user->id);
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false]);
    }

    public function daily_report()
    {
        $today = \Carbon\Carbon::today();
        $employeeId = (int) auth()->user()->name; // cast to int to match DB employee.id

        if ($this->isAdmin()) {
            $employees = DB::table('employees')
                ->select('id', 'name', 'lastname', 'image', 'gender')
                ->where('status', '=', 'Active')
                ->get();
        } else {
            $employees = DB::table('employees')
                ->select('id', 'name', 'lastname', 'image', 'gender')
                ->where('status', '=', 'Active')
                ->where('id', $employeeId)
                ->get();
        }

        $reportedEmployees = DB::table('daily_report_times')
            ->select('employee_id')
            ->whereDate('report_date', $today)
            ->distinct()
            ->pluck('employee_id')
            ->toArray();

        foreach ($employees as $emp) {
            $emp->has_report = in_array($emp->id, $reportedEmployees);
        }

        return view('admin.daily_report.list.list')->with('employees', $employees);
    }


    public function qrView()
    {
        return view('admin.daily_report.qr.qr');
    }

    public function checkQR(Request $request)
    {

        $code = $request->code;
        $type = $request->type;
        $employeeId = auth()->user()->name;
        $latitude = '';
        $longitude = '';

        $code_checker = DB::table('employees')
            ->select('code')
            ->where('code', $code)
            ->first();
        if (!$code_checker) {
            return response()->json(['success', 'Your code is wrong, please try again or call administrator']);
        }

        $work = DB::table('daily_report_work_places')
            ->select('type', 'place_name', 'branch_id', 'address', 'lat', 'lon')
            ->where('lat', '=', $latitude)
            ->where('lon', '=', $longitude)
            ->first();

        if (!$code_checker) {
            return response()->json(['success', 'Your code is wrong, please try again or call administrator']);
        }

        // Check if already exists today
        $report = DailyReport::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'start_date' => now()->toDateString()
            ],
            [
                'lat' => $request->lat,
                'lon' => $request->lon,
                'ip' => $request->ip(),
                'status' => 'started'
            ]
        );


    }


    public function weeklyReport($employee_id): JsonResponse
    {
        $date = request()->query('date') ?? now()->toDateString();
        $parsedDate = Carbon::parse($date);

        $startOfWeek = $parsedDate->copy()->startOfWeek()->startOfDay();
        $endOfWeek = $parsedDate->copy()->endOfWeek()->endOfDay();

        $entries = collect();

        $sources = [
            'tasks' => DB::table('employees_personal_tasks')
                ->join('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
                ->where('employees_personal_tasks.employee_id', $employee_id)
                ->whereBetween('personal_tasks.due_date', [$startOfWeek, $endOfWeek])
                ->select('personal_tasks.task_title as description', 'personal_tasks.due_time as start_time', 'personal_tasks.due_date as date')
                ->get()
                ->map(fn($item) => [
                    'time_start' => Carbon::parse($item->start_time ?? '08:00')->format('H:i'),
                    'time_end' => Carbon::parse($item->start_time ?? '08:00')->addHour()->format('H:i'),
                    'hours' => 1,
                    'address' => 'Task has no address',
                    'type' => 'Task',
                    'description' => $item->description,
                    'date' => $item->date,
                    'is_missing' => false,
                ]),

            'appointments' => DB::table('main_appointment_employees')
                ->join('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
                ->where('main_appointment_employees.employee_id', $employee_id)
                ->whereBetween('main_appointments.start_date', [$startOfWeek, $endOfWeek])
                ->select('main_appointments.full_address as address', 'main_appointments.start_time', 'main_appointments.end_time', 'main_appointments.start_date as date')
                ->get()
                ->map(fn($item) => [
                    'time_start' => Carbon::parse($item->start_time ?? '08:00')->format('H:i'),
                    'time_end' => Carbon::parse($item->end_time ?? '09:00')->format('H:i'),
                    'hours' => $this->hoursBetween($item->start_time, $item->end_time),
                    'address' => $item->address ?? 'Appointment',
                    'type' => 'Appointment',
                    'description' => 'Appointment attended',
                    'date' => $item->date,
                    'is_missing' => false,
                ]),

            'projects' => DB::table('projects')
                ->where('employee_id', $employee_id)
                ->whereBetween('project_start', [$startOfWeek, $endOfWeek])
                ->get()
                ->map(fn($item) => [
                    'time_start' => Carbon::parse($item->start_time ?? '08:00')->format('H:i'),
                    'time_end' => Carbon::parse($item->end_time ?? '09:00')->format('H:i'),
                    'hours' => $this->hoursBetween($item->start_time, $item->end_time),
                    'address' => 'Project',
                    'type' => 'Project',
                    'description' => 'Project work',
                    'date' => $item->project_start,
                    'is_missing' => false,
                ]),

            'offers' => DB::table('offers')
                ->where('created_for', $employee_id)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->get()
                ->map(fn($item) => [
                    'time_start' => '10:00',
                    'time_end' => '11:00',
                    'hours' => 1,
                    'address' => 'Offer',
                    'type' => 'Offer',
                    'description' => 'Offer created',
                    'date' => Carbon::parse($item->created_at)->toDateString(),
                    'is_missing' => false,
                ]),

            'problems' => DB::table('employee_problem')
                ->join('problems', 'problems.id', '=', 'employee_problem.problem_id')
                ->where('employee_problem.employee_id', $employee_id)
                ->whereBetween('problems.date', [$startOfWeek, $endOfWeek])
                ->select('problems.problem as description', 'problems.date')
                ->get()
                ->map(fn($item) => [
                    'time_start' => '11:00',
                    'time_end' => '12:00',
                    'hours' => 1,
                    'address' => 'Ticket',
                    'type' => 'Problem',
                    'description' => $item->description,
                    'date' => $item->date,
                    'is_missing' => false,
                ]),
        ];

        foreach ($sources as $source) {
            $entries = $entries->merge($source);
        }

        $grouped = $entries->groupBy('date');
        $result = [];

        foreach (Carbon::parse($startOfWeek)->daysUntil($endOfWeek) as $day) {
            if ($day->isWeekend())
                continue;

            $date = $day->toDateString();
            $records = $grouped[$date] ?? collect();

            $worked = round($records->sum('hours'), 2);
            // NEW: expected from time_management_* (or fallback)
            $expected = $this->expectedHoursForDay(
                (int) $employee_id,
                $day->copy()
            );

            $fail = max(0, $expected - $worked);


            $result[] = [
                'date' => $day->format('d.m.'),
                'full_date' => $date,
                'worked' => $worked,
                'expected' => $expected,
                'fail' => $fail,
                'has_report' => $worked > 0,
            ];
        }

        return response()->json(['week' => $startOfWeek->isoWeek(), 'days' => $result]);
    }


    public function dailyDetails($employeeId, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        $tasks = PersonalTask::with(['employees'])
            ->whereDate('due_date', $date)
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'Task',
                    'title' => $task->task_title,
                    'description' => $task->task_title,
                    'address' => 'Aufgabe hat keine Adresse',
                    'time_start' => $task->due_time ?? '08:00',
                    'time_end' => Carbon::parse($task->due_time)->addHour()->format('H:i'),
                ];
            });

        $appointments = MainAppointment::with(['employees', 'customer'])
            ->whereDate('start_date', $date)
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->get()
            ->map(function ($appointment) {
                return [
                    'type' => 'Appointment',
                    'title' => 'Termin',
                    'description' => 'Termin wahrgenommen',
                    'address' => $appointment->full_address ?? 'Kein Adresse',
                    'time_start' => $appointment->start_time,
                    'time_end' => $appointment->end_time,
                ];
            });

        $problems = Problem::with('employees')
            ->whereDate('date', $date)
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->get()
            ->map(function ($problem) {
                return [
                    'type' => 'Problem',
                    'title' => 'Ticket',
                    'description' => $problem->problem,
                    'address' => $problem->full_address ?? 'Ticket',
                    'time_start' => '11:00',
                    'time_end' => '12:00',
                ];
            });

        $projects = Project::where('employee_id', $employeeId)
            ->whereDate('project_start', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'Project',
                    'title' => 'Projekt',
                    'description' => 'Projektarbeit',
                    'address' => $project->address ?? 'Projekt',
                    'time_start' => $project->start_time ?? '10:00',
                    'time_end' => $project->end_time ?? '11:00',
                ];
            });

        $offers = Offer::where('created_for', $employeeId)
            ->whereDate('created_at', $date)
            ->get()
            ->map(function ($offer) {
                return [
                    'type' => 'Offer',
                    'title' => 'Angebot',
                    'description' => 'Angebot erstellt',
                    'address' => $offer->full_address ?? 'Angebot',
                    'time_start' => '14:00',
                    'time_end' => '15:00',
                ];
            });

        return view('admin.daily_report.detail', [
            'tasks' => $tasks,
            'appointments' => $appointments,
            'problems' => $problems,
            'projects' => $projects,
            'offers' => $offers,
            'date' => $date,
            'employeeId' => $employeeId
        ]);
    }
    public function storeMissingTime(Request $request)
    {
        Log::info('storeMissingTime request', $request->except(['_token']));

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'hours_spent' => 'required|numeric|min:0',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'address' => 'nullable|string|max:255',
            'id' => 'nullable|exists:daily_report_times,id',
            'reportable_type' => 'nullable|string|max:255',
            'reportable_id' => 'nullable|integer',

            'customer_ids' => 'array',
            'customer_ids.*' => 'integer|exists:new_leads,id',
            'share_hours' => 'array',
            'share_percent' => 'array',
            'customer_note' => 'array',
            'share_start_time' => 'array',
            'share_start_time.*' => 'nullable|date_format:H:i',
            'share_end_time' => 'array',
            'share_end_time.*' => 'nullable|date_format:H:i',
            'alternative_id' => 'array',
            'alternative_id.*' => 'nullable|integer|exists:lead_alternative_adds,id',
            'lead_product_list_id' => 'array',
            'lead_product_list_id.*' => 'nullable|integer|exists:lead_product_lists,id',
            'product_id' => 'array',
            'product_id.*' => 'nullable|integer|exists:article_groups,id',

            'billing_type' => 'nullable|in:billable,non_billable,internal',
            'activity_category' => 'nullable|string|max:50',
            'is_travel' => 'nullable|boolean',
            'related_report_action' => 'nullable|in:auto,add_mine,agree_existing,force_new',
        ]);

        DB::beginTransaction();

        try {
            $calculatedHours = $this->hoursBetween(
                $validated['start_time'],
                $validated['end_time'],
                (float) ($validated['hours_spent'] ?? 1)
            );

            $customerShareValidation = $this->validateCustomerShareTimes(
                $request->input('customer_ids', []),
                $request->input('share_start_time', []),
                $request->input('share_end_time', []),
                (float) $calculatedHours
            );

            if (!$customerShareValidation['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => $customerShareValidation['message'],
                ], 422);
            }

            $wasExisting = !empty($validated['id']);
            $entry = $wasExisting
                ? DailyReportTime::findOrFail($validated['id'])
                : new DailyReportTime();

            $oldDescription = $wasExisting ? (string) ($entry->description ?? '') : null;

            $entry->fill([
                'employee_id' => $validated['employee_id'],
                'report_date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'hours_spent' => $calculatedHours,
                'address' => $validated['address'] ?? null,
                'type' => $validated['type'],
                'reportable_type' => $validated['reportable_type'] ?? $this->mapTypeToModel($validated['type']),
                'reportable_id' => $validated['reportable_id'] ?? 0,
                'description' => $validated['description'] ?? null,
                'customer_id' => null,
                'billing_type' => $validated['billing_type'] ?? null,
                'activity_category' => $validated['activity_category'] ?? null,
                'is_travel' => !empty($validated['is_travel']),
                'ip' => $request->ip(),
            ]);

            $entry->save();

            $sync = $this->buildCustomerPivotSync(
                (float) $entry->hours_spent,
                $request->input('customer_ids', []),
                $request->input('share_hours', []),
                $request->input('share_percent', []),
                $request->input('customer_note', []),
                $request->input('share_start_time', []),
                $request->input('share_end_time', []),
                $request->input('alternative_id', []),
                $request->input('lead_product_list_id', []),
                $request->input('product_id', [])
            );

            if (!empty($sync)) {
                $entry->customers()->sync($sync);
            } else {
                $entry->customers()->detach();
            }

            $newDescription = (string) ($entry->description ?? '');
            $shouldCreateRelatedReport = !$wasExisting || trim((string) $oldDescription) !== trim($newDescription);

            $relatedReportSaved = false;
            if ($shouldCreateRelatedReport) {
                $entry->loadMissing('customers');
                $relatedReportSaved = $this->storeDailyReportIntoRelatedModule($entry, $request);

                if ($relatedReportSaved) {
                    $this->createDailyRelatedOverdueNotification($entry, (string) $entry->description);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Missing time saved successfully.',
                'id' => $entry->id,
                'related_report_saved' => $relatedReportSaved,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Missing daily report time save failed', [
                'message' => $e->getMessage(),
                'request' => $request->except(['_token']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Speichern fehlgeschlagen: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function delete($id)
    {
        $entry = DailyReportTime::findOrFail($id);
        $entry->delete();

        return response()->json(['success' => true, 'message' => 'Eintrag wurde gelöscht.']);
    }

    private function workingWindowForDay(int $employeeId, Carbon $date): array
    {
        // default: employee times
        $employee = Employee::find($employeeId);
        $start = $employee && $employee->daily_start_time
            ? Carbon::parse($employee->daily_start_time)
            : Carbon::parse('08:00');
        $end = $employee && $employee->daily_end_time
            ? Carbon::parse($employee->daily_end_time)
            : Carbon::parse('17:00');

        if ($end->lessThanOrEqualTo($start)) {
            $end = $start->copy()->addHours(8);
        }

        // override with time_management_entries if available
        $plan = DB::table('time_management_plans')
            ->where('employee_id', $employeeId)
            ->where('year', $date->year)
            ->where('month', $date->month)
            ->first();

        if ($plan) {
            $entry = DB::table('time_management_entries')
                ->where('plan_id', $plan->id)
                ->whereDate('work_date', $date->toDateString())
                ->first();

            if ($entry && $entry->start_time && $entry->end_time) {
                $pStart = Carbon::parse($entry->start_time);
                $pEnd = Carbon::parse($entry->end_time);

                if ($pEnd->gt($pStart)) {
                    $start = $pStart;
                    $end = $pEnd;
                }
            }
        }

        return [$start, $end];
    }


    public function reload($employee_id, $date)
    {
        $report = $this->buildReportEntries($employee_id, $date, $date);

        $customers = NewLeads::whereNotIn('status', ['junk', 'Junk', 'deleted'])
            ->whereNull('deleted_at')
            ->get();

        $customerWorkOptions = $this->customerWorkOptionsFor($customers->pluck('id')->all());

        $html = view('admin.daily_report.report.report_rows', [
            'entries' => $report['entries'],
            'customers' => $customers,
            'customerWorkOptions' => $customerWorkOptions,
            'employee_id' => $employee_id,
            'start_date' => $date
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'totalWorked' => round($report['totalWorked'], 2),
            'missingHours' => round($report['missing'], 2),
            'expectedHours' => round($report['expected'], 2),
            'date' => $date,
        ]);
    }


    private function expectedHoursForDay(int $employeeId, Carbon $date): float
    {
        // 1) Try time_management_* (your new planning tables)
        $plan = DB::table('time_management_plans')
            ->where('employee_id', $employeeId)
            ->where('year', $date->year)
            ->where('month', $date->month)
            ->first();

        if ($plan) {
            $entry = DB::table('time_management_entries')
                ->where('plan_id', $plan->id)
                ->whereDate('work_date', $date->toDateString())
                ->first();

            if ($entry) {
                // Prefer stored hours, otherwise compute from times – break
                if (!is_null($entry->hours)) {
                    return (float) $entry->hours;
                }

                $start = $entry->start_time ? Carbon::parse($entry->start_time) : null;
                $end = $entry->end_time ? Carbon::parse($entry->end_time) : null;

                if ($start && $end && $end->gt($start)) {
                    $minutes = $this->minutesBetween($start, $end) - (int) ($entry->break_minutes ?? 0);
                    return max($minutes, 0) / 60;
                }

                // If something is wrong with times, fall back to 0 instead of 8.5
                return 0.0;
            }

            // There is a monthly plan, but no entry for this day -> this day is OFF
            return 0.0;
        }

        // 2) Fallback: employee daily_start_time / daily_end_time
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return 8.0; // last fallback
        }

        $startTime = $employee->daily_start_time
            ? Carbon::parse($employee->daily_start_time)
            : Carbon::parse('08:00');

        $endTime = $employee->daily_end_time
            ? Carbon::parse($employee->daily_end_time)
            : Carbon::parse('16:30'); // 8.5h

        if ($endTime->lessThanOrEqualTo($startTime)) {
            $endTime = $startTime->copy()->addHours(8);
        }

        return $this->hoursBetween($startTime, $endTime);
    }
    protected function buildReportEntries($employee_id, $start_date, $end_date)
    {
        $start = Carbon::parse($start_date)->startOfDay();
        $end = Carbon::parse($end_date)->endOfDay();

        /*
         * IMPORTANT:
         * Many source tables store dates as DATE columns (without time).
         * Using Carbon datetime values in whereBetween() can exclude records from the same day.
         * Keep both formats and use the date-only values for DATE columns.
         */
        $startDay = $start->toDateString();
        $endDay = $end->toDateString();
        $startDateTime = $start->toDateTimeString();
        $endDateTime = $end->toDateTimeString();

        $makeCustomer = function ($customerId) {
            return $customerId ? [
                [
                    'id' => (int) $customerId,
                    'name' => '',
                    'lastname' => '',
                    'share_hours' => null,
                    'share_percent' => null,
                    'note' => null,
                ]
            ] : [];
        };

        // 1) Stored entries from daily_report_times
        $stored = DailyReportTime::with(['customers:id,name,lastname'])
            ->where('employee_id', $employee_id)
            ->whereBetween('report_date', [$startDay, $endDay])
            ->get()
            ->map(function ($item) {
                $typeCode = $this->normalizeTypeCode($item->type ?? 'Manual');
                $isMissing = strcasecmp($typeCode, 'Missing') === 0;

                return [
                    'id' => $item->id,
                    'time_start' => $item->start_time ? Carbon::parse($item->start_time)->format('H:i') : '08:00',
                    'time_end' => $item->end_time ? Carbon::parse($item->end_time)->format('H:i') : (($item->start_time ? Carbon::parse($item->start_time) : Carbon::parse('08:00'))->copy()->addHour()->format('H:i')),
                    'hours' => max(0, (float) $item->hours_spent),
                    'address' => $item->address ?? 'Manual',
                    'type' => $typeCode,
                    'description' => $item->description ?? '',
                    'is_missing' => $isMissing,
                    'from_saved' => true,
                    'customer_id' => $item->customer_id,
                    'reportable_type' => $item->reportable_type,
                    'reportable_id' => $item->reportable_id,

                    'billing_type' => $item->billing_type,
                    'activity_category' => $item->activity_category,
                    'is_travel' => (bool) $item->is_travel,

                    'customers' => $item->customers->map(fn($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'lastname' => $c->lastname,
                        'share_hours' => $c->pivot->share_hours,
                        'share_percent' => $c->pivot->share_percent,
                        'alternative_id' => $c->pivot->alternative_id ?? null,
                        'lead_product_list_id' => $c->pivot->lead_product_list_id ?? null,
                        'product_id' => $c->pivot->product_id ?? null,
                        'share_start_time' => !empty($c->pivot->share_start_time) ? Carbon::parse($c->pivot->share_start_time)->format('H:i') : null,
                        'share_end_time' => !empty($c->pivot->share_end_time) ? Carbon::parse($c->pivot->share_end_time)->format('H:i') : null,
                        'note' => $c->pivot->note ?? null,
                    ])->values(),
                ];
            });

        // Used to hide the original loaded source row after it has already been saved as a daily_report_time.
        $savedSourceKeys = $stored
            ->filter(fn($e) => !empty($e['reportable_type']) && !empty($e['reportable_id']))
            ->mapWithKeys(fn($e) => [$e['reportable_type'] . '|' . (int) $e['reportable_id'] => true])
            ->all();

        // 2) Tasks
        $tasks = DB::table('employees_personal_tasks')
            ->join('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
            ->where('employees_personal_tasks.employee_id', $employee_id)
            ->whereBetween('personal_tasks.due_date', [$startDay, $endDay])
            ->whereNull('personal_tasks.deleted_at')
            ->select(
                'personal_tasks.id',
                'personal_tasks.task_title as title',
                'personal_tasks.description as source_description',
                'personal_tasks.due_time as start_time'
            )
            ->get()
            ->map(function ($item) {
                $s = Carbon::parse($item->start_time ?: '08:00');
                $e = $s->copy()->addHour();

                return [
                    'time_start' => $s->format('H:i'),
                    'time_end' => $e->format('H:i'),
                    'hours' => 1,
                    'address' => 'Aufgabe hat keine Adresse',
                    'type' => 'Task',
                    'title' => $item->title ?: ('Aufgabe #' . $item->id),
                    'source_description' => $item->source_description ?: $item->title,
                    'description' => $item->source_description ?: $item->title,
                    'is_missing' => false,
                    'from_saved' => false,
                    'customer_id' => null,
                    'reportable_type' => PersonalTask::class,
                    'reportable_id' => $item->id,
                    'billing_type' => null,
                    'activity_category' => null,
                    'is_travel' => false,
                    'customers' => [],
                ];
            });

        // 3) Appointments
        $appointments = DB::table('main_appointment_employees')
            ->join('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
            ->where('main_appointment_employees.employee_id', $employee_id)
            ->whereBetween('main_appointments.start_date', [$startDay, $endDay])
            ->whereNull('main_appointments.deleted_at')
            ->select(
                'main_appointments.id',
                'main_appointments.name',
                'main_appointments.note',
                'main_appointments.full_address',
                'main_appointments.start_time',
                'main_appointments.end_time',
                'main_appointments.customer_id'
            )
            ->get()
            ->map(function ($item) use ($makeCustomer) {
                $s = Carbon::parse($item->start_time ?: '08:00');
                $e = Carbon::parse($item->end_time ?: $s->copy()->addHour()->format('H:i'));

                if ($e->lessThanOrEqualTo($s)) {
                    $e = $s->copy()->addHour();
                }

                return [
                    'time_start' => $s->format('H:i'),
                    'time_end' => $e->format('H:i'),
                    'hours' => $this->hoursBetween($s, $e),
                    'address' => $item->full_address ?? 'Termin',
                    'type' => 'Appointment',
                    'title' => $item->name ?: ('Termin #' . $item->id),
                    'source_description' => $item->note ?: '',
                    'description' => $item->note ?: ($item->name ?: 'Termin wahrgenommen'),
                    'is_missing' => false,
                    'from_saved' => false,
                    'customer_id' => $item->customer_id,
                    'reportable_type' => MainAppointment::class,
                    'reportable_id' => $item->id,
                    'billing_type' => null,
                    'activity_category' => null,
                    'is_travel' => false,
                    'customers' => $makeCustomer($item->customer_id),
                ];
            });

        // 4) Projects
        $projects = DB::table('projects')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'projects.alternative_id')
            ->where('projects.employee_id', $employee_id)
            ->whereBetween('projects.project_start', [$startDay, $endDay])
            ->whereNull('projects.deleted_at')
            ->select(
                'projects.id',
                'projects.start_time',
                'projects.end_time',
                'projects.customer_id',
                'projects.alternative_id',
                'projects.product_id',
                'lead_alternative_adds.full_address'
            )
            ->get()
            ->map(function ($item) use ($makeCustomer) {
                $s = Carbon::parse($item->start_time ?: '08:00');
                $e = Carbon::parse($item->end_time ?: $s->copy()->addHour()->format('H:i'));

                if ($e->lessThanOrEqualTo($s)) {
                    $e = $s->copy()->addHour();
                }

                return [
                    'time_start' => $s->format('H:i'),
                    'time_end' => $e->format('H:i'),
                    'hours' => $this->hoursBetween($s, $e),
                    'address' => $item->full_address ?? 'Projekt',
                    'type' => 'Project',
                    'description' => 'Projektarbeit',
                    'is_missing' => false,
                    'from_saved' => false,
                    'customer_id' => $item->customer_id,
                    'alternative_id' => $item->alternative_id ?? 0,
                    'product_id' => $item->product_id ?? 0,
                    'reportable_type' => Project::class,
                    'reportable_id' => $item->id,
                    'billing_type' => null,
                    'activity_category' => null,
                    'is_travel' => false,
                    'customers' => $makeCustomer($item->customer_id),
                ];
            });

        // 5) Offers
        $offers = DB::table('offers')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'offers.alternative_id')
            ->where('offers.created_for', $employee_id)
            ->whereBetween('offers.created_at', [$startDateTime, $endDateTime])
            ->whereNull('offers.deleted_at')
            ->select(
                'offers.id',
                'offers.customer_id',
                'offers.alternative_id',
                'offers.product_id',
                'offers.status_msg',
                'lead_alternative_adds.full_address'
            )
            ->get()
            ->map(function ($item) use ($makeCustomer) {
                return [
                    'time_start' => '10:00',
                    'time_end' => '11:00',
                    'hours' => 1,
                    'address' => $item->full_address ?? 'Angebot',
                    'type' => 'Offer',
                    'description' => $item->status_msg ?: 'Angebot erstellt',
                    'is_missing' => false,
                    'from_saved' => false,
                    'customer_id' => $item->customer_id,
                    'alternative_id' => $item->alternative_id ?? 0,
                    'product_id' => $item->product_id ?? 0,
                    'reportable_type' => Offer::class,
                    'reportable_id' => $item->id,
                    'billing_type' => null,
                    'activity_category' => null,
                    'is_travel' => false,
                    'customers' => $makeCustomer($item->customer_id),
                ];
            });

        // 6) Tickets / Problems
        $problems = DB::table('employee_problem')
            ->join('problems', 'problems.id', '=', 'employee_problem.problem_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'problems.alternative_id')
            ->where('employee_problem.employee_id', $employee_id)
            ->whereBetween('problems.date', [$startDay, $endDay])
            ->whereNull('problems.deleted_at')
            ->select(
                'problems.id',
                'problems.problem as description',
                'problems.customer_id',
                'problems.alternative_id',
                'problems.product_id',
                'lead_alternative_adds.full_address'
            )
            ->get()
            ->map(function ($item) use ($makeCustomer) {
                return [
                    'time_start' => '11:00',
                    'time_end' => '12:00',
                    'hours' => 1,
                    'address' => $item->full_address ?? 'Ticket',
                    'type' => 'Problem',
                    'title' => 'Ticket #' . $item->id,
                    'source_description' => $item->description,
                    'description' => $item->description,
                    'is_missing' => false,
                    'from_saved' => false,
                    'customer_id' => $item->customer_id,
                    'alternative_id' => $item->alternative_id ?? 0,
                    'product_id' => $item->product_id ?? 0,
                    'reportable_type' => Problem::class,
                    'reportable_id' => $item->id,
                    'billing_type' => null,
                    'activity_category' => null,
                    'is_travel' => false,
                    'customers' => $makeCustomer($item->customer_id),
                ];
            });

        $entries = collect()
            ->merge($stored)
            ->merge($tasks)
            ->merge($appointments)
            ->merge($projects)
            ->merge($offers)
            ->merge($problems)
            ->reject(function ($entry) use ($savedSourceKeys) {
                if (!empty($entry['from_saved'])) {
                    return false;
                }

                $type = $entry['reportable_type'] ?? null;
                $id = (int) ($entry['reportable_id'] ?? 0);

                return $type && $id > 0 && isset($savedSourceKeys[$type . '|' . $id]);
            });

        // Remove exact duplicates.
        $seen = [];
        $deduped = collect();

        foreach ($entries as $entry) {
            $key = implode('|', [
                $entry['time_start'] ?? '',
                $entry['time_end'] ?? '',
                $entry['type'] ?? '',
                $entry['description'] ?? '',
                $entry['address'] ?? '',
                $entry['reportable_type'] ?? '',
                $entry['reportable_id'] ?? '',
            ]);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $deduped->push($entry);
        }

        $deduped = $deduped->map(fn($entry) => $this->normalizeEntryHours($entry))->sortBy('time_start')->values();

        $withGaps = collect();
        [$cursor, $endOfDay] = $this->workingWindowForDay(
            (int) $employee_id,
            Carbon::parse($start_date)
        );

        $lastKnown = null;
        $totalWorked = 0.0;

        foreach ($deduped as $entry) {
            $entryStart = Carbon::parse($entry['time_start']);
            $entryEnd = Carbon::parse($entry['time_end']);

            if ($entryEnd->lessThanOrEqualTo($entryStart)) {
                $entryEnd = $entryStart->copy()->addHour();
                $entry['time_end'] = $entryEnd->format('H:i');
                $entry['hours'] = $this->hoursBetween($entryStart, $entryEnd);
            }

            if ($cursor->lt($entryStart)) {
                $withGaps->push([
                    'time_start' => $cursor->format('H:i'),
                    'time_end' => $entryStart->format('H:i'),
                    'hours' => $this->hoursBetween($cursor, $entryStart),
                    'address' => $lastKnown['address'] ?? '',
                    'type' => 'Missing',
                    'description' => '',
                    'is_missing' => true,
                    'from_saved' => false,
                    'customer_id' => null,
                    'reportable_type' => DailyReportTime::class,
                    'reportable_id' => 0,
                    'billing_type' => null,
                    'activity_category' => null,
                    'is_travel' => false,
                    'customers' => [],
                ]);
            }

            $withGaps->push($entry);

            if ($entryEnd->gt($cursor)) {
                $cursor = $entryEnd->copy();
            }

            $lastKnown = $entry;

            if (empty($entry['is_missing'])) {
                $totalWorked += (float) $entry['hours'];
            }
        }

        if ($cursor->lt($endOfDay)) {
            $withGaps->push([
                'time_start' => $cursor->format('H:i'),
                'time_end' => $endOfDay->format('H:i'),
                'hours' => $this->hoursBetween($cursor, $endOfDay),
                'address' => $lastKnown['address'] ?? '',
                'type' => 'Missing',
                'description' => '',
                'is_missing' => true,
                'from_saved' => false,
                'customer_id' => null,
                'reportable_type' => DailyReportTime::class,
                'reportable_id' => 0,
                'billing_type' => null,
                'activity_category' => null,
                'is_travel' => false,
                'customers' => [],
            ]);
        }

        $expected = $this->expectedHoursForDay(
            (int) $employee_id,
            Carbon::parse($start_date)
        );

        $withGaps = $this->hydrateEntryCustomerNames($withGaps);

        $withGaps = $withGaps
            ->map(function ($entry) use ($employee_id) {
                try {
                    return $this->appendRelatedReportStateToEntry((array) $entry, (int) $employee_id);
                } catch (\Throwable $e) {
                    Log::warning('Daily report related state failed', [
                        'employee_id' => $employee_id,
                        'entry_type' => $entry['type'] ?? null,
                        'reportable_type' => $entry['reportable_type'] ?? null,
                        'reportable_id' => $entry['reportable_id'] ?? null,
                        'message' => $e->getMessage(),
                    ]);

                    $entry = (array) $entry;
                    $entry['related_report'] = [
                        'module' => null,
                        'has_any' => false,
                        'has_mine' => false,
                        'my_report_id' => null,
                        'my_report_text' => '',
                        'my_report_created_at' => null,
                        'other_count' => 0,
                        'other_employee_names' => [],
                        'other_reports' => [],
                        'locked' => false,
                    ];

                    return $entry;
                }
            })
            ->values();

        return [
            'entries' => $withGaps,
            'totalWorked' => $totalWorked,
            'expected' => $expected,
            'missing' => max(0, $expected - $totalWorked),
        ];
    }


    private function hydrateEntryCustomerNames(Collection $entries): Collection
    {
        $customerIds = $entries
            ->flatMap(function ($entry) {
                $ids = [];

                if (!empty($entry['customer_id'])) {
                    $ids[] = (int) $entry['customer_id'];
                }

                foreach (($entry['customers'] ?? []) as $customer) {
                    if (!empty($customer['id'])) {
                        $ids[] = (int) $customer['id'];
                    }
                }

                return $ids;
            })
            ->filter()
            ->unique()
            ->values();

        if ($customerIds->isEmpty() || !Schema::hasTable('new_leads')) {
            return $entries;
        }

        $customers = DB::table('new_leads')
            ->whereIn('id', $customerIds)
            ->get([
                'id',
                DB::raw($this->hasColumnSafe('new_leads', 'firma') ? 'firma' : 'NULL as firma'),
                DB::raw($this->hasColumnSafe('new_leads', 'name') ? 'name' : 'NULL as name'),
                DB::raw($this->hasColumnSafe('new_leads', 'lastname') ? 'lastname' : 'NULL as lastname'),
            ])
            ->mapWithKeys(function ($customer) {
                $firma = trim((string) ($customer->firma ?? ''));
                $person = trim((string) (($customer->lastname ?? '') . ' ' . ($customer->name ?? '')));

                return [
                    (int) $customer->id => [
                        'id' => (int) $customer->id,
                        'name' => $firma ?: ($customer->name ?? ''),
                        'lastname' => $firma ? '' : ($customer->lastname ?? ''),
                        'display_name' => $firma ?: ($person ?: ('Kunde #' . (int) $customer->id)),
                    ]
                ];
            });

        return $entries->map(function ($entry) use ($customers) {
            $entry = (array) $entry;

            if (!empty($entry['customer_id']) && empty($entry['customers'])) {
                $customer = $customers->get((int) $entry['customer_id']);

                if ($customer) {
                    $entry['customers'] = [
                        [
                            'id' => $customer['id'],
                            'name' => $customer['name'],
                            'lastname' => $customer['lastname'],
                            'display_name' => $customer['display_name'],
                            'share_hours' => null,
                            'share_percent' => null,
                            'note' => null,
                        ]
                    ];
                }
            }

            if (!empty($entry['customers'])) {
                $entry['customers'] = collect($entry['customers'])->map(function ($row) use ($customers) {
                    $row = (array) $row;
                    $customer = !empty($row['id']) ? $customers->get((int) $row['id']) : null;

                    if ($customer) {
                        $row['name'] = $row['name'] ?: $customer['name'];
                        $row['lastname'] = $row['lastname'] ?: $customer['lastname'];
                        $row['display_name'] = $customer['display_name'];
                    }

                    return $row;
                })->values()->all();
            }

            return $entry;
        });
    }


    private function appendRelatedReportStateToEntry(array $entry, int $employeeId): array
    {
        $module = $this->moduleReportKeyFromValues(
            (string) ($entry['type'] ?? ''),
            (string) ($entry['reportable_type'] ?? '')
        );

        $reportableId = (int) ($entry['reportable_id'] ?? 0);

        $empty = [
            'module' => $module,
            'has_any' => false,
            'has_mine' => false,
            'my_report_id' => null,
            'my_report_text' => '',
            'my_report_created_at' => null,
            'other_count' => 0,
            'other_employee_names' => [],
            'other_reports' => [],
            'all_reports' => [],
            'locked' => false,
        ];

        if (!$module || $reportableId <= 0) {
            $entry['related_report'] = $empty;
            return $entry;
        }

        $rows = $this->relatedModuleReports($module, $reportableId);
        $mine = $rows->first(fn($row) => (int) ($row['employee_id'] ?? 0) === $employeeId);
        $others = $rows->filter(fn($row) => (int) ($row['employee_id'] ?? 0) !== $employeeId)->values();

        $entry['related_report'] = [
            'module' => $module,
            'has_any' => $rows->isNotEmpty(),
            'has_mine' => (bool) $mine,
            'my_report_id' => $mine['id'] ?? null,
            'my_report_text' => $mine['report'] ?? '',
            'my_report_created_at' => $mine['created_at'] ?? null,
            'other_count' => $others->count(),
            'other_employee_names' => $others->pluck('employee_name')->filter()->unique()->values()->all(),
            'other_reports' => $others->values()->all(),
            'all_reports' => $rows->values()->all(),
            'locked' => (bool) $mine,
        ];

        if ($mine && trim((string) ($entry['description'] ?? '')) === '') {
            $entry['description'] = (string) ($mine['report'] ?? '');
        }

        return $entry;
    }

    private function moduleReportKeyFromValues(string $type, string $reportableType): ?string
    {
        $type = strtolower(trim($type));

        if (in_array($type, ['appointment', 'termin'], true) || $reportableType === MainAppointment::class) {
            return 'appointment';
        }

        if (in_array($type, ['task', 'aufgabe'], true) || $reportableType === PersonalTask::class) {
            return 'task';
        }

        if (in_array($type, ['problem', 'ticket'], true) || $reportableType === Problem::class) {
            return 'ticket';
        }

        return null;
    }

    private function relatedModuleReports(string $module, int $targetId): Collection
    {
        try {
            $moduleRows = match ($module) {
                'appointment' => $this->appointmentRelatedReports($targetId),
                'task' => $this->taskRelatedReports($targetId),
                'ticket' => $this->ticketRelatedReports($targetId),
                default => collect(),
            };

            /*
             * Fallback: the OverdueCenter always creates an overdue_reports row after a module report.
             * If the module table has different column names in this installation, this still lets the
             * Tagesbericht show the lock/warning instead of acting like no report exists.
             */
            $overdueRows = $this->overdueRelatedReports($module, $targetId);

            return $moduleRows
                ->merge($overdueRows)
                ->unique(fn($row) => ($row['source'] ?? 'module') . ':' . ($row['id'] ?? 0))
                ->values();
        } catch (\Throwable $e) {
            Log::warning('Related module reports lookup failed', [
                'module' => $module,
                'target_id' => $targetId,
                'message' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    private function overdueRelatedReports(string $module, int $targetId): Collection
    {
        if (!Schema::hasTable('overdue_reports') || $targetId <= 0) {
            return collect();
        }

        $types = match ($module) {
            'appointment' => ['appointment', 'termin'],
            'task' => ['task', 'aufgabe'],
            'ticket' => ['ticket', 'problem'],
            default => [$module],
        };

        $employeeExpr = $this->hasColumnSafe('overdue_reports', 'employee_id')
            ? 'ovr.employee_id'
            : ($this->hasColumnSafe('overdue_reports', 'report_by') ? 'ovr.report_by' : '0');

        $reportExpr = $this->hasColumnSafe('overdue_reports', 'report')
            ? "COALESCE(ovr.report, '')"
            : "''";

        $createdExpr = $this->hasColumnSafe('overdue_reports', 'created_at')
            ? 'ovr.created_at'
            : 'NULL';

        return DB::table('overdue_reports as ovr')
            ->leftJoin('employees as e', 'e.id', '=', DB::raw($employeeExpr))
            ->whereIn('ovr.type', $types)
            ->where('ovr.target_id', $targetId)
            ->when($this->hasColumnSafe('overdue_reports', 'deleted_at'), fn($q) => $q->whereNull('ovr.deleted_at'))
            ->orderByDesc(DB::raw($createdExpr))
            ->get([
                'ovr.id',
                DB::raw($employeeExpr . ' as employee_id'),
                DB::raw($reportExpr . ' as report'),
                DB::raw($createdExpr . ' as created_at'),
                'e.name as employee_name',
                'e.lastname as employee_lastname',
                DB::raw("'overdue' as source"),
            ])
            ->map(fn($row) => $this->normalizeRelatedReportRow($row));
    }

    private function appointmentRelatedReports(int $appointmentId): Collection
    {
        if (!Schema::hasTable('appointment_reports')) {
            return collect();
        }

        $appointmentIdCol = $this->hasColumnSafe('appointment_reports', 'appointment_id')
            ? 'appointment_id'
            : ($this->hasColumnSafe('appointment_reports', 'main_appointment_id') ? 'main_appointment_id' : null);

        if (!$appointmentIdCol) {
            return collect();
        }

        $employeeExpr = $this->employeeExprForRelatedTable('appointment_reports', 'ar', [
            'report_by',
            'employee_id',
            'created_by',
            'user_id',
        ]);

        $reportExpr = $this->textExprForRelatedTable('appointment_reports', 'ar', [
            'report',
            'comment',
            'description',
            'note',
        ]);

        $createdExpr = $this->dateExprForRelatedTable('appointment_reports', 'ar');

        return DB::table('appointment_reports as ar')
            ->leftJoin('employees as e', 'e.id', '=', DB::raw($employeeExpr))
            ->where("ar.$appointmentIdCol", $appointmentId)
            ->when($this->hasColumnSafe('appointment_reports', 'deleted_at'), fn($q) => $q->whereNull('ar.deleted_at'))
            ->orderByDesc(DB::raw($createdExpr))
            ->get([
                'ar.id',
                DB::raw($employeeExpr . ' as employee_id'),
                DB::raw($reportExpr . ' as report'),
                DB::raw($createdExpr . ' as created_at'),
                'e.name as employee_name',
                'e.lastname as employee_lastname',
                DB::raw("'appointment_reports' as source"),
            ])
            ->map(fn($row) => $this->normalizeRelatedReportRow($row));
    }

    private function taskRelatedReports(int $taskId): Collection
    {
        if (!Schema::hasTable('personal_task_comments')) {
            return collect();
        }

        $taskIdCol = $this->hasColumnSafe('personal_task_comments', 'task_id')
            ? 'task_id'
            : ($this->hasColumnSafe('personal_task_comments', 'personal_task_id') ? 'personal_task_id' : null);

        if (!$taskIdCol) {
            return collect();
        }

        $employeeExpr = $this->employeeExprForRelatedTable('personal_task_comments', 'ptc', [
            'comment_by',
            'employee_id',
            'created_by',
            'user_id',
        ]);

        $reportExpr = $this->textExprForRelatedTable('personal_task_comments', 'ptc', [
            'comment',
            'report',
            'description',
            'note',
        ]);

        $createdExpr = $this->dateExprForRelatedTable('personal_task_comments', 'ptc');

        return DB::table('personal_task_comments as ptc')
            ->leftJoin('employees as e', 'e.id', '=', DB::raw($employeeExpr))
            ->where("ptc.$taskIdCol", $taskId)
            ->when($this->hasColumnSafe('personal_task_comments', 'deleted_at'), fn($q) => $q->whereNull('ptc.deleted_at'))
            ->when($this->hasColumnSafe('personal_task_comments', 'status'), function ($q) {
                $q->where(function ($inner) {
                    $inner->whereIn('ptc.status', ['report', 'skipped_report'])
                        ->orWhereNull('ptc.status');
                });
            })
            ->orderByDesc(DB::raw($createdExpr))
            ->get([
                'ptc.id',
                DB::raw($employeeExpr . ' as employee_id'),
                DB::raw($reportExpr . ' as report'),
                DB::raw($createdExpr . ' as created_at'),
                'e.name as employee_name',
                'e.lastname as employee_lastname',
                DB::raw("'personal_task_comments' as source"),
            ])
            ->map(fn($row) => $this->normalizeRelatedReportRow($row));
    }

    private function ticketRelatedReports(int $ticketId): Collection
    {
        if (!Schema::hasTable('ticket_reports')) {
            return collect();
        }

        $ticketIdCol = $this->hasColumnSafe('ticket_reports', 'ticket_id')
            ? 'ticket_id'
            : ($this->hasColumnSafe('ticket_reports', 'problem_id') ? 'problem_id' : null);

        if (!$ticketIdCol) {
            return collect();
        }

        $employeeExpr = $this->employeeExprForRelatedTable('ticket_reports', 'tr', [
            'employee_id',
            'report_by',
            'created_by',
            'user_id',
        ]);

        $reportExpr = $this->textExprForRelatedTable('ticket_reports', 'tr', [
            'report',
            'comment',
            'description',
            'note',
        ]);

        $createdExpr = $this->dateExprForRelatedTable('ticket_reports', 'tr');

        return DB::table('ticket_reports as tr')
            ->leftJoin('employees as e', 'e.id', '=', DB::raw($employeeExpr))
            ->where("tr.$ticketIdCol", $ticketId)
            ->when($this->hasColumnSafe('ticket_reports', 'deleted_at'), fn($q) => $q->whereNull('tr.deleted_at'))
            ->orderByDesc(DB::raw($createdExpr))
            ->get([
                'tr.id',
                DB::raw($employeeExpr . ' as employee_id'),
                DB::raw($reportExpr . ' as report'),
                DB::raw($createdExpr . ' as created_at'),
                'e.name as employee_name',
                'e.lastname as employee_lastname',
                DB::raw("'ticket_reports' as source"),
            ])
            ->map(fn($row) => $this->normalizeRelatedReportRow($row));
    }

    private function employeeExprForRelatedTable(string $table, string $alias, array $columns): string
    {
        $parts = [];

        foreach ($columns as $column) {
            if ($this->hasColumnSafe($table, $column)) {
                $parts[] = "NULLIF($alias.$column, 0)";
            }
        }

        return empty($parts)
            ? '0'
            : 'COALESCE(' . implode(', ', $parts) . ', 0)';
    }

    private function textExprForRelatedTable(string $table, string $alias, array $columns): string
    {
        $parts = [];

        foreach ($columns as $column) {
            if ($this->hasColumnSafe($table, $column)) {
                $parts[] = "NULLIF($alias.$column, '')";
            }
        }

        return empty($parts)
            ? "''"
            : 'COALESCE(' . implode(', ', $parts) . ", '')";
    }

    private function dateExprForRelatedTable(string $table, string $alias): string
    {
        if ($this->hasColumnSafe($table, 'created_at')) {
            return "$alias.created_at";
        }

        if ($this->hasColumnSafe($table, 'report_date')) {
            return "$alias.report_date";
        }

        return 'NULL';
    }

    private function normalizeRelatedReportRow(object $row): array
    {
        $employeeName = trim((string) (($row->employee_name ?? '') . ' ' . ($row->employee_lastname ?? '')));

        $report = (string) ($row->report ?? '');
        $plain = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($report), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $isHtml = $this->isHtmlReportContent($report);
        $source = (string) ($row->source ?? 'module');

        return [
            'id' => (int) ($row->id ?? 0),
            'employee_id' => (int) ($row->employee_id ?? 0),
            'employee_name' => $employeeName ?: ('Mitarbeiter #' . (int) ($row->employee_id ?? 0)),
            'report' => $report,
            'report_plain' => $plain,
            'report_is_html' => $isHtml,
            'created_at' => (string) ($row->created_at ?? ''),
            'created_label' => $this->formatReportDateLabel((string) ($row->created_at ?? '')),
            'source' => $source,
            'source_label' => $this->relatedReportSourceLabel($source),
        ];
    }

    private function isHtmlReportContent(string $value): bool
    {
        $value = trim($value);
        if ($value === '') {
            return false;
        }

        return $value !== strip_tags($value)
            || str_contains($value, '<p')
            || str_contains($value, '<br')
            || str_contains($value, '<ul')
            || str_contains($value, '<ol')
            || str_contains($value, '<div')
            || str_contains($value, 'ql-');
    }

    private function formatReportDateLabel(?string $value): string
    {
        if (!$value) {
            return '';
        }

        try {
            return Carbon::parse($value)->format('d.m.Y H:i');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    private function relatedReportSourceLabel(string $source): string
    {
        return match ($source) {
            'appointment_reports' => 'Terminbericht',
            'personal_task_comments' => 'Aufgabenbericht',
            'ticket_reports' => 'Ticketbericht',
            'overdue' => 'Berichtszentrale',
            default => 'Modulbericht',
        };
    }

    private function hasColumnSafe(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function mapTypeToModel($type)
    {
        $type = strtolower((string) $type);

        return match ($type) {
            'task', 'aufgabe' => PersonalTask::class,
            'appointment', 'termin' => MainAppointment::class,
            'project', 'projekt' => Project::class,
            'offer', 'angebot' => Offer::class,
            'problem', 'ticket' => Problem::class,
            'missing', 'manual', 'manuell' => DailyReportTime::class,
            default => DailyReportTime::class,
        };
    }


    private function createDailyRelatedOverdueNotification(DailyReportTime $entry, string $reportText): void
    {
        if (!Schema::hasTable('overdue_reports')) {
            return;
        }

        $module = $this->moduleReportKeyFromValues((string) $entry->type, (string) $entry->reportable_type);
        $targetId = (int) $entry->reportable_id;
        $employeeId = (int) $entry->employee_id;

        if (!$module || $targetId <= 0 || $employeeId <= 0 || trim($reportText) === '') {
            return;
        }

        try {
            $notification = \App\Models\OverdueReport::create([
                'type' => $module,
                'target_id' => $targetId,
                'report' => trim($reportText),
                'employee_id' => $employeeId,
            ]);

            if (Schema::hasTable('overdue_report_reads')) {
                $employees = Employee::query()
                    ->where('status', 'Active')
                    ->where('id', '!=', $employeeId)
                    ->pluck('id');

                foreach ($employees as $empId) {
                    \App\Models\OverdueReportRead::firstOrCreate([
                        'report_id' => $notification->id,
                        'employee_id' => $empId,
                    ]);
                }
            }

            try {
                broadcast(new \App\Events\OverdueReportCreated($notification))->toOthers();
            } catch (\Throwable $e) {
                // Notification broadcast must not break daily report saving.
            }
        } catch (\Throwable $e) {
            Log::warning('Daily related overdue notification failed', [
                'daily_report_time_id' => $entry->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function storeDailyReportIntoRelatedModule(DailyReportTime $entry, Request $request): bool
    {
        $type = strtolower((string) $entry->type);
        $reportableType = (string) $entry->reportable_type;
        $reportableId = (int) $entry->reportable_id;

        if ($reportableId <= 0 || $reportableType === DailyReportTime::class) {
            return false;
        }

        $employeeId = (int) $entry->employee_id;
        $reportDate = $entry->report_date
            ? Carbon::parse($entry->report_date)->toDateString()
            : now()->toDateString();

        $module = $this->moduleReportKeyFromValues($type, $reportableType);
        $action = (string) $request->input('related_report_action', 'auto');

        $existingReports = collect();
        $myReport = null;
        $otherReports = collect();

        if ($module) {
            $existingReports = $this->relatedModuleReports($module, $reportableId);
            $myReport = $existingReports->first(fn($row) => (int) ($row['employee_id'] ?? 0) === $employeeId);
            $otherReports = $existingReports->filter(fn($row) => (int) ($row['employee_id'] ?? 0) !== $employeeId);
        }

        $reportText = trim((string) $entry->description);

        /*
         * IMPORTANT:
         * Never update/replace an existing Termin/Aufgabe/Ticket report from Tagesbericht.
         * Every explicit action below creates a new module report row:
         * - add_mine       => employee writes a separate new report
         * - agree_existing => employee confirms/agrees with an existing report
         * - force_new      => technical fallback for creating a fresh row
         */
        if ($action === 'agree_existing') {
            $agreedWith = $existingReports
                ->map(fn($row) => trim((string) ($row['employee_name'] ?? $row['creator_name'] ?? '')))
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            $reportText = $agreedWith !== ''
                ? 'Ich stimme dem bereits erstellten Bericht von ' . $agreedWith . ' zu.'
                : 'Ich stimme dem bereits erstellten Bericht zu.';

            // Keep the daily row clear as well, so the saved row shows what this employee confirmed.
            $entry->description = $reportText;
            $entry->save();
        }

        // Do not create meaningless module reports. The planned text is context only;
        // the employee must either type his own text or explicitly agree with an existing report.
        if ($reportText === '') {
            return false;
        }

        if ($module) {
            if (($myReport || $otherReports->isNotEmpty()) && !in_array($action, ['add_mine', 'agree_existing', 'force_new'], true)) {
                // Someone already reported this module item. The UI must explicitly ask:
                // add a new own report OR agree with an existing report. Never overwrite silently.
                return false;
            }
        }

        if ($this->isAppointmentReport($type, $reportableType)) {
            return $this->storeAppointmentReportFromDaily($reportableId, $employeeId, $reportText, $reportDate, $entry);
        }

        if ($this->isTaskReport($type, $reportableType)) {
            return $this->storeTaskReportFromDaily($reportableId, $employeeId, $reportText, $reportDate, $entry);
        }

        if ($this->isTicketReport($type, $reportableType)) {
            return $this->storeTicketReportFromDaily($reportableId, $employeeId, $reportText, $reportDate, $entry);
        }

        if ($this->isInquiryReport($type, $reportableType)) {
            return $this->storeInquiryReportFromDaily($reportableId, $employeeId, $reportText, $reportDate, $entry);
        }

        if ($this->isLeadReport($type, $reportableType)) {
            return $this->storeLeadReportFromDaily($reportableId, $employeeId, $reportText, $reportDate, $entry);
        }

        if ($this->isProjectReport($type, $reportableType)) {
            return $this->storeProjectReportFromDaily($reportableId, $employeeId, $reportText, $reportDate, $entry);
        }

        if ($this->isOfferReport($type, $reportableType)) {
            return $this->storeOfferReportFromDaily($reportableId, $employeeId, $reportText, $reportDate, $entry);
        }

        return false;
    }

    private function updateRelatedModuleReportFromDaily(
        string $module,
        int $reportId,
        string $reportText,
        string $reportDate,
        DailyReportTime $entry
    ): bool {
        if ($reportId <= 0) {
            return false;
        }

        if ($module === 'appointment' && Schema::hasTable('appointment_reports')) {
            $data = ['report' => $reportText, 'updated_at' => now()];
            $this->putIfColumnExists($data, 'appointment_reports', 'report_date', $reportDate);
            $this->putIfColumnExists($data, 'appointment_reports', 'daily_report_time_id', $entry->id);
            return DB::table('appointment_reports')->where('id', $reportId)->update($data) > 0;
        }

        if ($module === 'task' && Schema::hasTable('personal_task_comments')) {
            $data = ['comment' => $reportText, 'updated_at' => now()];
            $this->putIfColumnExists($data, 'personal_task_comments', 'status', 'report');
            $this->putIfColumnExists($data, 'personal_task_comments', 'daily_report_time_id', $entry->id);
            return DB::table('personal_task_comments')->where('id', $reportId)->update($data) > 0;
        }

        if ($module === 'ticket' && Schema::hasTable('ticket_reports')) {
            $data = ['report' => $reportText, 'updated_at' => now()];
            $this->putIfColumnExists($data, 'ticket_reports', 'report_date', $reportDate);
            $this->putIfColumnExists($data, 'ticket_reports', 'daily_report_time_id', $entry->id);
            return DB::table('ticket_reports')->where('id', $reportId)->update($data) > 0;
        }

        return false;
    }

    private function isAppointmentReport(string $type, string $class): bool
    {
        return in_array($type, ['appointment', 'termin'], true)
            || $class === MainAppointment::class;
    }

    private function isTaskReport(string $type, string $class): bool
    {
        return in_array($type, ['task', 'aufgabe'], true)
            || $class === PersonalTask::class;
    }

    private function isTicketReport(string $type, string $class): bool
    {
        return in_array($type, ['problem', 'ticket'], true)
            || $class === Problem::class;
    }

    private function isInquiryReport(string $type, string $class): bool
    {
        return in_array($type, ['inquiry', 'anfrage'], true)
            || $class === \App\Models\Inquiry::class;
    }

    private function isLeadReport(string $type, string $class): bool
    {
        return $type === 'lead'
            || str_contains($class, 'LeadProduct');
    }

    private function isProjectReport(string $type, string $class): bool
    {
        return in_array($type, ['project', 'projekt'], true)
            || $class === Project::class;
    }

    private function isOfferReport(string $type, string $class): bool
    {
        return in_array($type, ['offer', 'angebot'], true)
            || $class === Offer::class;
    }

    private function dailyReportTypeLabel(string $type): string
    {
        return match ($type) {
            'appointment', 'termin' => 'Termin',
            'task', 'aufgabe' => 'Aufgabe',
            'problem', 'ticket' => 'Ticket',
            'project', 'projekt' => 'Projekt',
            'offer', 'angebot' => 'Angebot',
            'inquiry', 'anfrage' => 'Anfrage',
            'lead' => 'Lead',
            default => 'Tagesbericht',
        };
    }

    private function storeAppointmentReportFromDaily(
        int $appointmentId,
        int $employeeId,
        string $reportText,
        string $reportDate,
        DailyReportTime $entry
    ): bool {
        if (!Schema::hasTable('appointment_reports')) {
            return false;
        }

        $data = [
            'appointment_id' => $appointmentId,
            'report' => $reportText,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->putIfColumnExists($data, 'appointment_reports', 'employee_id', $employeeId);
        $this->putIfColumnExists($data, 'appointment_reports', 'report_by', $employeeId);
        $this->putIfColumnExists($data, 'appointment_reports', 'report_date', $reportDate);
        $this->putIfColumnExists($data, 'appointment_reports', 'daily_report_time_id', $entry->id);

        DB::table('appointment_reports')->insert($data);

        return true;
    }

    private function storeTaskReportFromDaily(int $taskId, int $employeeId, string $reportText, DailyReportTime $entry): bool
    {
        if (!Schema::hasTable('personal_task_comments')) {
            return false;
        }

        $data = [
            'task_id' => $taskId,
            'comment_by' => $employeeId,
            'comment' => $reportText,
            'status' => 'report',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->putIfColumnExists($data, 'personal_task_comments', 'daily_report_time_id', $entry->id);

        DB::table('personal_task_comments')->insert($data);

        if (Schema::hasTable('personal_tasks')) {
            $updates = ['updated_at' => now()];

            $this->putIfColumnExists($updates, 'personal_tasks', 'is_report', true);
            $this->putIfColumnExists($updates, 'personal_tasks', 'report_date', now()->toDateString());
            $this->putIfColumnExists($updates, 'personal_tasks', 'report_by', $employeeId);

            DB::table('personal_tasks')->where('id', $taskId)->update($updates);
        }

        return true;
    }

    private function storeTicketReportFromDaily(
        int $ticketId,
        int $employeeId,
        string $reportText,
        string $reportDate,
        DailyReportTime $entry,
        Request $request
    ): bool {
        if (!Schema::hasTable('ticket_reports')) {
            return false;
        }

        $problem = Schema::hasTable('problems')
            ? DB::table('problems')->where('id', $ticketId)->first()
            : null;

        $customerId = (int) ($problem->customer_id ?? $entry->customer_id ?? $request->input('customer_id', 0));
        $alternativeId = (int) ($problem->alternative_id ?? $request->input('alternative_id', 0));
        $productId = (int) ($problem->product_id ?? $request->input('product_id', 0));

        if ($customerId <= 0) {
            $entry->loadMissing('customers');
            $customerId = (int) optional($entry->customers->first())->id;
        }

        $title = trim((string) ($problem->article_name ?? $problem->title ?? $problem->subject ?? $problem->ticket_no ?? ''));
        if ($title === '') {
            $title = Str::limit(strip_tags($reportText), 80, '');
        }
        if ($title === '') {
            $title = 'Ticket Report';
        }

        $data = [
            'ticket_id' => $ticketId,
            'employee_id' => $employeeId,
            'title' => $title,
            'report' => $reportText,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->putIfColumnExists($data, 'ticket_reports', 'customer_id', $customerId);
        $this->putIfColumnExists($data, 'ticket_reports', 'alternative_id', $alternativeId);
        $this->putIfColumnExists($data, 'ticket_reports', 'product_id', $productId);
        $this->putIfColumnExists($data, 'ticket_reports', 'language', app()->getLocale());
        $this->putIfColumnExists($data, 'ticket_reports', 'report_date', $reportDate);
        $this->putIfColumnExists($data, 'ticket_reports', 'likes', 0);
        $this->putIfColumnExists($data, 'ticket_reports', 'daily_report_time_id', $entry->id);
        $this->putIfColumnExists($data, 'ticket_reports', 'deleted_at', null);

        DB::table('ticket_reports')->insert($data);

        return true;
    }

    private function storeInquiryReportFromDaily(
        int $inquiryId,
        int $employeeId,
        string $reportText,
        string $reportDate,
        DailyReportTime $entry
    ): bool {
        if (!Schema::hasTable('inquiry_reports')) {
            return false;
        }

        $data = [
            'inquiry_id' => $inquiryId,
            'report' => $reportText,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->putIfColumnExists($data, 'inquiry_reports', 'report_by', $employeeId);
        $this->putIfColumnExists($data, 'inquiry_reports', 'employee_id', $employeeId);
        $this->putIfColumnExists($data, 'inquiry_reports', 'report_date', $reportDate);
        $this->putIfColumnExists($data, 'inquiry_reports', 'due_date', null);
        $this->putIfColumnExists($data, 'inquiry_reports', 'meta', json_encode([
            'source' => 'daily_report',
            'daily_report_time_id' => $entry->id,
        ]));
        $this->putIfColumnExists($data, 'inquiry_reports', 'daily_report_time_id', $entry->id);

        DB::table('inquiry_reports')->insert($data);

        return true;
    }

    private function storeLeadReportFromDaily(int $leadProductListId, int $employeeId, string $reportText, DailyReportTime $entry): bool
    {
        if (!Schema::hasTable('lead_product_lists') || !Schema::hasTable('customer_reports')) {
            return false;
        }

        $lead = DB::table('lead_product_lists')->where('id', $leadProductListId)->first();

        if (!$lead || empty($lead->customer_id)) {
            return false;
        }

        return $this->insertCustomerReport([
            'customer_id' => (int) $lead->customer_id,
            'alternative_id' => (int) ($lead->alternative_id ?? 0),
            'product_id' => (int) ($lead->product_id ?? 0),
            'report_by' => $employeeId,
            'stage' => $lead->stage ?? $lead->status ?? 'lead',
            'report' => $reportText,
            'report_details' => [
                'source' => 'daily_report',
                'daily_report_time_id' => $entry->id,
                'reportable_type' => $entry->reportable_type,
                'reportable_id' => $entry->reportable_id,
            ],
        ]);
    }

    private function storeCustomerReportFromDailyEntry(DailyReportTime $entry, int $employeeId, string $reportText): bool
    {
        if (!Schema::hasTable('customer_reports')) {
            return false;
        }

        $entry->loadMissing('customers');

        $customerId = (int) (optional($entry->customers->first())->id ?: $entry->customer_id);

        if ($customerId <= 0) {
            return false;
        }

        $alternativeId = 0;
        $productId = 0;
        $stage = $entry->type;

        if ($entry->reportable_type === Project::class && Schema::hasTable('projects')) {
            $project = DB::table('projects')->where('id', $entry->reportable_id)->first();
            $alternativeId = (int) ($project->alternative_id ?? 0);
            $productId = (int) ($project->product_id ?? 0);
            $stage = $project->status ?? 'project';
        }

        if ($entry->reportable_type === Offer::class && Schema::hasTable('offers')) {
            $offer = DB::table('offers')->where('id', $entry->reportable_id)->first();
            $alternativeId = (int) ($offer->alternative_id ?? 0);
            $productId = (int) ($offer->product_id ?? 0);
            $stage = $offer->status ?? 'offer';
        }

        return $this->insertCustomerReport([
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'report_by' => $employeeId,
            'stage' => $stage,
            'report' => $reportText,
            'report_details' => [
                'source' => 'daily_report',
                'daily_report_time_id' => $entry->id,
                'reportable_type' => $entry->reportable_type,
                'reportable_id' => $entry->reportable_id,
                'hours_spent' => $entry->hours_spent,
                'start_time' => $entry->start_time,
                'end_time' => $entry->end_time,
            ],
        ]);
    }

    private function insertCustomerReport(array $payload): bool
    {
        if (!Schema::hasTable('customer_reports')) {
            return false;
        }

        $data = [
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach ($payload as $column => $value) {
            if ($column === 'report_details' && is_array($value)) {
                $value = json_encode($value);
            }

            $this->putIfColumnExists($data, 'customer_reports', $column, $value);
        }

        $this->putIfColumnExists($data, 'customer_reports', 'deleted_at', null);

        if (empty($data['customer_id']) || empty($data['report'])) {
            return false;
        }

        DB::table('customer_reports')->insert($data);

        return true;
    }

    private function putIfColumnExists(array &$data, string $table, string $column, mixed $value): void
    {
        if (Schema::hasColumn($table, $column)) {
            $data[$column] = $value;
        }
    }



    public function completeAndExport(Request $request)
    {
        $employee_id = $request->employee_id;
        $date = Carbon::parse($request->date)->toDateString();

        $entries = DailyReportTime::with(['customers:id,name,lastname'])
            ->where('employee_id', $employee_id)
            ->whereDate('report_date', $date)
            ->orderBy('start_time')
            ->get();

        $directory = public_path('reports');
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }

        $employee = Employee::find($employee_id);
        $pdf = PDF::loadView('admin.daily_report.report.pdf', compact('entries', 'employee', 'date'));

        $filename = "daily_report_{$employee->name}_{$date}.pdf";
        $pdf->save("{$directory}/{$filename}");

        return response()->json([
            'success' => true,
            'pdf_url' => asset("public/reports/{$filename}")
        ]);
    }

    protected function getReportEntries($employee_id, $date)
    {
        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        /*
         * DATE columns must use Y-m-d.
         * DATETIME columns like offers.created_at must use full datetime.
         */
        $startDay = $start->toDateString();
        $endDay = $end->toDateString();

        $startDateTime = $start->toDateTimeString();
        $endDateTime = $end->toDateTimeString();

        $entries = collect();

        // Personal Tasks
        $tasks = DB::table('employees_personal_tasks')
            ->join('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
            ->where('employees_personal_tasks.employee_id', $employee_id)
            ->whereBetween('personal_tasks.due_date', [$startDay, $endDay])
            ->whereNull('personal_tasks.deleted_at')
            ->select(
                'personal_tasks.id',
                'personal_tasks.task_title as title',
                'personal_tasks.description as source_description',
                'personal_tasks.due_time as start_time',
                'personal_tasks.due_date as date'
            )
            ->get()
            ->map(function ($item) {
                $start = Carbon::parse($item->start_time ?: '08:00');
                $end = $start->copy()->addHour();

                $title = trim((string) ($item->title ?? ''));
                $sourceDescription = trim((string) ($item->source_description ?? ''));

                return [
                    'time_start' => $start->format('H:i'),
                    'time_end' => $end->format('H:i'),
                    'hours' => 1,
                    'address' => 'Aufgabe hat keine Adresse',
                    'type' => 'Task',

                    // Planned context only; textarea should stay free for the real report.
                    'title' => $title ?: ('Aufgabe #' . $item->id),
                    'source_description' => $sourceDescription ?: $title,
                    'description' => $sourceDescription ?: $title,

                    'customer_id' => null,
                    'reportable_type' => \App\Models\PersonalTask::class,
                    'reportable_id' => $item->id,
                    'date' => $item->date,
                    'is_missing' => false,
                ];
            });

        // Appointments
        $appointments = DB::table('main_appointment_employees')
            ->join('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
            ->where('main_appointment_employees.employee_id', $employee_id)
            ->whereBetween('main_appointments.start_date', [$startDay, $endDay])
            ->whereNull('main_appointments.deleted_at')
            ->select(
                'main_appointments.id',
                'main_appointments.name as title',
                'main_appointments.note as source_description',
                'main_appointments.full_address',
                'main_appointments.start_time',
                'main_appointments.end_time',
                'main_appointments.customer_id',
                'main_appointments.start_date as date'
            )
            ->get()
            ->map(function ($item) {
                $start = Carbon::parse($item->start_time ?: '08:00');
                $end = Carbon::parse($item->end_time ?: $start->copy()->addHour()->format('H:i'));

                $title = trim((string) ($item->title ?? ''));
                $sourceDescription = trim((string) ($item->source_description ?? ''));

                return [
                    'time_start' => $start->format('H:i'),
                    'time_end' => $end->format('H:i'),
                    'hours' => $this->hoursBetween($start, $end),
                    'address' => $item->full_address ?? 'Termin',
                    'type' => 'Appointment',

                    // Planned context only.
                    'title' => $title ?: ('Termin #' . $item->id),
                    'source_description' => $sourceDescription,
                    'description' => $sourceDescription,

                    'customer_id' => $item->customer_id,
                    'reportable_type' => \App\Models\MainAppointment::class,
                    'reportable_id' => $item->id,
                    'date' => $item->date,
                    'is_missing' => false,
                ];
            });

        // Projects
        $projects = DB::table('projects')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'projects.alternative_id')
            ->where('projects.employee_id', $employee_id)
            ->whereBetween('projects.project_start', [$startDay, $endDay])
            ->whereNull('projects.deleted_at')
            ->select(
                'projects.id',
                'projects.start_time',
                'projects.end_time',
                'projects.customer_id',
                'projects.project_start as date',
                'lead_alternative_adds.full_address'
            )
            ->get()
            ->map(function ($item) {
                $start = Carbon::parse($item->start_time ?: '08:00');
                $end = Carbon::parse($item->end_time ?: $start->copy()->addHour()->format('H:i'));

                return [
                    'time_start' => $start->format('H:i'),
                    'time_end' => $end->format('H:i'),
                    'hours' => $this->hoursBetween($start, $end),
                    'address' => $item->full_address ?? 'Projekt',
                    'type' => 'Project',
                    'title' => 'Projekt #' . $item->id,
                    'source_description' => 'Projektarbeit',
                    'description' => 'Projektarbeit',
                    'customer_id' => $item->customer_id,
                    'reportable_type' => \App\Models\Project::class,
                    'reportable_id' => $item->id,
                    'date' => $item->date,
                    'is_missing' => false,
                ];
            });

        // Offers - created_at is DATETIME, so use full datetime range.
        $offers = DB::table('offers')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'offers.alternative_id')
            ->where('offers.created_for', $employee_id)
            ->whereBetween('offers.created_at', [$startDateTime, $endDateTime])
            ->whereNull('offers.deleted_at')
            ->select(
                'offers.id',
                'offers.customer_id',
                DB::raw('DATE(offers.created_at) as date'),
                'lead_alternative_adds.full_address'
            )
            ->get()
            ->map(function ($item) {
                return [
                    'time_start' => '10:00',
                    'time_end' => '11:00',
                    'hours' => 1,
                    'address' => $item->full_address ?? 'Angebot',
                    'type' => 'Offer',
                    'title' => 'Angebot #' . $item->id,
                    'source_description' => 'Angebot erstellt',
                    'description' => 'Angebot erstellt',
                    'customer_id' => $item->customer_id,
                    'reportable_type' => \App\Models\Offer::class,
                    'reportable_id' => $item->id,
                    'date' => $item->date,
                    'is_missing' => false,
                ];
            });

        // Problems / Tickets
        $problems = DB::table('employee_problem')
            ->join('problems', 'problems.id', '=', 'employee_problem.problem_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'problems.alternative_id')
            ->where('employee_problem.employee_id', $employee_id)
            ->whereBetween('problems.date', [$startDay, $endDay])
            ->whereNull('problems.deleted_at')
            ->select(
                'problems.id',
                'problems.problem as source_description',
                'problems.customer_id',
                'problems.date',
                'lead_alternative_adds.full_address'
            )
            ->get()
            ->map(function ($item) {
                return [
                    'time_start' => '11:00',
                    'time_end' => '12:00',
                    'hours' => 1,
                    'address' => $item->full_address ?? 'Ticket',
                    'type' => 'Problem',

                    // Planned context only.
                    'title' => 'Ticket #' . $item->id,
                    'source_description' => $item->source_description,
                    'description' => $item->source_description,

                    'customer_id' => $item->customer_id,
                    'reportable_type' => \App\Models\Problem::class,
                    'reportable_id' => $item->id,
                    'date' => $item->date,
                    'is_missing' => false,
                ];
            });

        return $entries
            ->merge($tasks)
            ->merge($appointments)
            ->merge($projects)
            ->merge($offers)
            ->merge($problems)
            ->sortBy('time_start')
            ->values();
    }

    public function getReportHistory(Request $request)
    {
        $employee = Employee::findOrFail($request->employee_id);
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $filename = 'daily_report_' . $employee->name . '_' . $date . '.pdf';
        $path = public_path('reports/' . $filename);

        $history = [];

        if (file_exists($path)) {
            $history[] = [
                'created_at' => Carbon::createFromTimestamp(filemtime($path))->format('d.m.Y H:i'),
                'url' => asset('reports/' . $filename)
            ];
        }

        return response()->json(['history' => $history]);
    }

}
