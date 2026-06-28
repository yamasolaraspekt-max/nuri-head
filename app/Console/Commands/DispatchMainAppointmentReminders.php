<?php

namespace App\Console\Commands;

use App\Events\MainAppointmentReminderDue;
use App\Models\MainAppointment;
use App\Models\MainAppointmentReminderLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchMainAppointmentReminders extends Command
{
    protected $signature = 'appointments:dispatch-reminders {--debug}';

    protected $description = 'Dispatch realtime reminders for main appointments 10 minutes before start.';

    private const MAX_REMINDERS = 3;
    private const REMINDER_WINDOW_MINUTES = 10;
    private const REMINDER_INTERVAL_MINUTES = 3;

    public function handle(): int
    {
        $now = Carbon::now();
        $from = $now->copy()->subSeconds(20);
        $to = $now->copy()->addMinutes(self::REMINDER_WINDOW_MINUTES);

        $appointments = MainAppointment::query()
            ->with(['customer', 'appointmentEmployees'])
            ->whereNull('deleted_at')
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
            ->get();

        if ($this->option('debug')) {
            $this->info('Now: ' . $now->format('Y-m-d H:i:s'));
            $this->info('Window: ' . $from->format('H:i:s') . ' - ' . $to->format('H:i:s'));
            $this->info('Appointments today: ' . $appointments->count());
        }

        foreach ($appointments as $appointment) {
            $appointmentDateTime = $this->getAppointmentDateTime($appointment);

            if ($this->option('debug')) {
                $this->line('----------------------------------------');
                $this->line('Appointment ID: ' . $appointment->id);
                $this->line('Start date: ' . $appointment->start_date);
                $this->line('Start time: ' . $appointment->start_time);
                $this->line('Parsed: ' . ($appointmentDateTime ? $appointmentDateTime->format('Y-m-d H:i:s') : 'NULL'));
                $this->line('Employees: ' . $appointment->appointmentEmployees->pluck('employee_id')->implode(', '));
            }

            if (!$appointmentDateTime) {
                if ($this->option('debug')) {
                    $this->warn('Skipped: no valid appointment datetime.');
                }

                continue;
            }

            if (!$appointmentDateTime->betweenIncluded($from, $to)) {
                if ($this->option('debug')) {
                    $this->warn('Skipped: outside 10-minute window.');
                }

                continue;
            }

            foreach ($appointment->appointmentEmployees as $appointmentEmployee) {
                $employeeId = (int) $appointmentEmployee->employee_id;

                if ($employeeId <= 0) {
                    continue;
                }

                $log = MainAppointmentReminderLog::query()
                    ->where('appointment_id', $appointment->id)
                    ->where('employee_id', $employeeId)
                    ->first();

                if (!$this->canSendReminder($log, $now)) {
                    if ($this->option('debug')) {
                        $reason = $this->blockedReason($log, $now);
                        $this->warn("Skipped employee {$employeeId}: {$reason}");
                    }

                    continue;
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

                event(new MainAppointmentReminderDue($appointment, $employeeId));

                $this->info(
                    'Appointment reminder sent: appointment_id=' .
                    $appointment->id .
                    ', employee_id=' .
                    $employeeId .
                    ', count=' .
                    $log->reminder_count
                );
            }
        }

        return self::SUCCESS;
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

    private function canSendReminder(?MainAppointmentReminderLog $log, Carbon $now): bool
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

    private function blockedReason(?MainAppointmentReminderLog $log, Carbon $now): string
    {
        if (!$log) {
            return 'not blocked';
        }

        if ($log->seen_at) {
            return 'already seen';
        }

        if ((int) $log->reminder_count >= self::MAX_REMINDERS) {
            return 'already reminded 3 times';
        }

        if (
            $log->last_reminded_at &&
            Carbon::parse($log->last_reminded_at)->gt($now->copy()->subMinutes(self::REMINDER_INTERVAL_MINUTES))
        ) {
            return 'last reminder was less than 3 minutes ago';
        }

        return 'unknown';
    }
}