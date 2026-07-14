<?php

namespace Tests\Feature\Geometrie;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * G0b / AP-4b — Architektur-Wächter: der Pflichtdurchgang wird GETESTET, nicht behauptet.
 * Schlägt fehl, wenn ein neuer Geometrie-Schreibpfad ohne Gate entsteht oder ein neuer Aufrufer
 * der Flächenfunktionen außerhalb der bekannten Allowlist auftaucht.
 */
class GeometrieSchreibpfadWaechterTest extends TestCase
{
    /** Geometrie-persistierende Dateien (RaumGeometrie::updateOrCreate) — erwartet: nur der Ingestion-Punkt. */
    public function test_nur_ingestion_punkt_persistiert_geometrie(): void
    {
        $persistierer = $this->dateienMit('RaumGeometrie::updateOrCreate');

        $this->assertEqualsCanonicalizing(
            ['GrundrissController.php'],
            $persistierer,
            'Neuer Geometrie-Schreibpfad ohne Gate? RaumGeometrie::updateOrCreate darf nur im Ingestion-Punkt stehen.'
        );

        // ... und dieser Schreibpfad geht durch das Gate.
        $controller = File::get(app_path('Http/Controllers/Energie/GrundrissController.php'));
        $this->assertStringContainsString('gatePruefung', $controller);
        $this->assertStringContainsString('TopologieGate', $controller);
    }

    /** Aufrufer der Flächenfunktionen müssen in der bekannten Allowlist bleiben. */
    public function test_flaechenfunktionen_nur_bekannte_aufrufer(): void
    {
        $allowlist = [
            'GeometrieAbleitungService.php', // Mirror (Definition + interner Aufruf)
            'RaumHuelleService.php',         // Mirror (Definition + interner Aufruf)
            'HeizlastRechner.php',           // Mirror-Aufrufer von effektiveBauteile
            'GrundrissController.php',       // Ingestion-Punkt
            'TopologieGate.php',             // Berechnungsfassade
        ];

        foreach (['->polygonFlaecheM2(', '->effektiveBauteile('] as $call) {
            foreach ($this->dateienMit($call) as $datei) {
                $this->assertContains($datei, $allowlist, "Unbekannter Aufrufer von {$call}: {$datei} — Gate-Pflichtdurchgang prüfen.");
            }
        }
    }

    /** @return array<int,string> Basenamen aller .php-Dateien unter app/, die $nadel enthalten. */
    private function dateienMit(string $nadel): array
    {
        return collect(File::allFiles(app_path()))
            ->filter(fn ($f) => $f->getExtension() === 'php')
            ->filter(fn ($f) => str_contains(File::get($f->getRealPath()), $nadel))
            ->map(fn ($f) => $f->getFilename())
            ->unique()
            ->values()
            ->all();
    }
}
