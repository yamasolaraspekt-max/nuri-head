<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSick;
use App\Notifications\EmployeeProfileNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class EmployeeSickController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function currentEmployeeId(): ?int
    {
        $value = auth()->user()->name ?? null;

        return $value ? (int) $value : null;
    }

    private function normalizeDocuments($raw): array
    {
        if (!$raw) {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter($raw));
        }

        if (is_object($raw)) {
            return array_values(array_filter(json_decode(json_encode($raw), true) ?: []));
        }

        if (is_string($raw)) {
            $value = trim($raw);

            if ($value === '') {
                return [];
            }

            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values(array_filter($decoded));
            }

            // Support old one-file records
            return [$value];
        }

        return [];
    }

    private function saveDocuments(EmployeeSick $sick, array $documents): void
    {
        $documents = array_values(array_filter($documents));

        $sick->document = $documents
            ? json_encode($documents, JSON_UNESCAPED_UNICODE)
            : null;
    }

    private function uploadDocuments(Request $request, int $employeeId): array
    {
        $paths = [];

        $files = [];

        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
        } elseif ($request->hasFile('document')) {
            $files = [$request->file('document')];
        }

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $destinationPath = public_path("images/employees/sick_documents/{$employeeId}");

            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
            $extension = $file->getClientOriginalExtension();

            $fileName = 'sick_' . now()->format('Ymd_His') . '_' . uniqid() . '_' . $safeName . '.' . $extension;

            $file->move($destinationPath, $fileName);

            $paths[] = "images/employees/sick_documents/{$employeeId}/{$fileName}";
        }

        return $paths;
    }

    private function calculateTotals(
        string $startDate,
        ?string $endDate,
        ?string $startTime = null,
        ?string $endTime = null
    ): array {
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : $start->copy();

        if ($startTime && $endTime) {
            $startDateTime = Carbon::parse($startDate . ' ' . $startTime);
            $endDateTime = Carbon::parse($startDate . ' ' . $endTime);

            if ($endDateTime->lt($startDateTime)) {
                $endDateTime->addDay();
            }

            return [
                'total_days' => 1,
                'total_hours' => round($startDateTime->floatDiffInHours($endDateTime), 2),
                'year' => $start->year,
            ];
        }

        $totalDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                $totalDays++;
            }

            $current->addDay();
        }

        return [
            'total_days' => $totalDays,
            'total_hours' => $totalDays * 24,
            'year' => $start->year,
        ];
    }

    private function sickPayload(EmployeeSick $sick): array
    {
        $documents = $this->normalizeDocuments($sick->document);

        return [
            'id' => (int) $sick->id,
            'emp_id' => (int) $sick->emp_id,
            'employee_name' => trim(($sick->employee?->name ?? '') . ' ' . ($sick->employee?->lastname ?? '')),
            'start_date' => $sick->start_date ? Carbon::parse($sick->start_date)->format('Y-m-d') : null,
            'end_date' => $sick->end_date ? Carbon::parse($sick->end_date)->format('Y-m-d') : null,
            'start_time' => $sick->start_time ? Carbon::parse($sick->start_time)->format('H:i') : null,
            'end_time' => $sick->end_time ? Carbon::parse($sick->end_time)->format('H:i') : null,
            'total_days' => (float) ($sick->total_days ?? 0),
            'total_hours' => (float) ($sick->total_hours ?? 0),
            'year' => (int) ($sick->year ?? now()->year),
            'status' => (string) ($sick->status ?? ''),
            'status_msg' => (string) ($sick->status_msg ?? ''),
            'documents' => $documents,
            'document_urls' => collect($documents)->map(fn($path) => asset($path))->values()->toArray(),
            'created_at' => $sick->created_at ? $sick->created_at->format('d.m.Y H:i') : '',
        ];
    }

    public function index()
    {
        $employees = Employee::orderBy('name')->orderBy('lastname')->get();

        return view('employee_sick.index', compact('employees'));
    }

    public function byEmployee($employeeId)
    {
        $employeeId = (int) $employeeId;

        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Mitarbeiter wurde nicht gefunden.',
            ], 404);
        }

        $sicks = EmployeeSick::with('employee')
            ->where('emp_id', $employeeId)
            ->latest()
            ->get()
            ->map(fn($sick) => $this->sickPayload($sick))
            ->values();

        return response()->json([
            'success' => true,
            'employee_id' => $employeeId,
            'sicks' => $sicks,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        $validator = Validator::make($request->all(), [
            'emp_id' => ['required', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string', 'max:255'],
            'status_msg' => ['nullable', 'string', 'max:5000'],

            // Document is optional
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierung fehlgeschlagen.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $totals = $this->calculateTotals(
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['start_time'] ?? null,
            $data['end_time'] ?? null
        );

        $uploadedDocuments = $this->uploadDocuments($request, (int) $data['emp_id']);

        $payload = [
            'emp_id' => (int) $data['emp_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? $data['start_date'],
            'total_days' => $totals['total_days'],
            'total_hours' => $totals['total_hours'],
            'year' => $totals['year'],
            'status' => $data['status'],
            'status_msg' => $data['status_msg'] ?? null,
            'created_by' => $this->currentEmployeeId() ?? auth()->id(),
        ];

        if (Schema::hasColumn('employee_sicks', 'start_time')) {
            $payload['start_time'] = $data['start_time'] ?? null;
        }

        if (Schema::hasColumn('employee_sicks', 'end_time')) {
            $payload['end_time'] = $data['end_time'] ?? null;
        }

        $sick = new EmployeeSick($payload);
        $this->saveDocuments($sick, $uploadedDocuments);
        $sick->save();
        $sick->load('employee');

        $employee = Employee::find($sick->emp_id);

        if ($employee) {
            $name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

            Notification::send(auth()->user(), new EmployeeProfileNotification([
                'title' => 'Krankmeldung beantragt',
                'message' => "{$name} hat eine Krankmeldung vom {$sick->start_date} bis {$sick->end_date} beantragt.",
                'emp_id' => $sick->emp_id,
                'type' => 'sick',
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Krankmeldung erfolgreich erstellt.',
            'sick' => $this->sickPayload($sick),
        ]);
    }

    public function edit($id)
    {
        $sick = EmployeeSick::with('employee')->findOrFail($id);

        return response()->json([
            'success' => true,
            'sick' => $this->sickPayload($sick),
        ]);
    }

    public function update(Request $request, $id)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        $sick = EmployeeSick::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'emp_id' => ['required', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string', 'max:255'],
            'status_msg' => ['nullable', 'string', 'max:5000'],

            // Document is optional
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierung fehlgeschlagen.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $totals = $this->calculateTotals(
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['start_time'] ?? null,
            $data['end_time'] ?? null
        );

        $oldDocuments = $this->normalizeDocuments($sick->document);
        $newDocuments = $this->uploadDocuments($request, (int) $data['emp_id']);

        $payload = [
            'emp_id' => (int) $data['emp_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? $data['start_date'],
            'total_days' => $totals['total_days'],
            'total_hours' => $totals['total_hours'],
            'year' => $totals['year'],
            'status' => $data['status'],
            'status_msg' => $data['status_msg'] ?? null,
            'created_by' => $this->currentEmployeeId() ?? auth()->id(),
        ];

        if (Schema::hasColumn('employee_sicks', 'start_time')) {
            $payload['start_time'] = $data['start_time'] ?? null;
        }

        if (Schema::hasColumn('employee_sicks', 'end_time')) {
            $payload['end_time'] = $data['end_time'] ?? null;
        }

        $sick->fill($payload);
        $this->saveDocuments($sick, array_merge($oldDocuments, $newDocuments));
        $sick->save();
        $sick->load('employee');

        return response()->json([
            'success' => true,
            'message' => 'Krankmeldung erfolgreich aktualisiert.',
            'sick' => $this->sickPayload($sick),
        ]);
    }

    public function destroy($id)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        $sick = EmployeeSick::findOrFail($id);

        foreach ($this->normalizeDocuments($sick->document) as $path) {
            $fullPath = public_path($path);

            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $sick->delete();

        return response()->json([
            'success' => true,
            'message' => 'Krankmeldung erfolgreich gelöscht.',
            'id' => (int) $id,
        ]);
    }

    public function destroyDocument($id, $index)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        $sick = EmployeeSick::findOrFail($id);
        $documents = $this->normalizeDocuments($sick->document);

        if (!isset($documents[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'Dokument wurde nicht gefunden.',
            ], 404);
        }

        $path = $documents[$index];
        $fullPath = public_path($path);

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }

        unset($documents[$index]);
        $documents = array_values($documents);

        $this->saveDocuments($sick, $documents);
        $sick->save();
        $sick->load('employee');

        return response()->json([
            'success' => true,
            'message' => 'Dokument wurde gelöscht.',
            'sick' => $this->sickPayload($sick),
        ]);
    }
}