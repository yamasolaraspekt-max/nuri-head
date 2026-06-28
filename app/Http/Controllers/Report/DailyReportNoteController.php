<?php
namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use App\Models\DailyReportNote;
use App\Models\DailyReport;
use Illuminate\Http\Request;

class DailyReportNoteController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    // GET /daily-notes?date=YYYY-MM-DD&entry_id=123
    public function index(Request $req)
    {
        $date = $req->query('date');
        if (!$date) return response()->json(['error' => 'date required'], 422);

        $q = DailyReportNote::with(['author:id,name,lastname,image'])
            ->whereDate('report_date', $date);

        // Important: use has(), not truthiness
        if ($req->query->has('entry_id')) {
            $eid = $req->query('entry_id');

            if ($eid === '__null' || $eid === '' || $eid === null) {
                $q->whereNull('daily_report_time_id');
            } else {
                $q->where('daily_report_time_id', $eid);
            }
        }

        $notes = $q->orderBy('created_at')->get()->map(function ($n) {
            return [
                'id'      => $n->id,
                'message' => $n->message,
                'created' => optional($n->created_at)->format('d.m.Y H:i'),
                'author'  => trim(($n->author->name ?? '').' '.($n->author->lastname ?? '')),
                'avatar'  => $n->author->image ?? null,
            ];
        });

        return response()->json(['data' => $notes]);
    }

    // POST /daily-notes  { date, entry_id?, message }
    public function store(Request $req)
    {
        // Normalize special token to NULL before validating
        $raw = $req->input('entry_id', null);
        $entryId = ($raw === '__null' || $raw === '') ? null : $raw;
        $req->merge(['entry_id' => $entryId]);

        $validated = $req->validate([
            'date'     => 'required|date',
            'entry_id' => 'nullable|integer|exists:daily_report_times,id',
            'message'  => 'required|string|max:2000',
        ]);

        $employeeId = (int) auth()->user()->name; // your app’s mapping

        $report = DailyReport::firstOrCreate(
            ['employee_id' => $employeeId, 'start_date' => $validated['date']],
            ['status' => 'started']
        );

        $note = DailyReportNote::create([
            'report_id'            => $report->id,
            'employee_id'          => $employeeId,
            'daily_report_time_id' => $validated['entry_id'], // null if no row
            'report_date'          => $validated['date'],
            'message'              => $validated['message'],
        ]);

        return response()->json(['success' => true, 'id' => $note->id]);
    }

    public function destroy($id)
    {
        $note = DailyReportNote::findOrFail($id);
        // optional: check ownership/role here
        $note->delete();
        return response()->json(['success' => true]);
    }
}
