<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSick;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class DashboardAbsenceRequestController extends Controller
{
    public function data()
    {
        $employeeId = $this->employeeId();

        $employee = Employee::query()
            ->select('id', 'name', 'lastname', 'image')
            ->findOrFail($employeeId);

        $approvers = Employee::query()
            ->select('id', 'name', 'lastname', 'image')
            ->where('id', '!=', $employeeId)
            ->orderBy('name')
            ->get()
            ->map(fn($emp) => [
                'id' => $emp->id,
                'name' => trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? '')),
                'image_url' => $this->employeeImageUrl($emp),
            ]);

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                'image_url' => $this->employeeImageUrl($employee),
            ],
            'approvers' => $approvers,
        ]);
    }
    public function store(Request $request)
    {
        $employeeId = (int) auth()->user()->name;

        $validator = Validator::make($request->all(), [
            'request_type' => ['required', 'in:leave,sick'],
            'request_to' => ['required', 'integer', 'exists:employees,id'],
            'leave_type' => ['nullable', 'string', 'max:100'],
            'reason' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:5000'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte Eingaben prüfen.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->startOfDay();
        $days = $start->diffInDays($end) + 1;
        $year = (int) $start->format('Y');

        if ($request->request_type === 'leave') {
            $leave = Leave::create([
                'emp_id' => $employeeId,
                'created_by' => $employeeId,
                'request_to' => $request->request_to,
                'year' => $year,
                'leave_type' => $request->leave_type ?: 'Urlaub',
                'reason' => $request->reason,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'leave_day' => $days,
                'duration' => $days,
                'remaining_day' => 0,
                'paid' => null,
                'approved' => 'pending',
                'status' => 'pending',
                'description' => $request->description,
                'notes' => [
                    [
                        'text' => 'Antrag über Dashboard erstellt.',
                        'employee_id' => $employeeId,
                        'created_at' => now()->toDateTimeString(),
                    ],
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Urlaubsantrag wurde gesendet.',
                'type' => 'leave',
                'item' => $leave,
            ]);
        }

        $documentPath = null;

        if ($request->hasFile('document')) {
            $relativeFolder = 'images/employees/sick_documents';
            $folder = public_path($relativeFolder);

            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }

            $file = $request->file('document');

            $filename = 'sick_document_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move($folder, $filename);

            $documentPath = $relativeFolder . '/' . $filename;
        }
        $sick = EmployeeSick::create([
            'emp_id' => $employeeId,
            'created_by' => $employeeId,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_days' => $days,
            'total_hours' => $days * 8,
            'year' => $year,
            'status' => 'pending',
            'document' => $documentPath,
            'status_msg' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Krankmeldung wurde gesendet.',
            'type' => 'sick',
            'item' => $sick,
            'document_url' => $filename ? asset('images/employees/sick_document/' . $filename) : null,
        ]);
    }

    private function employeeId(): int
    {
        return (int) auth()->user()->name;
    }

    private function employeeImageUrl(?Employee $employee): string
    {
        if (!$employee || empty($employee->image)) {
            return asset('images/gender/male.png');
        }

        return asset('images/employee/' . $employee->image);
    }
}