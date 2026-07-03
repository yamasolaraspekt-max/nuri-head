<?php

namespace App\Http\Controllers;

use App\Models\PersonalTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * F3 — Aktionen fuer das "Meine Follow-ups"-Widget (/home).
 *
 * STRENG gescoped: eine Aktion greift nur, wenn (a) der Datensatz type='follow_up' ist UND
 * (b) der eingeloggte Mitarbeiter zugeordnet ist (employees_personal_tasks-Pivot ODER
 * controller_id). Sonst 403. Grund: die bestehenden PersonalTask-Status-Routen
 * (updateStatus/dueDateUpdate) haben KEINE Berechtigungspruefung -> hier ein eigener,
 * enger Weg, damit ein Fremder ein fremdes Follow-up nicht abschliessen/verschieben kann.
 */
class FollowUpController extends Controller
{
    /** Laedt das Follow-up und stellt sicher, dass es MIR gehoert; sonst 403/404. */
    private function guardOwnFollowUp($taskId): PersonalTask
    {
        $employeeId = (int) (auth()->user()->name ?? 0);

        $task = PersonalTask::query()
            ->where('id', (int) $taskId)
            ->where('type', 'follow_up')
            ->whereNull('deleted_at')
            ->firstOrFail();

        $assigned = DB::table('employees_personal_tasks')
            ->where('task_id', $task->id)
            ->where('employee_id', $employeeId)
            ->exists();

        $controllers = is_array($task->controller_id) ? array_map('intval', $task->controller_id) : [];

        if ($employeeId <= 0 || (!$assigned && !in_array($employeeId, $controllers, true))) {
            abort(403, 'Kein Zugriff auf dieses Follow-up.');
        }

        return $task;
    }

    /** Follow-up erledigen: task_status='completed' + Pivot-Status 'done'. */
    public function complete(Request $request, $task)
    {
        $followup = $this->guardOwnFollowUp($task);

        $followup->task_status = 'completed';
        $followup->save();

        DB::table('employees_personal_tasks')->where('task_id', $followup->id)->update(['status' => 'done']);

        return response()->json(['success' => true, 'id' => $followup->id, 'status' => 'completed']);
    }

    /** Follow-up um 3 Tage verschieben (Faelligkeit + reminder_date). */
    public function snooze(Request $request, $task)
    {
        $followup = $this->guardOwnFollowUp($task);

        $base = $followup->due_date ? Carbon::parse($followup->due_date) : Carbon::now();
        $new = $base->copy()->addDays(3)->toDateString();

        $followup->due_date = $new;
        $followup->reminder_date = $new;
        $followup->save();

        return response()->json(['success' => true, 'id' => $followup->id, 'due_date' => $new]);
    }
}
