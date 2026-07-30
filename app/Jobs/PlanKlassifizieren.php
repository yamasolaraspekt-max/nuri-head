<?php

namespace App\Jobs;

use App\Models\PlanUpload;
use App\Services\Import\DateiSignatur;
use App\Services\Import\ImportServiceClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Grobe Klassifikation eines hochgeladenen Plans (A-3a): Typ aus Endung/MIME +
 * einfache Magic-Bytes-Prüfung. Bei aktivem Import-Service wird für DXF/PDF direkt
 * die Vektor-Extraktion angestoßen; ist der Service inaktiv (IMPORT_SERVICE_URL leer),
 * bleibt es graceful bei status='klassifiziert'.
 *
 * PORT-HINWEIS: In wberechnung stieß dieser Job eigene Folge-Jobs an
 * (PlanVektorExtrahieren/PlanPdfExtrahieren/PlanBildVermessen). Diese sind im ticket
 * NICHT portiert (nicht im Scope des Plan-Import-Tracks). Statt der fehlenden Klassen
 * wird die Extraktion — sofern der Service aktiv ist — inline über den
 * ImportServiceClient ausgeführt; die reine Bild-Vermessung (GD) entfällt graceful.
 */
class PlanKlassifizieren implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlanUpload $planUpload) {}

    public function handle(ImportServiceClient $importService): void
    {
        $upload = $this->planUpload;
        $endung = strtolower(pathinfo($upload->original_name, PATHINFO_EXTENSION));

        $typ = match ($endung) {
            'dwg' => 'dwg',
            'dxf' => 'dxf',
            'pdf' => 'pdf',
            'png', 'jpg', 'jpeg', 'tif', 'tiff' => 'bild',
            default => null,
        };

        if ($typ === null) {
            $upload->update(['status' => 'fehler', 'meta' => ['fehler' => "Unbekannter Dateityp: .{$endung}"]]);

            return;
        }

        $upload->update([
            'typ' => $typ,
            'status' => 'klassifiziert',
            'meta' => ['endung' => $endung, 'magic' => $this->magicHinweis($upload->pfad)],
        ]);

        // Import-Service inaktiv (IMPORT_SERVICE_URL leer) → graceful aus: nur klassifiziert.
        if (! $importService->aktiv()) {
            return;
        }

        // DXF/PDF: Vektor-Extraktion inline (Folge-Jobs aus wberechnung sind nicht portiert).
        if ($typ === 'dxf' || $typ === 'pdf') {
            try {
                $geometrie = $typ === 'dxf'
                    ? $importService->extractDxf($upload->pfad)
                    : $importService->extractPdf($upload->pfad);

                $upload->update([
                    'status' => 'verarbeitet',
                    'kandidat_geometrie' => $geometrie,
                ]);
            } catch (Throwable $e) {
                $upload->update([
                    'status' => 'fehler',
                    'meta' => array_merge((array) $upload->meta, ['fehler' => $e->getMessage()]),
                ]);
            }
        }

        // AUF-88-P1 / K-03 — PDF als Referenzunterlage: `PlanUploadController::bild()` liest für
        // `typ !== 'bild'` bereits `meta.bild_pfad`; bislang setzte ihn niemand. Eigener try/catch,
        // getrennt von der Vektor-Extraktion oben — misslingt die Rasterung, bleibt die
        // Kandidaten-Geometrie trotzdem stehen, und umgekehrt.
        if ($typ === 'pdf') {
            try {
                $raster = $importService->rasterizePdf($upload->pfad);
                $bildPfad = 'plan-uploads/raster/'.$upload->id.'.png';
                Storage::put($bildPfad, $raster['png']);
                $upload->update([
                    'meta' => array_merge((array) $upload->meta, [
                        'bild_pfad' => $bildPfad,
                        'bild_breite' => $raster['breite'],
                        'bild_hoehe' => $raster['hoehe'],
                        'bild_quelle_seite' => $raster['quelle_seite'],
                        'bild_seiten_gesamt' => $raster['seiten_gesamt'],
                    ]),
                ]);
            } catch (Throwable $e) {
                $upload->update([
                    'meta' => array_merge((array) $upload->meta, ['rasterung_fehler' => $e->getMessage()]),
                ]);
            }
        }

        // typ === 'bild': Rasterbild-Vermessung (reines GD) ist im ticket nicht portiert →
        // bleibt graceful bei status='klassifiziert'.
    }

    /**
     * Liest die ersten Bytes und liefert einen (nicht autoritativen) Magic-Hinweis.
     *
     * AUF-88-P1: die Erkennung selbst liegt jetzt in `DateiSignatur` — eine Wahrheit mit der
     * Prüfung, die `PlanUploadController::store()` VOR dem Speichern fährt.
     */
    private function magicHinweis(string $pfad): ?string
    {
        if (! Storage::exists($pfad)) {
            return null;
        }

        return DateiSignatur::erkenne(substr((string) Storage::get($pfad), 0, 8));
    }
}
