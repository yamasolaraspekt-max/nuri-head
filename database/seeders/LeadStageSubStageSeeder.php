<?php

namespace Database\Seeders;

use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadStageSubStageSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $stages = [
                [
                    'key' => 'lead',
                    'name' => 'Lead',
                    'color' => '#74b2d4',
                    'icon' => 'user-plus',
                    'sort_order' => 10,
                    'is_default' => true,
                    'is_protected' => true,
                    'is_closed' => false,
                    'sub_stages' => [
                        ['name' => 'Neu eingegangen', 'icon' => 'sparkles', 'color' => '#74b2d4', 'default' => true],
                        ['name' => 'Erstkontakt offen', 'icon' => 'phone', 'color' => '#0ea5e9'],
                        ['name' => 'Kunde nicht erreicht', 'icon' => 'phone-off', 'color' => '#f59e0b'],
                        ['name' => 'Rückruf vereinbart', 'icon' => 'calendar-clock', 'color' => '#8b5cf6'],
                        ['name' => 'Daten unvollständig', 'icon' => 'alert-circle', 'color' => '#ef4444'],
                        ['name' => 'Qualifiziert', 'icon' => 'check-circle', 'color' => '#22c55e'],
                    ],
                ],

                [
                    'key' => 'offer',
                    'name' => 'Angebot',
                    'color' => '#93c21c',
                    'icon' => 'file-text',
                    'sort_order' => 20,
                    'is_default' => false,
                    'is_protected' => true,
                    'is_closed' => false,
                    'sub_stages' => [
                        ['name' => 'Angebot in Vorbereitung', 'icon' => 'edit-3', 'color' => '#84cc16', 'default' => true],
                        ['name' => 'Technische Prüfung', 'icon' => 'search-check', 'color' => '#06b6d4'],
                        ['name' => 'Kalkulation offen', 'icon' => 'calculator', 'color' => '#f59e0b'],
                        ['name' => 'Angebot in Erstellung', 'icon' => 'file-edit', 'color' => '#3b82f6'],
                        ['name' => 'Angebot intern geprüft', 'icon' => 'shield-check', 'color' => '#14b8a6'],
                        ['name' => 'Angebot versendet', 'icon' => 'send', 'color' => '#22c55e'],
                        ['name' => 'Rückfrage vom Kunden', 'icon' => 'message-circle-question', 'color' => '#f97316'],
                        ['name' => 'Nachbearbeitung', 'icon' => 'refresh-cw', 'color' => '#8b5cf6'],
                        ['name' => 'Warten auf Entscheidung', 'icon' => 'hourglass', 'color' => '#64748b'],
                        ['name' => 'Angebot angenommen', 'icon' => 'thumbs-up', 'color' => '#16a34a'],
                        ['name' => 'Angebot abgelehnt', 'icon' => 'thumbs-down', 'color' => '#dc2626'],
                    ],
                ],

                [
                    'key' => 'follow_up',
                    'name' => 'Nachfassen',
                    'color' => '#f3c12f',
                    'icon' => 'phone-call',
                    'sort_order' => 30,
                    'is_default' => false,
                    'is_protected' => false,
                    'is_closed' => false,
                    'sub_stages' => [
                        ['name' => 'Nachfassen geplant', 'icon' => 'calendar-plus', 'color' => '#f59e0b', 'default' => true],
                        ['name' => 'Erste Erinnerung gesendet', 'icon' => 'bell', 'color' => '#f97316'],
                        ['name' => 'Telefonisch nachgefasst', 'icon' => 'phone-call', 'color' => '#0ea5e9'],
                        ['name' => 'Kunde überlegt noch', 'icon' => 'hourglass', 'color' => '#64748b'],
                        ['name' => 'Entscheidung erwartet', 'icon' => 'clock', 'color' => '#8b5cf6'],
                        ['name' => 'Nicht erreichbar', 'icon' => 'phone-off', 'color' => '#ef4444'],
                    ],
                ],

                [
                    'key' => 'accepted',
                    'name' => 'Angenommen',
                    'color' => '#22c55e',
                    'icon' => 'check-circle',
                    'sort_order' => 40,
                    'is_default' => false,
                    'is_protected' => false,
                    'is_closed' => false,
                    'sub_stages' => [
                        ['name' => 'Zusage erhalten', 'icon' => 'check', 'color' => '#22c55e', 'default' => true],
                        ['name' => 'Unterlagen anfordern', 'icon' => 'folder-plus', 'color' => '#0ea5e9'],
                        ['name' => 'Unterlagen vollständig', 'icon' => 'folder-check', 'color' => '#16a34a'],
                        ['name' => 'Vertrag vorbereiten', 'icon' => 'file-signature', 'color' => '#84cc16'],
                        ['name' => 'Übergabe an Auftrag', 'icon' => 'arrow-right-circle', 'color' => '#14b8a6'],
                    ],
                ],

                [
                    'key' => 'deal',
                    'name' => 'Auftrag',
                    'color' => '#0ea5e9',
                    'icon' => 'briefcase',
                    'sort_order' => 50,
                    'is_default' => false,
                    'is_protected' => true,
                    'is_closed' => false,
                    'sub_stages' => [
                        ['name' => 'Auftrag erhalten', 'icon' => 'inbox', 'color' => '#0ea5e9', 'default' => true],
                        ['name' => 'Auftragsprüfung', 'icon' => 'search-check', 'color' => '#3b82f6'],
                        ['name' => 'Vertrag erstellt', 'icon' => 'file-text', 'color' => '#8b5cf6'],
                        ['name' => 'Vertrag versendet', 'icon' => 'send', 'color' => '#06b6d4'],
                        ['name' => 'Vertrag unterschrieben', 'icon' => 'signature', 'color' => '#22c55e'],
                        ['name' => 'Anzahlung offen', 'icon' => 'wallet', 'color' => '#f59e0b'],
                        ['name' => 'Anzahlung erhalten', 'icon' => 'badge-euro', 'color' => '#16a34a'],
                        ['name' => 'Materialplanung', 'icon' => 'package-search', 'color' => '#64748b'],
                        ['name' => 'Material bestellt', 'icon' => 'shopping-cart', 'color' => '#3b82f6'],
                        ['name' => 'Material vollständig', 'icon' => 'package-check', 'color' => '#22c55e'],
                    ],
                ],

                [
                    'key' => 'project',
                    'name' => 'Montage',
                    'color' => '#8b5cf6',
                    'icon' => 'wrench',
                    'sort_order' => 60,
                    'is_default' => false,
                    'is_protected' => false,
                    'is_closed' => false,
                    'sub_stages' => [
                        ['name' => 'Montageplanung', 'icon' => 'calendar-days', 'color' => '#8b5cf6', 'default' => true],
                        ['name' => 'Montagetermin offen', 'icon' => 'calendar-plus', 'color' => '#f59e0b'],
                        ['name' => 'Montagetermin bestätigt', 'icon' => 'calendar-check', 'color' => '#22c55e'],
                        ['name' => 'Vorbereitung läuft', 'icon' => 'settings', 'color' => '#0ea5e9'],
                        ['name' => 'In Ausführung', 'icon' => 'hammer', 'color' => '#3b82f6'],
                        ['name' => 'Teilweise abgeschlossen', 'icon' => 'circle-dot', 'color' => '#f97316'],
                        ['name' => 'Montage abgeschlossen', 'icon' => 'check-circle-2', 'color' => '#16a34a'],
                        ['name' => 'Mangel / Problem', 'icon' => 'alert-triangle', 'color' => '#dc2626'],
                        ['name' => 'Nacharbeit geplant', 'icon' => 'refresh-cw', 'color' => '#f59e0b'],
                    ],
                ],

                [
                    'key' => 'completed',
                    'name' => 'Abschluss',
                    'color' => '#16a34a',
                    'icon' => 'flag',
                    'sort_order' => 70,
                    'is_default' => false,
                    'is_protected' => false,
                    'is_closed' => true,
                    'sub_stages' => [
                        ['name' => 'Abnahme offen', 'icon' => 'clipboard-check', 'color' => '#f59e0b', 'default' => true],
                        ['name' => 'Abnahme durchgeführt', 'icon' => 'check-square', 'color' => '#22c55e'],
                        ['name' => 'Rechnung erstellt', 'icon' => 'receipt', 'color' => '#0ea5e9'],
                        ['name' => 'Rechnung versendet', 'icon' => 'send', 'color' => '#3b82f6'],
                        ['name' => 'Zahlung offen', 'icon' => 'wallet', 'color' => '#f59e0b'],
                        ['name' => 'Bezahlt', 'icon' => 'badge-euro', 'color' => '#16a34a'],
                        ['name' => 'Projekt abgeschlossen', 'icon' => 'badge-check', 'color' => '#15803d'],
                    ],
                ],

                [
                    'key' => 'archive',
                    'name' => 'Archiv',
                    'color' => '#64748b',
                    'icon' => 'archive',
                    'sort_order' => 80,
                    'is_default' => false,
                    'is_protected' => true,
                    'is_closed' => true,
                    'sub_stages' => [
                        ['name' => 'Archiviert', 'icon' => 'archive', 'color' => '#64748b', 'default' => true],
                        ['name' => 'Später interessant', 'icon' => 'clock', 'color' => '#8b5cf6'],
                        ['name' => 'Abgeschlossen archiviert', 'icon' => 'folder-check', 'color' => '#16a34a'],
                    ],
                ],

                [
                    'key' => 'junk',
                    'name' => 'Junk',
                    'color' => '#dc2626',
                    'icon' => 'trash-2',
                    'sort_order' => 90,
                    'is_default' => false,
                    'is_protected' => true,
                    'is_closed' => true,
                    'sub_stages' => [
                        ['name' => 'Duplikat', 'icon' => 'copy', 'color' => '#ef4444', 'default' => true],
                        ['name' => 'Falsche Anfrage', 'icon' => 'x-circle', 'color' => '#dc2626'],
                        ['name' => 'Kein Interesse', 'icon' => 'thumbs-down', 'color' => '#f97316'],
                        ['name' => 'Nicht qualifiziert', 'icon' => 'ban', 'color' => '#991b1b'],
                        ['name' => 'Spam', 'icon' => 'shield-x', 'color' => '#7f1d1d'],
                    ],
                ],
            ];

            foreach ($stages as $stageData) {
                $stage = LeadStage::withTrashed()->firstOrNew([
                    'key' => $stageData['key'],
                ]);

                $stage->fill([
                    'name' => $stageData['name'],
                    'color' => $stageData['color'],
                    'icon' => $stageData['icon'],
                    'sort_order' => $stageData['sort_order'],
                    'is_default' => $stageData['is_default'],
                    'is_protected' => $stageData['is_protected'],
                    'is_closed' => $stageData['is_closed'],
                    'is_active' => true,
                ]);

                if ($stage->trashed()) {
                    $stage->restore();
                }

                $stage->save();

                $hasDefault = false;

                foreach ($stageData['sub_stages'] as $index => $subStageData) {
                    $key = $this->makeKey($subStageData['name']);

                    $subStage = LeadStageSubStage::withTrashed()->firstOrNew([
                        'lead_stage_id' => $stage->id,
                        'key' => $key,
                    ]);

                    $isDefault = (bool) ($subStageData['default'] ?? false);

                    if ($isDefault) {
                        $hasDefault = true;

                        LeadStageSubStage::withTrashed()
                            ->where('lead_stage_id', $stage->id)
                            ->where('id', '!=', $subStage->id ?? 0)
                            ->update(['is_default' => false]);
                    }

                    $subStage->fill([
                        'lead_stage_id' => $stage->id,
                        'key' => $key,
                        'name' => $subStageData['name'],
                        'color' => $subStageData['color'] ?? $stage->color,
                        'icon' => $subStageData['icon'] ?? 'list',
                        'sort_order' => ($index + 1) * 10,
                        'is_default' => $isDefault,
                        'is_active' => true,
                    ]);

                    if ($subStage->trashed()) {
                        $subStage->restore();
                    }

                    $subStage->save();
                }

                if (!$hasDefault) {
                    $firstSubStage = LeadStageSubStage::query()
                        ->where('lead_stage_id', $stage->id)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->first();

                    if ($firstSubStage) {
                        LeadStageSubStage::query()
                            ->where('lead_stage_id', $stage->id)
                            ->update(['is_default' => false]);

                        $firstSubStage->update([
                            'is_default' => true,
                            'is_active' => true,
                        ]);
                    }
                }
            }
        });
    }

    private function makeKey(string $name): string
    {
        $key = Str::slug($name, '_');
        $key = strtolower(trim($key, '_'));

        return $key !== '' ? $key : 'unterphase';
    }
}