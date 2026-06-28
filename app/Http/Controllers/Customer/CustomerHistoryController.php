<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\CustomerHistory;
use App\Models\Employee;
use Illuminate\Http\Request;
use DB;
use App\Services\TimeSummaryService; 
use App\Models\Image; 
use Illuminate\Support\Facades\Storage;
class CustomerHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }


public function saveFromAjax(Request $request, TimeSummaryService $summary)
{
    \Log::info('CustomerHistory AJAX request', ['payload' => $request->all()]);

    // --- Local helpers --------------------------------------------------------
    $normalize = static function ($time) {
        // Your own normalizer on the controller
        return static::normalizeTime($time);
    };

    // "HH:MM[:SS]" -> total minutes (null if invalid)
    $toMinutes = static function (?string $time): ?int {
        if (!$time) {
            return null;
        }

        $time = trim($time);

        if (!preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $m)) {
            return null;
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    };

    // +N / -N minutes -> signed "HH:MM:00" string (e.g. "-00:15:00")
    $formatSigned = static function (int $minutes): string {
        $negative = $minutes < 0;
        $minutes  = abs($minutes);

        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;

        return ($negative ? '-' : '') . sprintf('%02d:%02d:00', $hours, $mins);
    };

    // --- Normalize BEFORE validation ------------------------------------------
    $request->merge([
        'plan_time' => $normalize($request->plan_time),
        'is_time'   => $normalize($request->is_time),
    ]);

    // --- Validate -------------------------------------------------------------
    try {
        $validated = $request->validate([
            'activity_id'         => 'required|integer',
            'phase_id'            => 'required|integer',
            'customer_id'         => 'required|integer',
            'alternative_id'      => 'required|integer',
            'product_id'          => 'required|integer',
            'section_id'          => 'nullable|integer',

            'is_done'             => 'nullable',

            'done_reason'         => 'nullable|array',
            'done_reason.percent' => 'nullable',
            'done_reason.reason'  => 'nullable|string|max:255',

            // allow "HH:MM" or "HH:MM:SS"
            'plan_time'           => ['nullable', 'regex:/^\d{1,2}:\d{2}(?::\d{2})?$/'],
            'is_time'             => ['nullable', 'regex:/^\d{1,2}:\d{2}(?::\d{2})?$/'],

            'done_date'           => 'nullable|date',
            'notes'               => 'nullable|string',
            'done_by'             => 'nullable|integer',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::warning('CustomerHistory validation failed', [
            'errors'  => $e->errors(),
            'payload' => $request->all(),
        ]);
        throw $e; // keep standard 422 JSON response
    }

    // In your setup employee.id is stored in users.name
    $userId   = auth()->user()->name;
    $employee = Employee::find($userId);

    try {
        [$history, $sums, $diffMinutes] = DB::transaction(function () use (
            $validated,
            $userId,
            $employee,
            $summary,
            $toMinutes,
            $formatSigned
        ) {
            // Upsert one row per (customer, alt, product, phase, activity)
            $history = CustomerHistory::firstOrNew([
                'customer_id'    => $validated['customer_id'],
                'alternative_id' => $validated['alternative_id'],
                'product_id'     => $validated['product_id'],
                'phase_id'       => $validated['phase_id'],
                'activity_id'    => $validated['activity_id'],
            ]);

            // Decode existing done_history safely
            $existing = $history->done_history;
            if (is_string($existing)) {
                $decoded  = json_decode($existing, true);
                $existing = is_array($decoded) ? $decoded : [];
            }
            $log = is_array($existing) ? $existing : [];

            // Compute d_time (difference plan vs. ist)
            $planMinutes = $toMinutes($validated['plan_time'] ?? null);
            $isMinutes   = $toMinutes($validated['is_time'] ?? null);

            $diffMinutes = null;
            $dTime       = null;

            if ($planMinutes !== null && $isMinutes !== null) {
                $diffMinutes = $isMinutes - $planMinutes;   // >0 = über Plan
                $dTime       = $formatSigned($diffMinutes); // "-00:15:00"
            }

            // Append history log entry
            $log[] = [
                'changed_at'     => now()->format('Y-m-d H:i:s'),
                'marked_by_name' => $employee?->name ?? 'Unbekannt',
                'is_done'        => $validated['is_done'] ?? null,
                'done_reason'    => $validated['done_reason'] ?? null,
                'plan_time'      => $validated['plan_time'] ?? null,
                'is_time'        => $validated['is_time'] ?? null,
                'd_time'         => $dTime,
            ];
            $history->done_history = $log;

            // Fill simple columns from validation
            $history->fill($validated);

            // Persist computed time difference
            $history->d_time = $dTime;

            // Actor columns
            $history->marked_by ??= $userId;
            $history->done_by   ??= $userId;

            // Auto done_date if state is "completed"
            if (($validated['is_done'] ?? null) === '1' && empty($validated['done_date'])) {
                $history->done_date = now();
            }

            $history->save();

            // Recompute summaries (phase + total)
            $sums = $summary->recomputeBoth(
                $validated['customer_id'],
                $validated['alternative_id'],
                $validated['product_id'],
                (int) ($validated['section_id'] ?? 0),
                $validated['phase_id']
            );

            return [$history, $sums, $diffMinutes];
        });

        return response()->json([
            'success'   => true,
            'initials'  => $employee
                ? strtoupper(substr($employee->name, 0, 1) . substr($employee->lastname, 0, 1))
                : '??',
            'done_date' => optional($history->done_date)->format('Y-m-d'),
            'd_time'    => $history->d_time,     // e.g. "-00:15:00"
            'd_minutes' => $diffMinutes,         // e.g. -15
            'summaries' => [
                'phase'    => $sums['phase'],    // minutes + percent + latest_done_date
                'total'    => $sums['total'],
                'phase_id' => $history->phase_id,
            ],
        ]);

    } catch (\Throwable $e) {
        \Log::error('CustomerHistory Save Error', [
            'message' => $e->getMessage(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Fehler: ' . $e->getMessage(),
        ], 500);
    }
}

 

private static function normalizeTime($value)
{
    if ($value === null) return null;
    if (is_string($value)) {
        $value = trim($value);
        if ($value === '') return null;
    }

    $raw = is_string($value) ? $value : (string)$value;

    $cap = static function (int $mins): int {
        if ($mins < 0) return 0;
        $max = 23 * 60 + 59; // keep within H:i validation window
        return $mins > $max ? $max : $mins;
    };

    $emit = static function (int $mins): string {
        $h = intdiv($mins, 60);
        $m = $mins % 60;
        return sprintf('%02d:%02d', $h, $m);
    };

    // Normalize odd separators to colon; keep only letters/digits/colon/dot/space
    $s = preg_replace('/[^\pL\pN:.\s]/u', '', $raw);
    $s = str_replace(['٫','．','。','.',' '], [':',':',':',':',''], $s);

    // 1) HH:MM[:SS] with overflow carry
    if (preg_match('/^(\d{1,3}):(\d{1,2})(?::(\d{1,2}))?$/', $s, $m)) {
        $h   = (int)$m[1];
        $i   = (int)$m[2];
        $sec = isset($m[3]) ? (int)$m[3] : 0;
        $i  += intdiv($sec, 60);
        $mins = $h * 60 + $i;
        return $emit($cap($mins));
    }

    // 2) 1h30 / 1h / 2hours 5m
    if (preg_match('/^\s*(\d+)\s*h(?:ours?)?\s*(\d{1,2})?\s*(?:m(?:in(?:utes)?)?)?\s*$/i', $s, $m)) {
        $h = (int)$m[1];
        $i = isset($m[2]) ? (int)$m[2] : 0;
        return $emit($cap($h * 60 + $i));
    }

    // 3) 90m / 45min / 120 minutes
    if (preg_match('/^\s*(\d+)\s*m(?:in(?:utes)?)?\s*$/i', $s, $m)) {
        return $emit($cap((int)$m[1]));
    }

    // 4) Plain integer => treat as minutes
    if (preg_match('/^\d+$/', $s)) {
        return $emit($cap((int)$s));
    }

    // 5) Last resort: Carbon parse (e.g., "9am", "21:30")
    try {
        return \Carbon\Carbon::parse($raw)->format('H:i');
    } catch (\Throwable $e) {
        return $raw; // let validation complain if it's truly invalid
    }
}



    public function uploadActivityDocument(Request $request)
    {
        $data = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['required', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'phase_id' => ['required', 'integer'],
            'task_id' => ['required', 'integer'],
            'stage' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('document');

        $originalName = preg_replace(
            '/[^a-zA-Z0-9\-_\.]/',
            '_',
            $file->getClientOriginalName()
        );

        $filename = time() . '_' . uniqid() . '_' . $originalName;

        /*
        |--------------------------------------------------------------------------
        | Save in the same place as the normal upload()
        |--------------------------------------------------------------------------
        | storage/app/uploads/customers/{filename}
        |--------------------------------------------------------------------------
        */
        $path = $file->storeAs('uploads/customers', $filename);

        if (!$path || !Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Datei konnte nicht gespeichert werden.',
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | Find or create CustomerHistory record
        |--------------------------------------------------------------------------
        */
        $history = CustomerHistory::firstOrNew([
            'customer_id' => $data['customer_id'],
            'alternative_id' => $data['alternative_id'],
            'product_id' => $data['product_id'] ?? null,
            'phase_id' => $data['phase_id'],
            'activity_id' => $data['task_id'],
        ]);

        $history->has_document = $filename;
        $history->save();

        /*
        |--------------------------------------------------------------------------
        | Save also to images table exactly like normal upload()
        |--------------------------------------------------------------------------
        */
        $image = Image::create([
            'customer_id' => $data['customer_id'],
            'alternative_id' => $data['alternative_id'],
            'article_group' => $data['product_id'] ?? null,

            'phase_id' => $data['phase_id'],
            'task_id' => $data['task_id'],

            'image' => $filename,
            'image_name' => pathinfo($originalName, PATHINFO_FILENAME),
            'file_type' => $file->getClientOriginalExtension(),

            'stage' => $data['stage'] ?? null,
            'status' => $data['stage'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | In your app auth()->user()->name is employee_id
            |--------------------------------------------------------------------------
            */
            'created_by' => auth()->user()->name ?? 'system',
        ]);

        return response()->json([
            'success' => true,
            'image_id' => $image->id,
            'file_name' => $filename,
            'path' => $path,
            'message' => 'Aktivitätsdokument wurde erfolgreich hochgeladen.',
        ]);
    }
public function getDoneHistory(Request $request)
{
    $request->validate([
        'activity_id' => 'required|numeric',
        'phase_id'    => 'required|numeric',
    ]);

    $userId = auth()->user()->name;

    $history = CustomerHistory::where('activity_id', $request->activity_id)
        ->where('phase_id', $request->phase_id)
        ->where(function ($q) use ($userId) {
            $q->where('marked_by', $userId)->orWhereNotNull('done_history');
        })->first();

    if (!$history || empty($history->done_history)) {
        return response()->json([
            'success' => true,
            'history' => []
        ]);
    }

    return response()->json([
        'success' => true,
        'history' => $history->done_history
    ]);
}

public function getTimeSummary(Request $request)
{
    // 1) Validate + sanitize
    $data = $request->validate([
        'customer_id'    => 'required|integer',
        'alternative_id' => 'required|integer',
        'product_id'     => 'required|integer',
        'phase_id'       => 'nullable',
    ]);

    $customerId    = (int)$data['customer_id'];
    $alternativeId = (int)$data['alternative_id'];
    $productId     = (int)$data['product_id'];
    $phaseIdRaw    = $data['phase_id'] ?? null;

    // accept "12" or "12-card"
    $phaseId = null;
    if (!is_null($phaseIdRaw)) {
        if (is_numeric($phaseIdRaw)) {
            $phaseId = (int)$phaseIdRaw;
        } elseif (preg_match('/^(\d+)/', (string)$phaseIdRaw, $m)) {
            $phaseId = (int)$m[1];
        }
    }

    // 2) Try cached materialized row (optional, if you created time_summaries)
    if (class_exists(\App\Models\TimeSummary::class)) {
        $row = \App\Models\TimeSummary::where([
            'customer_id'    => $customerId,
            'alternative_id' => $alternativeId,
            'product_id'     => $productId,
            'scope'          => $phaseId ? 'phase' : 'total',
            'phase_id'       => $phaseId,
        ])->first();

        if ($row) {
            return response()->json([
                'plan'             => (int)$row->plan_minutes,
                'is'               => (int)$row->actual_minutes,
                'diff'             => (int)$row->diff_minutes,
                'weighted_percent' => (int)$row->weighted_percent,
                'end'              => $row->latest_done_date
                    ? \Carbon\Carbon::parse($row->latest_done_date)->isoFormat('DD.MM.YYYY')
                    : null,
            ]);
        }
    }

    // 3) Recompute live from CustomerHistory (latest per activity)
    $base = \App\Models\CustomerHistory::query()
        ->where('customer_id', $customerId)
        ->where('alternative_id', $alternativeId)
        ->where('product_id', $productId);

    if ($phaseId) {
        $base->where('phase_id', $phaseId);
    }

    // One row per activity: latest id
    $latestIds = (clone $base)
        ->selectRaw('MAX(id) as id')
        ->groupBy('activity_id')
        ->pluck('id');

    $items = \App\Models\CustomerHistory::whereIn('id', $latestIds)
        ->get(['plan_time','is_time','done_reason','done_date','is_done']);

    // Parser for "HH:MM" / "HH:MM:SS" anywhere in the string
    $toMinutes = static function ($v): int {
        if (!$v) return 0;
        $s = is_string($v) ? $v : (string)$v;
        if (preg_match('/(\d{1,2}):(\d{2})(?::(\d{2}))?/', $s, $m)) {
            $h = (int)($m[1] ?? 0);
            $i = (int)($m[2] ?? 0);
            $sec = (int)($m[3] ?? 0);
            return $h * 60 + $i + intdiv($sec, 60);
        }
        return 0;
    };

    $plan = 0;          // total planned minutes
    $actual = 0;        // total actual minutes
    $cap = 0;           // sum(min(actual_i, plan_i)) for weighted %
    $latestDone = null;

    foreach ($items as $r) {
        $p = $toMinutes($r->plan_time);
        $a = $toMinutes($r->is_time);

        // If half-done and no actual entered, derive from percent of plan
        if ($a === 0 && $r->done_reason) {
            $jr = is_array($r->done_reason) ? $r->done_reason
                 : (is_string($r->done_reason) ? json_decode($r->done_reason, true) : []);
            $percent = (int)($jr['percent'] ?? 0);
            if ($percent > 0 && $p > 0) {
                $a = (int) round($p * ($percent / 100));
            }
        }

        $plan   += $p;
        $actual += $a;
        $cap    += min($a, $p);

        if (!empty($r->done_date)) {
            $d = \Carbon\Carbon::parse($r->done_date);
            if (!$latestDone || $d->gt($latestDone)) $latestDone = $d;
        }
    }

    $diff = $actual - $plan;
    $weightedPercent = $plan > 0 ? (int) round(($cap / $plan) * 100) : 0;

    return response()->json([
        'plan'             => $plan,
        'is'               => $actual,                 // ✅ was $actual undefined in your code
        'diff'             => $diff,
        'weighted_percent' => $weightedPercent,        // ✅ now computed
        'end'              => $latestDone?->isoFormat('DD.MM.YYYY'),
    ]);
}

    


}
