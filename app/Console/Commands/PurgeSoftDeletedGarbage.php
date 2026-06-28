<?php

namespace App\Console\Commands;

use App\Services\SoftDeletedGarbageCollector;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeSoftDeletedGarbage extends Command
{
    protected $signature = 'garbage:soft-deleted
        {--months=2 : Delete records soft-deleted older than this many months}
        {--all : Delete all soft-deleted records, ignoring age}
        {--dry-run : Show what would be deleted without deleting}
        {--table=* : Optional specific table name, can be used multiple times}';

    protected $description = 'Permanently delete soft-deleted records from all tables with deleted_at.';

    public function handle(SoftDeletedGarbageCollector $collector): int
    {
        $all = (bool) $this->option('all');
        $dryRun = (bool) $this->option('dry-run');
        $months = (int) $this->option('months');
        $tables = $this->option('table') ?? [];

        $olderThan = $all ? null : now()->subMonths($months);

        if ($all && !$dryRun) {
            if (!$this->confirm('This will permanently delete ALL soft-deleted records from every table with deleted_at. Continue?')) {
                $this->warn('Cancelled.');
                return self::SUCCESS;
            }
        }

        $result = $collector->purge(
            olderThan: $olderThan,
            all: $all,
            dryRun: $dryRun,
            onlyTables: $tables
        );

        $rows = [];

        foreach ($result['tables'] as $table => $count) {
            $rows[] = [$table, $count];
        }

        $this->table(['Table', $dryRun ? 'Would delete' : 'Deleted'], $rows);

        if (!empty($result['errors'])) {
            $this->error('Some tables could not be cleaned:');

            foreach ($result['errors'] as $table => $error) {
                $this->line("{$table}: {$error}");
            }
        }

        $this->info(($dryRun ? 'Total would delete: ' : 'Total deleted: ') . $result['total']);

        return self::SUCCESS;
    }
}