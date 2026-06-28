<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FusionFormEntryJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->entries as $entry) {
            WpFusionFormEntry::updateOrCreate(
                ['submission_id' => $entry['submission_id'], 'field_id' => $entry['field_id']],
                ['form_id' => $entry['form_id'], 'value' => $entry['value'] ?? null]
            );
        }
    }
}
