<?php
 
namespace App\Http\Controllers\Inquiry;
use App\Http\Controllers\Controller;

use App\Models\Inquiry;
use App\Models\InquiryReport;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // GET /inquiry/{inquiry}/reports
    public function index(Inquiry $inquiry)
    {
        $reports = InquiryReport::query()
            ->with(['reporter:id,name,lastname,image,gender'])
            ->where('inquiry_id', $inquiry->id)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    // POST /inquiry/{inquiry}/reports
    public function store(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'report'      => ['nullable', 'string'],
            'meta'        => ['nullable'], // allow array or json-string
            'report_date' => ['nullable', 'date'],
            'due_date'    => ['nullable', 'date'],
        ]);

        $meta = $this->normalizeMeta($validated['meta'] ?? null);

        $report = InquiryReport::create([
            'inquiry_id'  => $inquiry->id,
            'report_by'   => $this->resolveEmployeeId(),
            'report'      => $validated['report'] ?? null,
            'meta'        => $meta,
            'report_date' => $validated['report_date'] ?? now(),
            'due_date'    => $validated['due_date'] ?? null,
        ]);

        $report->load(['reporter:id,name,lastname,image,gender']);

        return response()->json([
            'success' => true,
            'message' => 'Report erstellt.',
            'data'    => $report,
        ]);
    }

    // PUT /inquiry/reports/{report}
    public function update(Request $request, InquiryReport $report)
    {
        $validated = $request->validate([
            'report'      => ['nullable', 'string'],
            'meta'        => ['nullable'],
            'report_date' => ['nullable', 'date'],
            'due_date'    => ['nullable', 'date'],
        ]);

        // Safety: do not allow switching inquiry_id
        $report->report      = $validated['report'] ?? $report->report;
        $report->meta        = $this->normalizeMeta($validated['meta'] ?? $report->meta);
        $report->report_date = $validated['report_date'] ?? $report->report_date;
        $report->due_date    = $validated['due_date'] ?? $report->due_date;

        // keep existing report_by unless you want to overwrite:
        // $report->report_by = $this->resolveEmployeeId();

        $report->save();
        $report->load(['reporter:id,name,lastname,image,gender']);

        return response()->json([
            'success' => true,
            'message' => 'Report aktualisiert.',
            'data'    => $report,
        ]);
    }

    // DELETE /inquiry/reports/{report}
    public function destroy(InquiryReport $report)
    {
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report gelöscht.',
        ]);
    }

    private function normalizeMeta($meta)
    {
        if ($meta === null || $meta === '') return null;

        if (is_array($meta)) return $meta;

        if (is_string($meta)) {
            $decoded = json_decode($meta, true);
            return is_array($decoded) ? $decoded : ['raw' => $meta];
        }

        return null;
    }

    /**
     * Resolve employees.id for current user.
     * Your codebase sometimes stores employee id in auth()->user()->name (numeric).
     */
    private function resolveEmployeeId(): ?int
    {
        $u = Auth::user();
        if (!$u) return null;

        // Case 1: user->name is numeric employee id (your existing pattern)
        if (isset($u->name) && is_numeric($u->name)) {
            return (int) $u->name;
        }

        // Case 2: match by email
        if (!empty($u->email)) {
            $empId = Employee::where('email', $u->email)->value('id');
            if ($empId) return (int) $empId;
        }

        return null;
    }
}
