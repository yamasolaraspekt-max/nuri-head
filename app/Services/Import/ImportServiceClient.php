<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Client für den Python-Import-Microservice (ezdxf/PyMuPDF). Ist `services.import.url`
 * leer, gilt der Service als inaktiv und alle Import-Features sind graceful aus.
 */
class ImportServiceClient
{
    public function aktiv(): bool
    {
        return filled(config('services.import.url'));
    }

    /**
     * Extrahiert die rohe Kandidaten-Geometrie aus einer DXF-Datei (Storage-Pfad).
     *
     * @return array<string, mixed>
     */
    public function extractDxf(string $pfad): array
    {
        return $this->extrahiere($pfad, '/extract/dxf');
    }

    /**
     * Extrahiert die rohe Vektor-Geometrie aus einer PDF-Datei (Storage-Pfad).
     * Maßstab bleibt unbekannt (PDF-Punkte) — Kalibrierung folgt im Editor (A-3c).
     *
     * @return array<string, mixed>
     */
    public function extractPdf(string $pfad): array
    {
        return $this->extrahiere($pfad, '/extract/pdf');
    }

    /**
     * Rendert die größte Seite eines (Raster-)PDFs zu einem PNG (A-3d-2). Liefert die PNG-Bytes
     * plus Metadaten (Maße, Quellseite, effektive DPI). Bei 422 (z.B. passwortgeschützt) wird die
     * Klartext-Meldung des Service durchgereicht — kein Stacktrace.
     *
     * @return array{png: string, breite: int, hoehe: int, quelle_seite: int, seiten_gesamt: int, effektive_dpi: int}
     */
    public function rasterizePdf(string $pfad): array
    {
        $url = rtrim((string) config('services.import.url'), '/').'/rasterize/pdf';

        $response = Http::timeout(120)
            ->attach('datei', (string) Storage::get($pfad), basename($pfad))
            ->post($url);

        if ($response->failed()) {
            throw new RuntimeException((string) ($response->json('detail') ?? 'Import-Service-Fehler ('.$response->status().')'));
        }

        return [
            'png' => $response->body(),
            'breite' => (int) $response->header('X-Breite'),
            'hoehe' => (int) $response->header('X-Hoehe'),
            'quelle_seite' => (int) $response->header('X-Quelle-Seite'),
            'seiten_gesamt' => (int) $response->header('X-Seiten-Gesamt'),
            'effektive_dpi' => (int) $response->header('X-Effektive-DPI'),
        ];
    }

    /**
     * OCR eines Underlay-Bildes (A-3d-2). Roh-Aufruf — liefert `{texte, ocr_verfuegbar}`.
     *
     * @return array{texte: list<array<string, mixed>>, ocr_verfuegbar: bool}
     */
    public function ocr(string $pfad): array
    {
        $url = rtrim((string) config('services.import.url'), '/').'/ocr';

        $response = Http::timeout(120)
            ->attach('datei', (string) Storage::get($pfad), basename($pfad))
            ->post($url);

        if ($response->failed()) {
            throw new RuntimeException('Import-Service-Fehler ('.$response->status().')');
        }

        return ['texte' => $response->json('texte', []), 'ocr_verfuegbar' => (bool) $response->json('ocr_verfuegbar', false)];
    }

    /**
     * Graceful-off-Wrapper: liefert OCR-`texte[]` oder `[]`, ohne je zu werfen. Ist der Service inaktiv,
     * tesseract nicht installiert oder der Aufruf fehlerhaft, bleibt der Underlay-Pfad voll nutzbar;
     * fehlendes OCR wird einmalig (Cache-guarded) als Info geloggt, nie als Nutzerfehler.
     *
     * @return list<array<string, mixed>>
     */
    public function ocrTexte(string $pfad): array
    {
        if (! $this->aktiv()) {
            return [];
        }

        try {
            $r = $this->ocr($pfad);
        } catch (\Throwable $e) {
            return [];
        }

        if (! $r['ocr_verfuegbar']) {
            if (Cache::add('import_ocr_unavailable_logged', true, now()->addHours(6))) {
                Log::info('OCR nicht verfügbar (tesseract fehlt im Import-Service) — Underlay funktioniert, nur keine Text-Vorschläge.');
            }

            return [];
        }

        return $r['texte'];
    }

    /**
     * @return array<string, mixed>
     */
    private function extrahiere(string $pfad, string $endpoint): array
    {
        $url = rtrim((string) config('services.import.url'), '/').$endpoint;

        $response = Http::timeout(60)
            ->attach('datei', (string) Storage::get($pfad), basename($pfad))
            ->post($url);

        if ($response->failed()) {
            throw new RuntimeException('Import-Service-Fehler ('.$response->status().'): '.$response->body());
        }

        return $response->json() ?? [];
    }
}
