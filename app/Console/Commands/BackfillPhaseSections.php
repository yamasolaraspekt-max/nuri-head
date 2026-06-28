<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleGroup;
use App\Models\PhaseSection;
use Illuminate\Support\Facades\DB;

class BackfillPhaseSections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backfill:phase-sections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill phase_sections table with default rows for existing article groups';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $defaultSections = [
            'complete',
            'montage',
            'product',
            'plan',
            'maintenance',
            'repair',
            'others',
        ];

        $articleGroups = ArticleGroup::all();

        foreach ($articleGroups as $group) {
            foreach ($defaultSections as $section) {
                PhaseSection::firstOrCreate(
                    [
                        'product_id' => $group->id,
                        'phase_section' => $section,
                    ],
                    [
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->info('Phase sections backfilled for all existing article groups.');

        return 0;
    }
}
