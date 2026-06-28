<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobRepresentative;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateJobRepresentativeStatus extends Command
{
    protected $signature = 'job_representatives:update-status';
    protected $description = 'Update the status of job representatives where the end date has passed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get the current date
        $currentDate = Carbon::now();

        // Find all job representatives where the end date has passed and status is not updated
        $jobRepresentatives = JobRepresentative::where('end_date', '<', $currentDate)
                            ->where('status', '!=', 'the employee is back to work')
                            ->get();

        foreach ($jobRepresentatives as $repre) {
            // Update the current_representer and status
            $repre->current_representer = $repre->employee_id;
            $repre->status = 'the employee is back to work';
            $repre->save();

            $this->info('Updated job representative ID ' . $repre->id . ' to status "the employee is back to work".');
        }

        $this->info('Job representative status update process completed.');
    }
}
