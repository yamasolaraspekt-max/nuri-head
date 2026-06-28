<?php

namespace App\Jobs;

use App\Models\WpFusionFormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFusionEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $entry;

    public function __construct(array $entry)
    {
        $this->entry = $entry;
    }

    public function handle()
    {
        WpFusionFormEntry::updateOrCreate(
            [
                'submission_id' => $this->entry['submission_id'],
                'field_id' => $this->entry['field_id'],
            ],
            [
                'form_id' => $this->entry['form_id'],
                'value' => $this->entry['value'] ?? null,
                'privacy' => $this->entry['privacy'] ?? null,
                'data' => is_array($this->entry['data']) ? json_encode($this->entry['data']) : $this->entry['data'],
            ]
        );
    }
}
