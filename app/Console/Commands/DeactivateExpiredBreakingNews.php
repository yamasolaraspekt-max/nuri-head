<?php

namespace App\Console\Commands;

use App\Models\BreakingNews;
use Illuminate\Console\Command;

class DeactivateExpiredBreakingNews extends Command
{
    protected $signature = 'breaking-news:deactivate-expired';
    protected $description = 'Automatically deactivate breaking news whose end date has passed';

    public function handle(): int
    {
        $now = now();

        $count = BreakingNews::where('is_active', true)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->update(['is_active' => false]);

        $this->info("Deactivated {$count} expired breaking news items.");

        return Command::SUCCESS;
    }
}
