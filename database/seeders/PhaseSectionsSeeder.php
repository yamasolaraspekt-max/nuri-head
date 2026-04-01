<?php
use Illuminate\Database\Seeder;
use App\Models\PhaseSection;

class PhaseSectionsSeeder extends Seeder
{
    public function run()
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

        foreach ($defaultSections as $section) {
            PhaseSection::create([
                'product_id' => 0, // Default product_id; to be updated dynamically later
                'phase_section' => $section,
                'status' => 'pending', // You can set a default status
            ]);
        }
    }
}
