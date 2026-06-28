<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Leave;
use Carbon\Carbon;

class UpdateLeaveStatus extends Command
{
    protected $signature = 'leaves:update-status';
    protected $description = 'Update the status of leaves based on their end date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get the current date
        $currentDate = Carbon::now();

        // Find all leaves
        $leaves = Leave::all();

        foreach ($leaves as $leave) {
            $endDate = Carbon::parse($leave->end_date);

            if ($endDate < $currentDate) {
                // If the leave end date has passed, update the status to "the leave is finished, report to station"
                $leave->status = 'the leave is finished, report to station';
            } else {
                // Calculate the remaining days
                $remainingDays = $endDate->diffInDays($currentDate);

                // Update the status to show the remaining days
                $leave->status = "leave ends in {$remainingDays} days";
            }

            $leave->save();

            $this->info('Updated leave ID ' . $leave->id . ' to status "' . $leave->status . '".');
        }

        $this->info('Leave status update process completed.');
    }
}
