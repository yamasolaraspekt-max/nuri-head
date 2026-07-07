<?php

namespace App\Jobs;

use App\Models\PlanUpload;
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

        // typ === 'bild': Rasterbild-Vermessung (reines GD) ist im ticket nicht portiert →
        // bleibt graceful bei status='klassifiziert'.
    }

    /**
     * Liest die ersten Bytes und liefert einen (nicht autoritativen) Magic-Hinweis.
     */
    private function magicHinweis(string $pfad): ?string
    {
        if (! Storage::exists($pfad)) {
            return null;
        }
        $kopf = substr((string) Storage::get($pfad), 0, 8);

        return match (true) {
            str_starts_with($kopf, '%PDF') => 'pdf',
            str_starts_with($kopf, "\x89PNG") => 'png',
            str_starts_with($kopf, "\xFF\xD8\xFF") => 'jpg',
            str_starts_with($kopf, 'II*') || str_starts_with($kopf, "MM\x00*") => 'tiff',
            str_starts_with($kopf, 'AC10') => 'dwg',
            default => null,
        };
    }
}
