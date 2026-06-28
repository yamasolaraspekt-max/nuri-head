<?php

 namespace App\Jobs;

use App\Events\TaskReminderEvent;
use App\Models\PersonalTask;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleTaskReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $taskId;

    public function __construct(int $taskId)
    {
        $this->taskId = $taskId;
    }

    public function handle()
    {
        $task = PersonalTask::with('employees:id')->find($this->taskId);
        if (!$task) return;

        $employeeIds = $task->employees->pluck('id')->all();

        event(new TaskReminderEvent($task, $employeeIds));

        // simple repeat: if task is repeating, compute next due and schedule again
        if ($task->repeat && $task->repeat_interval) {
            $current = Carbon::parse($task->due_date.' '.$task->due_time);
            $next    = match ($task->repeat_interval) {
                'daily'     => $current->clone()->addDay(),
                'weekly'    => $current->clone()->addWeek(),
                'monthly'   => $current->clone()->addMonth(),
                'quarterly' => $current->clone()->addMonths(3),
                'yearly'    => $current->clone()->addYear(),
                default     => null,
            };

            if ($next) {
                $task->update([
                    'due_date' => $next->toDateString(),
                    'due_time' => $next->format('H:i:s'),
                ]);

                static::dispatch($task->id)
                    ->delay($next->copy()->subHours(4));
            }
        }
    }
}
