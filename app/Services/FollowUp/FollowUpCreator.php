<?php

namespace App\Services\FollowUp;

use App\Models\EmployeesPersonalTask;
use App\Models\PersonalTask;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

/**
 * EINE Erzeugungs-Stelle fuer Follow-ups (F4). Alle Karten-Flows (Notiz, kanban_lead_task,
 * Kundenbericht, Termin-Sync) docken hier an - kein Copy-Paste je Controller.
 *
 * Traeger = personal_tasks mit type='follow_up' (Termin-Muster verallgemeinert):
 * naechster Schritt = task_title (auf /home garantiert sichtbar), Zuweisung ueber
 * employees_personal_tasks-Pivot, /home ueber das "Meine Follow-ups"-Widget. next_reminder_at
 * bleibt NULL -> ProcessPersonalTaskScheduler ueberspringt -> Stufe-1 dashboard-only (kein Mail/Push).
 *
 * UPSERT by (source_type, source_id): ein Quell-Objekt hat hoechstens EIN Follow-up. Dadurch
 * erzeugt ein wiederholter Aufruf (z.B. Termin-Update) KEIN Duplikat, sondern aktualisiert.
 */
class FollowUpCreator
{
    /** Belegte Herkunfts-Liste (F1: 5 + F4-Termin: main_appointment = 6). Kein stilles Wildern. */
    public const SOURCE_TYPES = [
        'customer_note',
        'kanban_lead_task',
        'customer_report',
        'lead_product_list',
        'appointment_report',
        'main_appointment',
    ];

    /** Ausgang (Dialog) -> follow_up_art. 'keine'/unbekannt -> null (Caller erzeugt dann keine Zeile). */
    public static function artFromOutcome(?string $outcome): ?string
    {
        return match ($outcome) {
            'weitere_aufgaben' => 'wiederaufnahme',
            'nachfass' => 'nachfass',
            default => null,
        };
    }

    /**
     * Upsert EIN Follow-up fuer (sourceType, sourceId).
     *
     * @param array $context customer_id|alternative_id|product_id|lead_product_list_id|description
     * @param array $data    follow_up_art(nullable)|next_step|due_date|responsible(int|array)
     */
    public function sync(string $sourceType, int $sourceId, array $context, array $data, int $creatorEmployeeId): PersonalTask
    {
        $responsibles = array_values(array_unique(array_filter(array_map('intval', (array) ($data['responsible'] ?? [])))));
        if (empty($responsibles)) {
            $responsibles = [$creatorEmployeeId]; // Default = Ersteller
        }

        $attrs = [
            'task_title'           => Str::limit(trim(strip_tags((string) ($data['next_step'] ?? ''))), 120, '') ?: 'Follow-up',
            'description'          => $context['description'] ?? null,
            'task_status'          => 'open',
            'type'                 => 'follow_up',
            'follow_up_art'        => $data['follow_up_art'] ?? null, // NULL erlaubt (Termin-Sync ohne Outcome)
            'source_type'          => $sourceType,
            'source_id'            => $sourceId,
            'due_date'             => $data['due_date'] ?? null,
            'reminder_date'        => $data['due_date'] ?? null,
            'next_reminder_at'     => null, // Stufe-1: Scheduler ueberspringt -> kein Mail/Push
            'assigned_by'          => (string) $creatorEmployeeId,
            'customer_id'          => $context['customer_id'] ?? null,
            'alternative_id'       => $context['alternative_id'] ?? null,
            'product_id'           => $context['product_id'] ?? null,
            'lead_product_list_id' => $context['lead_product_list_id'] ?? null,
            'controller_id'        => $responsibles, // Model-Cast 'array' -> einmal JSON-encodiert
        ];

        // Natürlicher Schlüssel (type, source_type, source_id) — ab A1b DB-Unique-Index.
        $lookup = fn () => PersonalTask::query()
            ->where('type', 'follow_up')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();

        // A1b-Kante (Soft-Delete): Der Unique-Index zählt AUCH soft-gelöschte Zeilen, der
        // Pre-Check (SoftDelete-Scope) sieht sie NICHT. Kollidiert create() gegen eine
        // soft-gelöschte Zeile, muss der Catch sie inkl. trashed finden und wiederherstellen —
        // sonst 500 statt Idempotenz. Der Quell-Vorgang braucht das Follow-up wieder.
        $lookupTrashed = fn () => PersonalTask::withTrashed()
            ->where('type', 'follow_up')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();

        $task = $lookup();

        if ($task) {
            $task->fill($attrs)->save();
            EmployeesPersonalTask::where('task_id', $task->id)->delete(); // Pivot neu setzen
        } else {
            try {
                $task = PersonalTask::create($attrs);
            } catch (QueryException $e) {
                // Race (Doppel-Submit/Queue-Retry): ein paralleler Thread hat zwischen
                // Pre-Check und create() dieselbe Zeile angelegt -> Unique-Verletzung
                // (SQLSTATE 23000). DB-Constraint = Wahrheit, dieser Catch = freundliche
                // Hülle: den nun existierenden Task neu laden und den UPDATE-Pfad anwenden
                // (last-write-wins, konsistent mit der UPSERT-Semantik). Kein 500, kein Datenmüll.
                if (($e->errorInfo[0] ?? null) !== '23000') {
                    throw $e; // andere DB-Fehler nicht verschlucken
                }

                // inkl. trashed suchen: die kollidierende Zeile kann live ODER soft-gelöscht sein.
                $task = $lookupTrashed();
                if (! $task) {
                    throw $e; // Unique-Verletzung ohne auffindbare Zeile -> echter Fehler
                }

                if (method_exists($task, 'trashed') && $task->trashed()) {
                    $task->restore(); // soft-gelöschtes Follow-up wiederbeleben statt 500
                }

                $task->fill($attrs)->save();
                EmployeesPersonalTask::where('task_id', $task->id)->delete(); // Pivot neu setzen
            }
        }

        foreach ($responsibles as $responsible) {
            EmployeesPersonalTask::create([
                'employee_id' => $responsible,
                'task_id'     => $task->id,
                'status'      => 'send',
            ]);
        }

        return $task;
    }
}
