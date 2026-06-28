<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected $commands = [
        \App\Console\Commands\SyncSolarNewsToChat::class,
        \App\Console\Commands\BackfillPhaseSections::class,

    ];
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('leaves:update-status')->hourly();
        // $schedule->command('job_representatives:update-status')->hourly(); 
        $schedule->command('chat:sync-solar-news')->everyFifteenMinutes();
        $schedule->command('breaking-news:deactivate-expired')->everyMinute();
        $schedule->command('lead-emails:sync')->everyMinute()->withoutOverlapping();
        $schedule->command('appointments:dispatch-reminders')->everyMinute();
        $schedule->command('personal-tasks:process-scheduler')
            ->everyMinute()
            ->withoutOverlapping();




    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php'); 
    }
}
