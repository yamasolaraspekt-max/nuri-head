<?php

namespace Database\Seeders;

use App\Models\DashboardWidget;
use Illuminate\Database\Seeder;

class DashboardShortcutWidgetSeeder extends Seeder
{
    public function run(): void
    {
        DashboardWidget::updateOrCreate(
            ['key' => 'shortcuts'],
            [
                'title' => 'Schnellzugriffe',
                'subtitle' => 'Häufige Aktionen',
                'icon' => 'zap',
                'color' => 'green',
                'default_view' => 'personal',
                'tags' => ['today', 'shortcuts'],
                'default_col_span' => 4,
                'default_row_span' => 4,
                'component' => 'shortcuts',
                'allow_multiple' => false,
                'is_active' => true,
                'sort_order' => 20,
                'meta' => [],
            ]
        );
    }
}