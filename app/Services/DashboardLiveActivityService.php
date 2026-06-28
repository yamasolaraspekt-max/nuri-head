<?php

namespace App\Services;

use App\Events\DashboardLiveActivityCreated;
use App\Models\DashboardLiveActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardLiveActivityService
{
    public function notifyEmployees(array $employeeIds, array $data): void
    {
        $employeeIds = collect($employeeIds)
            ->filter()
            ->map(fn($id) => (string) $id)
            ->unique()
            ->values();

        if ($employeeIds->isEmpty()) {
            return;
        }

        foreach ($employeeIds as $employeeId) {
            $activity = DashboardLiveActivity::create([
                'employee_id' => $employeeId,
                'actor_user_id' => Auth::id(),
                'actor_employee_id' => Auth::check() ? Auth::user()->name : null,
                'type' => $data['type'] ?? 'general',
                'action' => $data['action'] ?? 'created',
                'subject_type' => $data['subject_type'] ?? null,
                'subject_id' => $data['subject_id'] ?? null,
                'title' => $data['title'] ?? 'Neue Aktivität',
                'message' => $data['message'] ?? null,
                'url' => $data['url'] ?? null,
                'payload' => $data['payload'] ?? null,
            ]);

            broadcast(new DashboardLiveActivityCreated($activity))->toOthers();
        }
    }

    public function notifyTask($task): void
    {
        $employeeIds = DB::table('employees_personal_tasks')
            ->where('task_id', $task->id)
            ->pluck('employee_id')
            ->toArray();

        $this->notifyEmployees($employeeIds, [
            'type' => 'task',
            'subject_type' => get_class($task),
            'subject_id' => $task->id,
            'title' => 'Neue Aufgabe',
            'message' => $task->task_title ?? $task->title ?? 'Eine neue Aufgabe wurde erstellt.',
            'url' => url('/personal-tasks'),
        ]);
    }

    public function notifyAppointment($appointment): void
    {
        $employeeIds = [];

        if (method_exists($appointment, 'employees')) {
            $employeeIds = $appointment->employees()->pluck('employees.id')->toArray();
        }

        $this->notifyEmployees($employeeIds, [
            'type' => 'appointment',
            'subject_type' => get_class($appointment),
            'subject_id' => $appointment->id,
            'title' => 'Neuer Termin',
            'message' => $appointment->name ?? 'Ein neuer Termin wurde erstellt.',
            'url' => url('/calendar'),
        ]);
    }

    public function notifyLead($lead, ?int $leadProductListId = null): void
    {
        $employeeIds = collect();

        if ($leadProductListId) {
            $row = DB::table('lead_product_lists')
                ->where('id', $leadProductListId)
                ->first(['employee_id', 'field_employee']);

            if ($row) {
                $employeeIds->push($row->employee_id);
                $employeeIds->push($row->field_employee);
            }
        }

        if (!empty($lead->contact_person)) {
            $employeeIds->push($lead->contact_person);
        }

        $this->notifyEmployees($employeeIds->toArray(), [
            'type' => 'lead',
            'subject_type' => get_class($lead),
            'subject_id' => $lead->id,
            'title' => 'Neuer Lead',
            'message' => trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? '')) ?: 'Ein neuer Lead wurde erstellt.',
            'url' => url('/new_lead_view/' . $lead->id),
        ]);
    }

    public function notifyInquiry($inquiry, ?int $inquiryProductListId = null): void
    {
        $employeeIds = collect();

        if ($inquiryProductListId) {
            $row = DB::table('inquiry_product_lists')
                ->where('id', $inquiryProductListId)
                ->first(['employee_id', 'field_employee']);

            if ($row) {
                $employeeIds->push($row->employee_id);
                $employeeIds->push($row->field_employee);
            }
        }

        if (!empty($inquiry->employee_id)) {
            $employeeIds->push($inquiry->employee_id);
        }

        if (!empty($inquiry->contact_person)) {
            $employeeIds->push($inquiry->contact_person);
        }

        $this->notifyEmployees($employeeIds->toArray(), [
            'type' => 'inquiry',
            'subject_type' => get_class($inquiry),
            'subject_id' => $inquiry->id,
            'title' => 'Neue Anfrage',
            'message' => trim(($inquiry->name ?? '') . ' ' . ($inquiry->lastname ?? '')) ?: 'Eine neue Anfrage wurde erstellt.',
            'url' => url('/inquiries/' . $inquiry->id),
        ]);
    }

    public function notifyOffer($offer): void
    {
        $employeeIds = collect([
            $offer->employee_id ?? null,
            $offer->created_by ?? null,
            $offer->contact_person ?? null,
        ]);

        $this->notifyEmployees($employeeIds->toArray(), [
            'type' => 'offer',
            'subject_type' => get_class($offer),
            'subject_id' => $offer->id,
            'title' => 'Neues Angebot',
            'message' => $offer->title ?? $offer->offer_no ?? 'Ein neues Angebot wurde erstellt.',
            'url' => url('/offer/' . $offer->id),
        ]);
    }

    public function notifyDeal($deal): void
    {
        $employeeIds = collect([
            $deal->employee_id ?? null,
            $deal->created_by ?? null,
            $deal->contact_person ?? null,
        ]);

        $this->notifyEmployees($employeeIds->toArray(), [
            'type' => 'deal',
            'subject_type' => get_class($deal),
            'subject_id' => $deal->id,
            'title' => 'Neuer Auftrag / Deal',
            'message' => $deal->title ?? $deal->deal_no ?? 'Ein neuer Auftrag wurde erstellt.',
            'url' => url('/deals/' . $deal->id),
        ]);
    }

    public function notifyTicket($ticket): void
    {
        $employeeIds = collect();

        if (method_exists($ticket, 'employees')) {
            $employeeIds = $ticket->employees()->pluck('employees.id');
        }

        if (!empty($ticket->employee_id)) {
            $employeeIds->push($ticket->employee_id);
        }

        $this->notifyEmployees($employeeIds->toArray(), [
            'type' => 'ticket',
            'subject_type' => get_class($ticket),
            'subject_id' => $ticket->id,
            'title' => 'Neues Ticket',
            'message' => $ticket->problem ?? $ticket->title ?? 'Ein neues Ticket wurde erstellt.',
            'url' => url('/problems/' . $ticket->id),
        ]);
    }
}