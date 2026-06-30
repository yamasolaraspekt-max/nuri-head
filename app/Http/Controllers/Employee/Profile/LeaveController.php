<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    public function index()
    {
        //
    }

    /**
     * Decide if the request should receive JSON.
     * Supports the new notification AJAX page and old normal Blade forms.
     */
    private function wantsJsonResponse(Request $request): bool
    {
        return $request->expectsJson()
            || $request->ajax()
            || str_contains((string) $request->header('Accept'), 'application/json')
            || str_contains((string) $request->header('Content-Type'), 'application/json');
    }

    private function successResponse(Request $request, array $payload = [], ?string $redirectMessage = null)
    {
        if ($this->wantsJsonResponse($request)) {
            return response()->json(array_merge([
                'success' => true,
                'active_tab' => 'leave',
            ], $payload));
        }

        return redirect()->back()->with([
            'save_msg' => $redirectMessage ?: ($payload['message'] ?? 'Aktion erfolgreich ausgeführt.'),
            'active_tab' => 'leave',
        ]);
    }

    private function errorResponse(Request $request, string $message, int $status = 422, array $errors = [])
    {
        if ($this->wantsJsonResponse($request)) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
                'active_tab' => 'leave',
            ], $status);
        }

        return redirect()->back()
            ->withInput()
            ->with([
                'delete_msg' => $message,
                'active_tab' => 'leave',
            ]);
    }

    private function currentEmployeeId(): ?int
    {
        $value = auth()->user()->name ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function currentActorId(): int
    {
        return $this->currentEmployeeId() ?: (int) auth()->id();
    }

    private function nullableNumber($value, $default = null)
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return is_numeric($value) ? $value + 0 : $default;
    }

    private function nullableInt($value, $default = null): ?int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return is_numeric($value) ? (int) $value : $default;
    }

    private function calculateWorkingDays(string $startDate, string $endDate): int
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        $duration = 0;
        $current = clone $start;

        while ($current <= $end) {
            $dayOfWeek = (int) $current->format('N');

            if ($dayOfWeek < 6) {
                $duration++;
            }

            $current->modify('+1 day');
        }

        return $duration;
    }

    /**
     * leaves.notes can be JSON string, array, Collection, object, null, or already decoded.
     */
    private function normalizeNotes($raw): array
    {
        if ($raw instanceof \Illuminate\Support\Collection) {
            return $raw->toArray();
        }

        if (is_array($raw)) {
            return array_values($raw);
        }

        if (is_object($raw)) {
            return json_decode(json_encode($raw), true) ?: [];
        }

        if (is_string($raw)) {
            $raw = trim($raw);

            if ($raw === '') {
                return [];
            }

            $decoded = json_decode($raw, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values($decoded);
            }

            return [];
        }

        return [];
    }

    private function saveNotes(Leave $leave, array $notes): void
    {
        $notes = array_values($notes);

        if (
            method_exists($leave, 'hasCast')
            && $leave->hasCast('notes', ['array', 'json', 'object', 'collection'])
        ) {
            $leave->notes = $notes;
        } else {
            $leave->notes = json_encode($notes, JSON_UNESCAPED_UNICODE);
        }

        $leave->save();
    }

    private function hasColumn(string $column): bool
    {
        try {
            return Schema::hasColumn('leaves', $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function setOptionalLeaveColumn(array &$payload, string $column, $value): void
    {
        if ($this->hasColumn($column)) {
            $payload[$column] = $value;
        }
    }

    private function findLeave($id): ?Leave
    {
        return Leave::find($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Urlaubssaldo-Buchung (Paket 2 / Punkt 3 / Bug 2, Modell A)
    |--------------------------------------------------------------------------
    | Invariante: Tage sind genau dann vom Mitarbeiter-Saldo abgebucht, wenn der
    | Antrag genehmigt ist. Abgebucht wird ausschliesslich bei der Genehmigung
    | (approve / change-accept), zurueckgebucht bei Storno/Ablehnung/Edit eines
    | vorher genehmigten Antrags. Alles idempotent ueber den Genehmigt-Status.
    */

    /** Gilt der Antrag als genehmigt (= Tage wurden vom Saldo abgebucht)? */
    private function leaveIsApproved($leave): bool
    {
        return strtolower(trim((string) ($leave->approved ?? ''))) === 'yes'
            || strtolower(trim((string) ($leave->status ?? ''))) === 'accept';
    }

    /** Urlaubsdauer vom Mitarbeiter-Saldo abbuchen (nur bei erstmaliger Genehmigung). */
    private function debitRemainingDay($leave): void
    {
        $duration = (int) $leave->duration;
        if ($leave->emp_id && $duration > 0) {
            Employee::where('id', $leave->emp_id)->decrement('remaining_day', $duration);
        }
    }

    /** Urlaubsdauer auf den Mitarbeiter-Saldo zurueckbuchen (nur fuer vorher abgebuchte Antraege). */
    private function creditRemainingDay($leave): void
    {
        $duration = (int) $leave->duration;
        if ($leave->emp_id && $duration > 0) {
            Employee::where('id', $leave->emp_id)->increment('remaining_day', $duration);
        }
    }

    private function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate, ?int $ignoreLeaveId = null): bool
    {
        $query = Leave::where('emp_id', $employeeId)
            ->where(function ($q) {
                $q->whereNull('approved')
                    ->orWhere('approved', '')
                    ->orWhere('approved', 'Pending')
                    ->orWhere('approved', 'Yes')
                    ->orWhere('approved', '!=', 'No');
            })
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', ['canceled', 'cancelled', 'deleted', 'rejected']);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                        $subQuery->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($ignoreLeaveId) {
            $query->where('id', '!=', $ignoreLeaveId);
        }

        return $query->exists();
    }

    /**
     * GET/POST approve support.
     */
    public function approve(Request $request, $id)
    {
        try {
            $leave = $this->findLeave($id);

            if (!$leave) {
                return $this->errorResponse($request, 'Urlaubsantrag wurde nicht gefunden.', 404);
            }

            // FIX P2-31: War der Antrag vor diesem Aufruf schon genehmigt? Dann die
            // Resttage NICHT erneut abziehen (Schutz vor Doppel-Dekrement bei erneutem approve()).
            $alreadyApproved = strtolower(trim((string) $leave->approved)) === 'yes'
                || strtolower(trim((string) $leave->status)) === 'accept';

            DB::transaction(function () use ($leave, $alreadyApproved) {
                $leave->approved = 'Yes';
                $leave->status = 'accept';
                $leave->request_answer = 'accept';
                $leave->changed_by = $this->currentActorId();
                $leave->save();

                // FIX P2-31: Beim erstmaligen Genehmigen die verbleibenden Urlaubstage
                // des Mitarbeiters um die Urlaubsdauer reduzieren (bisher fehlte das,
                // nur der Dashboard-Pfad save() tat es).
                $duration = (int) $leave->duration;
                if (!$alreadyApproved && $leave->emp_id && $duration > 0) {
                    Employee::where('id', $leave->emp_id)->decrement('remaining_day', $duration);
                }
            });

            return $this->successResponse($request, [
                'message' => 'Urlaub erfolgreich genehmigt!',
                'leave_id' => $leave->id,
            ], 'Urlaub erfolgreich genehmigt!');
        } catch (\Throwable $e) {
            Log::error('LeaveController@approve failed', [
                'id' => $id,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return $this->errorResponse($request, 'Serverfehler: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store leave request.
     * Works with old normal forms and new AJAX modal.
     */
    public function store(Request $request)
    {
        try {
            Log::info('LeaveController@store incoming request', [
                'payload' => $request->all(),
                'user_id' => auth()->id(),
                'employee_id' => $this->currentEmployeeId(),
            ]);

            $input = $request->all();

            $input['emp_id'] = $this->nullableInt($input['emp_id'] ?? null);
            $input['request_to'] = $this->nullableInt($input['request_to'] ?? null);
            $input['department_id'] = $this->nullableInt($input['department_id'] ?? null);
            $input['year'] = $this->nullableInt($input['year'] ?? null, now()->year);

            $input['leave_day'] = $this->nullableNumber($input['leave_day'] ?? null, null);
            $input['remaining_day'] = $this->nullableNumber($input['remaining_day'] ?? null, 0);
            $input['duration'] = $this->nullableNumber($input['duration'] ?? null, null);
            $input['last_year_remainings'] = $this->nullableNumber($input['last_year_remainings'] ?? null, null);

            $validator = Validator::make($input, [
                'emp_id' => ['required', 'integer', 'exists:employees,id'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'leave_day' => ['nullable', 'numeric', 'min:0'],
                'remaining_day' => ['nullable', 'numeric', 'min:0'],
                'duration' => ['nullable', 'numeric', 'min:0'],
                'reason' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'request_to' => ['nullable', 'integer', 'exists:employees,id'],
                'active_tab' => ['nullable', 'string'],
                'department_id' => ['nullable', 'integer'],
                'year' => ['nullable', 'integer'],
                'last_year_remainings' => ['nullable', 'numeric'],
            ]);

            if ($validator->fails()) {
                Log::warning('LeaveController@store validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'payload' => $input,
                ]);

                return $this->errorResponse(
                    $request,
                    'Validierung fehlgeschlagen.',
                    422,
                    $validator->errors()->toArray()
                );
            }

            $data = $validator->validated();
            $employeeId = (int) $data['emp_id'];

            $duration = $this->calculateWorkingDays($data['start_date'], $data['end_date']);

            if ($duration <= 0) {
                return $this->errorResponse(
                    $request,
                    'Die Urlaubsdauer muss mindestens einen Werktag betragen.',
                    422
                );
            }

            if ($this->hasOverlappingLeave($employeeId, $data['start_date'], $data['end_date'])) {
                return $this->errorResponse(
                    $request,
                    'Ein Urlaub in diesem Zeitraum wurde bereits beantragt.',
                    422
                );
            }

            DB::beginTransaction();

            $leavePayload = [
                'emp_id' => $employeeId,
                'year' => $data['year'] ?? Carbon::parse($data['start_date'])->year,
                'leave_type' => 'Personal',
                'created_by' => $this->currentActorId(),
                'request_to' => $data['request_to'] ?? null,
                'reason' => $data['reason'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'remaining_day' => $data['remaining_day'] ?? 0,
                'leave_day' => $data['leave_day'] ?? $duration,
                'duration' => $duration,
                'status' => 'Pending',
                'approved' => null,
                'request_answer' => null,
                'description' => $data['description'] ?? null,
            ];

            $this->setOptionalLeaveColumn($leavePayload, 'changed_by', $this->currentActorId());
            $this->setOptionalLeaveColumn($leavePayload, 'paid', 'No');
            $this->setOptionalLeaveColumn($leavePayload, 'request_back', null);
            $this->setOptionalLeaveColumn($leavePayload, 'old_start', null);
            $this->setOptionalLeaveColumn($leavePayload, 'old_end', null);
            $this->setOptionalLeaveColumn($leavePayload, 'department_id', $data['department_id'] ?? null);
            $this->setOptionalLeaveColumn($leavePayload, 'last_year_remainings', $data['last_year_remainings'] ?? null);

            $leave = Leave::create($leavePayload);

            DB::commit();

            Log::info('LeaveController@store leave created', [
                'leave_id' => $leave->id,
                'emp_id' => $employeeId,
                'request_to' => $leave->request_to,
            ]);

            return $this->successResponse($request, [
                'message' => 'Urlaub erfolgreich beantragt!',
                'leave_id' => $leave->id,
            ], 'Urlaub erfolgreich beantragt!');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('LeaveController@store failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'payload' => $request->all(),
                'user_id' => auth()->id(),
                'employee_id' => $this->currentEmployeeId(),
            ]);

            return $this->errorResponse($request, 'Serverfehler: ' . $e->getMessage(), 500);
        }
    }

    public function representer(Request $request)
    {
        return redirect()->back()->withInput()->with('active_tab', 'department');
    }

    public function edit(Leave $leave)
    {
        //
    }

    /**
     * Update leave request.
     */
    public function update(Request $request)
    {
        try {
            Log::info('LeaveController@update incoming request', [
                'payload' => $request->all(),
                'user_id' => auth()->id(),
                'employee_id' => $this->currentEmployeeId(),
            ]);

            $input = $request->all();
            $input['emp_id'] = $this->nullableInt($input['emp_id'] ?? null);
            $input['leave_day'] = $this->nullableNumber($input['leave_day'] ?? null, null);
            $input['remaining_day'] = $this->nullableNumber($input['remaining_day'] ?? null, 0);
            $input['duration'] = $this->nullableNumber($input['duration'] ?? null, null);

            $validator = Validator::make($input, [
                'id' => ['required', 'integer', 'exists:leaves,id'],
                'emp_id' => ['nullable', 'integer', 'exists:employees,id'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'leave_day' => ['nullable', 'numeric', 'min:0'],
                'remaining_day' => ['nullable', 'numeric', 'min:0'],
                'duration' => ['nullable', 'numeric', 'min:0'],
                'reason' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    $request,
                    'Validierung fehlgeschlagen.',
                    422,
                    $validator->errors()->toArray()
                );
            }

            $data = $validator->validated();

            $leave = $this->findLeave($data['id']);

            if (!$leave) {
                return $this->errorResponse($request, 'Urlaubsantrag wurde nicht gefunden.', 404);
            }

            $employeeId = (int) ($data['emp_id'] ?? $leave->emp_id);
            $duration = $this->calculateWorkingDays($data['start_date'], $data['end_date']);

            if ($duration <= 0) {
                return $this->errorResponse(
                    $request,
                    'Die Urlaubsdauer muss mindestens einen Werktag betragen.',
                    422
                );
            }

            if ($this->hasOverlappingLeave($employeeId, $data['start_date'], $data['end_date'], (int) $leave->id)) {
                return $this->errorResponse(
                    $request,
                    'Ein Urlaub in diesem Zeitraum wurde bereits beantragt.',
                    422
                );
            }

            $oldStart = $leave->start_date;
            $oldEnd = $leave->end_date;

            // MODELL A: War der Antrag vorher GENEHMIGT (= abgebucht), den ALTEN Abzug zurueckbuchen.
            // Der editierte Antrag geht auf Pending und wird erst bei erneuter Genehmigung neu abgebucht
            // -> bei Kuerzung wird netto nur die Differenz abgebucht. Idempotent (danach Pending -> kein zweites Mal).
            $wasApprovedBeforeUpdate = $this->leaveIsApproved($leave);
            $oldEmpId = (int) $leave->emp_id;
            $oldDuration = (int) $leave->duration;

            $payload = [
                'emp_id' => $employeeId,
                'year' => Carbon::parse($data['start_date'])->year,
                'changed_by' => $this->currentActorId(),
                'old_start' => $oldStart,
                'old_end' => $oldEnd,
                'request_back' => 'yes',
                'leave_type' => 'Personal',
                'reason' => $data['reason'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'remaining_day' => $data['remaining_day'] ?? 0,
                'leave_day' => $data['leave_day'] ?? $duration,
                'duration' => $duration,
                'paid' => 'No',
                'approved' => 'Pending',
                'status' => 'Pending',
                'description' => $data['description'] ?? null,
            ];

            $leave->update($payload);

            if ($wasApprovedBeforeUpdate && $oldEmpId && $oldDuration > 0) {
                Employee::where('id', $oldEmpId)->increment('remaining_day', $oldDuration);
            }

            return $this->successResponse($request, [
                'message' => 'Urlaub erfolgreich aktualisiert!',
                'leave_id' => $leave->id,
            ], 'Urlaub erfolgreich aktualisiert!');
        } catch (\Throwable $e) {
            Log::error('LeaveController@update failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'payload' => $request->all(),
            ]);

            return $this->errorResponse($request, 'Serverfehler: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $leave = $this->findLeave($id);

            if (!$leave) {
                return $this->errorResponse($request, 'Urlaubsantrag wurde nicht gefunden.', 404);
            }

            // MODELL A: War der Urlaub genehmigt (= abgebucht), die Tage VOR dem Loeschen zurueckbuchen.
            // Hard-Delete (kein SoftDeletes) => zweites destroy() findet nichts => keine Doppel-Rueckbuchung.
            DB::transaction(function () use ($leave) {
                if ($this->leaveIsApproved($leave)) {
                    $this->creditRemainingDay($leave);
                }
                $leave->delete();
            });

            return $this->successResponse($request, [
                'message' => 'Der Urlaub wurde erfolgreich gelöscht!',
                'leave_id' => (int) $id,
            ], 'Der Urlaub wurde erfolgreich gelöscht!');
        } catch (\Throwable $e) {
            Log::error('LeaveController@destroy failed', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);

            return $this->errorResponse($request, 'Serverfehler: ' . $e->getMessage(), 500);
        }
    }

    /**
     * First response from requested employee:
     * accept = sends it to review
     * reject = proposes changed dates.
     */
    public function accept(Request $request)
    {
        try {
            Log::info('LeaveController@accept incoming request', $request->all());

            $validator = Validator::make($request->all(), [
                'leave_id' => ['required', 'integer', 'exists:leaves,id'],
                'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'type' => ['required', 'in:accept,reject'],
                'comment' => ['nullable', 'string', 'max:5000'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validierung fehlgeschlagen.',
                    'errors' => $validator->errors(),
                    'active_tab' => 'leave',
                ], 422);
            }

            $data = $validator->validated();
            $leave = $this->findLeave($data['leave_id']);

            if (!$leave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Urlaubsantrag wurde nicht gefunden.',
                    'active_tab' => 'leave',
                ], 404);
            }

            $oldStart = $leave->start_date;
            $oldEnd = $leave->end_date;

            if ($data['type'] === 'accept') {
                $leave->update([
                    'request_answer' => 'accept',
                    'status' => 'Zur Überprüfung',
                    'changed_by' => $this->currentActorId(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Anfrage wurde akzeptiert und zur Überprüfung weitergeleitet.',
                    'active_tab' => 'leave',
                    'leave_id' => $leave->id,
                ]);
            }

            $leave->update([
                'start_date' => $data['start_date'] ?? $leave->start_date,
                'end_date' => $data['end_date'] ?? $leave->end_date,
                'old_start' => $oldStart,
                'old_end' => $oldEnd,
                'changed_by' => $this->currentActorId(),
                'status' => 'Anfrage',
                'request_answer' => 'reject',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anfrage wurde abgelehnt/geändert.',
                'active_tab' => 'leave',
                'leave_id' => $leave->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('LeaveController@accept failed', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler: ' . $e->getMessage(),
                'active_tab' => 'leave',
            ], 500);
        }
    }

    /**
     * Final approval/change step.
     */
    public function change(Request $request)
    {
        try {
            Log::info('LeaveController@change incoming request', $request->all());

            $validator = Validator::make($request->all(), [
                'leave_id' => ['required', 'integer', 'exists:leaves,id'],
                'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'type' => ['required', 'in:accept,reject'],
                'comment' => ['nullable', 'string', 'max:5000'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validierung fehlgeschlagen.',
                    'errors' => $validator->errors(),
                    'active_tab' => 'leave',
                ], 422);
            }

            $data = $validator->validated();
            $leave = $this->findLeave($data['leave_id']);

            if (!$leave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Urlaubsantrag wurde nicht gefunden.',
                    'active_tab' => 'leave',
                ], 404);
            }

            $oldStart = $leave->start_date;
            $oldEnd = $leave->end_date;

            if ($data['type'] === 'accept') {
                // MODELL A: finale Genehmigung -> idempotent abbuchen, exakt wie approve().
                $wasApproved = $this->leaveIsApproved($leave);

                $leave->update([
                    'request_answer' => 'accept',
                    'approved' => 'Yes',
                    'status' => 'accept',
                    'changed_by' => $this->currentActorId(),
                ]);

                if (!$wasApproved) {
                    $this->debitRemainingDay($leave);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Urlaub wurde genehmigt.',
                    'active_tab' => 'leave',
                    'leave_id' => $leave->id,
                ]);
            }

            // MODELL A: Ablehnung eines vorher GENEHMIGTEN Antrags bucht die Tage zurueck (idempotent:
            // danach status='Anfrage' -> nicht mehr genehmigt -> kein zweites Mal).
            $wasApproved = $this->leaveIsApproved($leave);

            $leave->update([
                'start_date' => $data['start_date'] ?? $leave->start_date,
                'end_date' => $data['end_date'] ?? $leave->end_date,
                'old_start' => $oldStart,
                'old_end' => $oldEnd,
                'changed_by' => $this->currentActorId(),
                'status' => 'Anfrage',
                'request_answer' => 'reject',
            ]);

            if ($wasApproved) {
                $this->creditRemainingDay($leave);
            }

            return response()->json([
                'success' => true,
                'message' => 'Urlaub wurde abgelehnt/geändert.',
                'active_tab' => 'leave',
                'leave_id' => $leave->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('LeaveController@change failed', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler: ' . $e->getMessage(),
                'active_tab' => 'leave',
            ], 500);
        }
    }

    public function getNotes($id)
    {
        $leave = Leave::findOrFail($id);

        return response()->json($this->normalizeNotes($leave->notes));
    }

    public function storeNote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'text' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Notiz ist leer oder zu lang.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $leave = Leave::findOrFail($id);
        $notes = $this->normalizeNotes($leave->notes);

        $employeeId = $this->currentEmployeeId();
        $employee = $employeeId ? Employee::find($employeeId) : null;

        $text = (string) $request->input('text');

        $notes[] = [
            'employee_id' => $employee?->id,
            'employee' => trim(($employee?->name ?? '') . ' ' . ($employee?->lastname ?? '')) ?: 'Unbekannt',
            'image' => $employee?->image,
            'date' => now()->format('Y-m-d H:i'),
            'text' => preg_replace('/@([\w\.\-]+)/', '<span class="mention">@$1</span>', e($text)),
        ];

        $this->saveNotes($leave, $notes);

        return response()->json([
            'success' => true,
            'notes' => $notes,
        ]);
    }

    public function updateNote(Request $request, $id, $index)
    {
        $validator = Validator::make($request->all(), [
            'text' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Notiz ist leer oder zu lang.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $leave = Leave::findOrFail($id);
        $notes = $this->normalizeNotes($leave->notes);

        if (isset($notes[$index])) {
            $notes[$index]['text'] = preg_replace(
                '/@([\w\.\-]+)/',
                '<span class="mention">@$1</span>',
                e((string) $request->input('text'))
            );

            $notes[$index]['updated_at'] = now()->format('Y-m-d H:i');

            $this->saveNotes($leave, $notes);
        }

        return response()->json([
            'success' => true,
            'notes' => $notes,
        ]);
    }

    public function deleteNote($id, $index)
    {
        $leave = Leave::findOrFail($id);
        $notes = $this->normalizeNotes($leave->notes);

        if (isset($notes[$index])) {
            unset($notes[$index]);
            $notes = array_values($notes);
            $this->saveNotes($leave, $notes);
        }

        return response()->json([
            'success' => true,
            'notes' => $notes,
        ]);
    }

    /**
     * Old mention endpoint.
     */
    public function getEmployees(Request $request)
    {
        $employees = DB::table('employees')
            ->when(Schema::hasColumn('employees', 'deleted_at'), function ($q) {
                $q->whereNull('employees.deleted_at');
            })
            ->select([
                'employees.id',
                'employees.name',
                'employees.lastname',
                'employees.email',
                'employees.image',
            ])
            ->orderBy('employees.name')
            ->orderBy('employees.lastname')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'emp_id' => $employee->id,
                    'name' => $employee->name ?? '',
                    'lastname' => $employee->lastname ?? '',
                    'email' => $employee->email ?? '',
                    'image' => $employee->image ?? null,
                    'full_name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'text' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                ];
            });

        return response()->json($employees);
    }

    public function getEmployeeMainDepartment($employeeId)
    {
        try {
            $employeeId = (int) $employeeId;

            if ($employeeId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitarbeiter fehlt.',
                    'department_id' => null,
                ], 422);
            }

            $departmentPosition = DB::table('department_positions as dp')
                ->join('departments as d', 'd.id', '=', 'dp.department_id')
                ->where('dp.employee_id', $employeeId)
                ->when(Schema::hasColumn('departments', 'deleted_at'), function ($q) {
                    $q->whereNull('d.deleted_at');
                })
                ->orderByRaw("
                    CASE
                        WHEN dp.main = 'Yes' THEN 1
                        WHEN dp.main = 'yes' THEN 1
                        WHEN dp.main = 'active' THEN 1
                        WHEN dp.main = 'Active' THEN 1
                        WHEN dp.main = '1' THEN 1
                        WHEN dp.main = 1 THEN 1
                        ELSE 0
                    END DESC
                ")
                ->orderByDesc('dp.id')
                ->first([
                    'dp.department_id',
                    'd.department_name',
                    'd.department_head',
                    'd.head_representative',
                ]);

            if (!$departmentPosition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Für diesen Mitarbeiter wurde keine Hauptabteilung gefunden.',
                    'department_id' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'department_id' => (int) $departmentPosition->department_id,
                'department_name' => $departmentPosition->department_name,
                'department_head' => $departmentPosition->department_head,
                'head_representative' => $departmentPosition->head_representative,
            ]);
        } catch (\Throwable $e) {
            Log::error('getEmployeeMainDepartment failed', [
                'employee_id' => $employeeId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler: ' . $e->getMessage(),
                'department_id' => null,
            ], 500);
        }
    }

    public function getDepartmentLeaders($departmentId)
    {
        try {
            $departmentId = (int) $departmentId;

            $department = DB::table('departments')
                ->where('id', $departmentId)
                ->when(Schema::hasColumn('departments', 'deleted_at'), function ($q) {
                    $q->whereNull('deleted_at');
                })
                ->first([
                    'id',
                    'department_name',
                    'department_head',
                    'head_representative',
                ]);

            if (!$department) {
                return response()->json([]);
            }

            $leaderIds = collect([
                $department->department_head ?? null,
                $department->head_representative ?? null,
            ])
                ->filter()
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->unique()
                ->values();

            if ($leaderIds->isEmpty()) {
                return response()->json([]);
            }

            $leaders = DB::table('employees')
                ->whereIn('id', $leaderIds)
                ->when(Schema::hasColumn('employees', 'deleted_at'), function ($q) {
                    $q->whereNull('deleted_at');
                })
                ->select([
                    'id as emp_id',
                    'id',
                    'name',
                    'lastname',
                    'email',
                    'image',
                ])
                ->orderBy('name')
                ->orderBy('lastname')
                ->get()
                ->map(function ($employee) {
                    return [
                        'emp_id' => $employee->emp_id,
                        'id' => $employee->id,
                        'name' => $employee->name ?? '',
                        'lastname' => $employee->lastname ?? '',
                        'email' => $employee->email ?? '',
                        'image' => $employee->image ?? null,
                        'full_name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                        'text' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    ];
                });

            return response()->json($leaders);
        } catch (\Throwable $e) {
            Log::error('getDepartmentLeaders failed', [
                'department_id' => $departmentId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([], 500);
        }
    }

    public function overview(Request $request)
    {
        try {
            $authUser = $request->user();

            $employee = DB::table('employees')
                ->where('id', $authUser->name)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitarbeiter nicht gefunden.',
                ], 404);
            }

            $year = Carbon::now()->year;

            $leaves = Leave::query()
                ->where('emp_id', $employee->id)
                ->where('year', $year)
                ->orderByDesc('start_date')
                ->limit(5)
                ->get(['id', 'start_date', 'end_date', 'duration', 'status', 'approved'])
                ->map(function ($leave) {
                    $statusRaw = $leave->approved === 'Yes'
                        ? 'approved'
                        : ($leave->status ?? '');

                    $status = strtolower(trim((string) $statusRaw));

                    $leave->status_label = match ($status) {
                        'accept', 'accepted', 'approve', 'approved' => 'Genehmigt',
                        'reject', 'rejected' => 'Abgelehnt',
                        'pending', '', null => 'Ausstehend',
                        default => ucfirst((string) $statusRaw),
                    };

                    return $leave;
                });

            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'remaining_day' => (int) ($employee->remaining_day ?? 0),
                ],
                'year' => $year,
                'leaves' => $leaves,
            ]);
        } catch (\Throwable $e) {
            Log::error('LeaveController@overview failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Small dashboard leave save endpoint.
     * Kept separate from store() to avoid breaking old leave forms.
     */
    public function save(Request $request)
    {
        try {
            $authUser = $request->user();

            $employee = DB::table('employees')
                ->where('id', $authUser->name)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitarbeiter nicht gefunden.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'reason' => ['required', 'string', 'max:255'],
                'note' => ['nullable', 'string', 'max:5000'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validierung fehlgeschlagen.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);
            $year = $start->year;
            $duration = $this->calculateWorkingDays($start->toDateString(), $end->toDateString());

            if ($duration <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Die Urlaubsdauer muss mindestens einen Werktag betragen.',
                ], 422);
            }

            $remaining = (int) ($employee->remaining_day ?? 0);

            if ($duration > $remaining) {
                return response()->json([
                    'success' => false,
                    'message' => 'Du hast nicht genug verbleibende Urlaubstage.',
                ], 422);
            }

            if ($this->hasOverlappingLeave((int) $employee->id, $start->toDateString(), $end->toDateString())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ein Urlaub in diesem Zeitraum wurde bereits beantragt.',
                ], 422);
            }

            DB::beginTransaction();

            $newRemaining = max(0, $remaining - $duration);

            $leavePayload = [
                'emp_id' => $employee->id,
                'created_by' => $employee->id,
                'request_to' => $employee->supervisor ?? $employee->id,
                'year' => $year,
                'leave_type' => 'urlaub',
                'reason' => $validated['reason'],
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'remaining_day' => $newRemaining,
                'leave_day' => $duration,
                'duration' => $duration,
                'approved' => null,
                'status' => 'pending',
                'request_answer' => null,
                'description' => $validated['note'] ?? null,
            ];

            $this->setOptionalLeaveColumn($leavePayload, 'changed_by', null);
            $this->setOptionalLeaveColumn($leavePayload, 'old_start', null);
            $this->setOptionalLeaveColumn($leavePayload, 'old_end', null);
            $this->setOptionalLeaveColumn($leavePayload, 'paid', 'yes');
            $this->setOptionalLeaveColumn($leavePayload, 'request_back', null);
            $this->setOptionalLeaveColumn($leavePayload, 'notes', null);

            $leave = Leave::create($leavePayload);

            // MODELL A (Bug 2): Der Self-Service-Antrag bucht NICHT mehr sofort ab. Der Saldo bleibt
            // unberuehrt; abgebucht wird ausschliesslich bei der Genehmigung (approve / change-accept).
            // Damit verschwindet auch der fruehere Doppel-Abzug (save() + spaeteres approve()).

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dein Urlaubsantrag wurde gespeichert.',
                'remaining_day' => $newRemaining,
                'leave_id' => $leave->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('LeaveController@save failed', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler: ' . $e->getMessage(),
            ], 500);
        }
    }
}
