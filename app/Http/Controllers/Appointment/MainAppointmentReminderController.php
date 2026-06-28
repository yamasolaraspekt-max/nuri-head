<?php

namespace App\Http\Controllers\Appointment;
use App\Http\Controllers\Controller;

use App\Events\MainAppointmentReminderDue;
use App\Models\MainAppointment;
use App\Models\MainAppointmentReminderLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainAppointmentReminderController extends Controller
{
    private const MAX_REMINDERS = 3;
    private const REMINDER_WINDOW_MINUTES = 10;
    private const REMINDER_INTERVAL_MINUTES = 3;

    public function upcoming(Request $request)
    {
        $employeeId = (int) auth()->user()->name;

        if ($employeeId <= 0) {
            return response()->json([
                'has_reminder' => false,
                'appointment' => null,
            ]);
        }

        $appointment = $this->findUpcomingAppointmentForEmployee($employeeId);

        if (!$appointment) {
            return response()->json([
                'has_reminder' => false,
                'appointment' => null,
            ]);
        }

        $now = now();

        $log = MainAppointmentReminderLog::query()
            ->where('appointment_id', $appointment->id)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$this->canShowReminder($log, $now)) {
            return response()->json([
                'has_reminder' => false,
                'appointment' => null,
            ]);
        }

        if (!$log) {
            $log = MainAppointmentReminderLog::create([
                'appointment_id' => $appointment->id,
                'employee_id' => $employeeId,
                'reminder_at' => $now,
                'reminder_count' => 0,
                'last_reminded_at' => null,
                'seen_at' => null,
            ]);
        }

        $log->forceFill([
            'reminder_count' => ((int) $log->reminder_count) + 1,
            'last_reminded_at' => $now,
            'reminder_at' => $log->reminder_at ?: $now,
        ])->save();

        return response()->json([
            'has_reminder' => true,
            'appointment' => $this->formatAppointmentPayload($appointment),
            'reminder_count' => $log->reminder_count,
            'max_reminders' => self::MAX_REMINDERS,
        ]);
    }

    public function markSeen(Request $request, MainAppointment $appointment)
    {
        $employeeId = (int) auth()->user()->name;

        MainAppointmentReminderLog::updateOrCreate(
            [
                'appointment_id' => $appointment->id,
                'employee_id' => $employeeId,
            ],
            [
                'reminder_at' => now(),
                'seen_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
        ]);
    }

    public function test(Request $request, MainAppointment $appointment)
    {
        $employeeId = (int) auth()->user()->name;

        event(new MainAppointmentReminderDue($appointment, $employeeId));

        return response()->json([
            'success' => true,
            'message' => 'Test reminder sent.',
            'employee_id' => $employeeId,
            'appointment_id' => $appointment->id,
        ]);
    }

    private function findUpcomingAppointmentForEmployee(int $employeeId): ?MainAppointment
    {
        $now = Carbon::now();
        $from = $now->copy()->subSeconds(20);
        $to = $now->copy()->addMinutes(self::REMINDER_WINDOW_MINUTES);

        $appointments = MainAppointment::query()
            ->with(['customer'])
            ->whereNull('deleted_at')
            ->whereHas('appointmentEmployees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })
            ->whereDate('start_date', $now->toDateString())
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', [
                        'cancelled',
                        'canceled',
                        'storniert',
                        'done',
                        'completed',
                    ]);
            })
            ->whereDoesntHave('reminderLogs', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId)
                    ->whereNotNull('seen_at');
            })
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        return $appointments->first(function ($appointment) use ($from, $to) {
            $dateTime = $this->getAppointmentDateTime($appointment);

            if (!$dateTime) {
                return false;
            }

            return $dateTime->betweenIncluded($from, $to);
        });
    }

    private function getAppointmentDateTime(MainAppointment $appointment): ?Carbon
    {
        if (!$appointment->start_date || !$appointment->start_time) {
            return null;
        }

        try {
            $date = Carbon::parse($appointment->start_date)->format('Y-m-d');
            $time = Carbon::parse($appointment->start_time)->format('H:i:s');

            return Carbon::parse($date . ' ' . $time);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function canShowReminder(?MainAppointmentReminderLog $log, Carbon $now): bool
    {
        if (!$log) {
            return true;
        }

        if ($log->seen_at) {
            return false;
        }

        if ((int) $log->reminder_count >= self::MAX_REMINDERS) {
            return false;
        }

        if (
            $log->last_reminded_at &&
            Carbon::parse($log->last_reminded_at)->gt($now->copy()->subMinutes(self::REMINDER_INTERVAL_MINUTES))
        ) {
            return false;
        }

        return true;
    }

    private function formatAppointmentPayload(MainAppointment $appointment): array
    {
        $customer = $appointment->customer;

        $customerName = trim(
            ($customer->lastname ?? '') . ' ' . ($customer->name ?? '')
        );

        if (!$customerName && !empty($customer?->firma)) {
            $customerName = $customer->firma;
        }

        $address = $appointment->full_address;

        if (!$address) {
            $address = trim(
                ($appointment->street ?? '') . ', ' .
                ($appointment->postcode ?? '') . ' ' .
                ($appointment->city ?? '')
            );
        }

        return [
            'id' => $appointment->id,
            'title' => $appointment->name ?: 'Termin',
            'note' => $appointment->note,
            'appointment_type' => $appointment->appointment_type,
            'execution_type' => $appointment->execution_type,
            'start_date' => $appointment->start_date
                ? Carbon::parse($appointment->start_date)->format('Y-m-d')
                : null,
            'start_time' => $appointment->start_time,
            'end_time' => $appointment->end_time,
            'customer_id' => $appointment->customer_id,
            'customer_name' => $customerName ?: 'Unbekannter Kunde',
            'phone' => $appointment->phone ?: ($customer->phone ?? null),
            'email' => $appointment->email ?: ($customer->email ?? null),
            'address' => $address ?: 'Keine Adresse hinterlegt',
            'link' => $appointment->link,
            'url' => url('customer/appointments?appointment_id=' . $appointment->id),
        ];
    }
}