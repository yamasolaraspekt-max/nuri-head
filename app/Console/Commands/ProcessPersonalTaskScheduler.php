<?php

namespace App\Console\Commands;

use App\Events\PersonalTaskReminderTriggered;
use App\Models\PersonalTask;
use App\Notifications\PersonalTaskReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessPersonalTaskScheduler extends Command
{
    protected $signature = 'personal-tasks:process-scheduler';

    protected $description = 'Process personal task reminders and repeated tasks';

    public function handle(): int
    {
        $this->processReminderTasks();
        $this->processRepeatTasks();

        return self::SUCCESS;
    }

    protected function processReminderTasks(): void
    {
        $now = now();

        PersonalTask::query()
            ->with(['employees:id,name,lastname,image'])
            ->whereNull('deleted_at')
            ->whereNull('archived_at')
            ->whereNotIn('task_status', ['completed', 'cancel', 'junk', 'rejected'])
            ->whereNotNull('reminder_date')
            ->whereNotNull('reminder_time')
            ->where(function ($q) use ($now) {
                $q->whereNull('next_reminder_at')
                    ->orWhere('next_reminder_at', '<=', $now);
            })
            ->chunkById(100, function ($tasks) use ($now) {
                foreach ($tasks as $task) {
                    DB::transaction(function () use ($task, $now) {
                        $task = PersonalTask::whereKey($task->id)->lockForUpdate()->first();

                        if (!$task || $task->deleted_at || $task->archived_at) {
                            return;
                        }

                        $reminderAt = $this->buildDateTime($task->reminder_date, $task->reminder_time);

                        if (!$reminderAt || $reminderAt->greaterThan($now)) {
                            $task->next_reminder_at = $reminderAt;
                            $task->save();
                            return;
                        }

                        if ($task->last_reminded_at && $task->last_reminded_at->greaterThanOrEqualTo($reminderAt)) {
                            return;
                        }

                        $this->notifyAssignedEmployees($task, 'reminder');

                        $task->forceFill([
                            'next_reminder_at' => $this->nextDateByRepeat($reminderAt, $task->repeat),
                            'last_reminded_at' => $now,
                            'reminder_count' => ((int) $task->reminder_count) + 1,
                        ])->save();
                    });
                }
            });
    }

    protected function processRepeatTasks(): void
    {
        $now = now();

        PersonalTask::query()
            ->with(['employees:id'])
            ->whereNull('deleted_at')
            ->whereNull('archived_at')
            ->whereNotNull('repeat')
            ->whereNotIn('repeat', ['', 'none', 'no', 'nicht', 'never'])
            ->whereNotNull('due_date')
            ->where(function ($q) use ($now) {
                $q->whereNull('next_repeat_at')
                    ->orWhere('next_repeat_at', '<=', $now);
            })
            ->chunkById(50, function ($tasks) use ($now) {
                foreach ($tasks as $task) {
                    DB::transaction(function () use ($task, $now) {
                        $task = PersonalTask::whereKey($task->id)->lockForUpdate()->first();

                        if (!$task || $task->deleted_at || $task->archived_at) {
                            return;
                        }

                        $baseAt = $this->buildDateTime($task->due_date, $task->due_time ?: '09:00');

                        if (!$baseAt || $baseAt->greaterThan($now)) {
                            $task->next_repeat_at = $baseAt;
                            $task->save();
                            return;
                        }

                        $nextAt = $this->nextDateByRepeat($baseAt, $task->repeat);

                        if (!$nextAt) {
                            return;
                        }

                        if ($task->last_repeated_at && $task->last_repeated_at->isSameDay($nextAt)) {
                            return;
                        }

                        $newTask = $this->createRepeatedTask($task, $nextAt);

                        $task->forceFill([
                            'next_repeat_at' => $this->nextDateByRepeat($nextAt, $task->repeat),
                            'last_repeated_at' => $now,
                        ])->save();

                        $this->notifyAssignedEmployees($newTask, 'repeat');
                    });
                }
            });
    }

    protected function createRepeatedTask(PersonalTask $task, Carbon $nextAt): PersonalTask
    {
        $newTask = $task->replicate([
            'task_id',
            'task_status',
            'progress',
            'reminder_count',
            'last_reminded_at',
            'last_repeated_at',
            'created_at',
            'updated_at',
            'deleted_at',
            'archived_at',
        ]);

        $newTask->task_status = 'open';
        $newTask->progress = 0;
        $newTask->due_date = $nextAt->toDateString();
        $newTask->due_time = $nextAt->format('H:i:s');
        $newTask->repeat_parent_id = $task->repeat_parent_id ?: $task->id;

        if ($task->reminder_date && $task->reminder_time) {
            $oldReminder = $this->buildDateTime($task->reminder_date, $task->reminder_time);
            $diffMinutes = $oldReminder && $task->due_date
                ? $oldReminder->diffInMinutes($this->buildDateTime($task->due_date, $task->due_time ?: '09:00'), false)
                : 0;

            $newReminder = $nextAt->copy()->addMinutes($diffMinutes);

            $newTask->reminder_date = $newReminder->toDateString();
            $newTask->reminder_time = $newReminder->format('H:i:s');
            $newTask->next_reminder_at = $newReminder;
        }

        $newTask->save();

        $pivot = $task->employees()
            ->get()
            ->mapWithKeys(fn($employee) => [
                $employee->id => [
                    'status' => 'accepted',
                    'change_date' => now()->toDateString(),
                ],
            ])
            ->toArray();

        $newTask->employees()->sync($pivot);

        foreach ($task->keys as $key) {
            $newKey = $key->replicate([
                'is_completed',
                'done_by',
                'done_date',
                'done_status',
                'work_progress',
                'submit_time',
                'reason',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

            $newKey->personal_task_id = $newTask->id;
            $newKey->is_completed = 0;
            $newKey->done_by = null;
            $newKey->done_date = null;
            $newKey->done_status = null;
            $newKey->work_progress = 0;
            $newKey->submit_time = null;
            $newKey->reason = null;
            $newKey->save();
        }

        return $newTask->load('employees');
    }

    protected function notifyAssignedEmployees(PersonalTask $task, string $type): void
    {
        $employeeIds = $task->employees()->pluck('employees.id')->unique()->values();

        $users = \App\Models\User::whereIn(
            'name',
            $employeeIds->map(fn($id) => (string) $id)->all()
        )->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new PersonalTaskReminderNotification($task, $type));
        }

        foreach ($employeeIds as $employeeId) {
            broadcast(new PersonalTaskReminderTriggered($task, (int) $employeeId, $type));
        }

        Log::info('[PERSONAL_TASK_SCHEDULER] Notification sent', [
            'task_id' => $task->id,
            'type' => $type,
            'employee_ids' => $employeeIds->all(),
        ]);
    }

    protected function buildDateTime($date, $time): ?Carbon
    {
        if (!$date) {
            return null;
        }

        $dateValue = $date instanceof Carbon
            ? $date->toDateString()
            : Carbon::parse($date)->toDateString();

        $timeValue = $time ?: '09:00:00';

        return Carbon::parse($dateValue . ' ' . $timeValue);
    }

    protected function nextDateByRepeat(Carbon $date, ?string $repeat): ?Carbon
    {
        $repeat = strtolower(trim((string) $repeat));

        return match ($repeat) {
            'daily', 'day', 'täglich', 'taeglich' => $date->copy()->addDay(),
            'weekly', 'week', 'wöchentlich', 'woechentlich' => $date->copy()->addWeek(),
            'monthly', 'month', 'monatlich' => $date->copy()->addMonthNoOverflow(),
            'yearly', 'year', 'jährlich', 'jaehrlich' => $date->copy()->addYear(),
            default => null,
        };
    }
}