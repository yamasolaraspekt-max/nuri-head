<?php

namespace App\Domain\Hausplaner\Actions;

use App\Domain\Hausplaner\Models\HausplanerDocument;
use Illuminate\Support\Facades\DB;

/**
 * Hausplaner P0 — Speichern mit Revisionsschutz (Kante „409-Pfad").
 *
 * Semantik (Spec §1/§4): Client sendet base_revision. Stimmt sie nicht mit der
 * DB-Revision überein, wird NICHTS geschrieben (['ok' => false] + aktuelle Revision)
 * — der Controller antwortet 409, kein stiller Verlust. Prüfung + Schreiben laufen
 * in einer Transaktion mit Zeilensperre (zwei parallele PUTs können nicht beide gewinnen).
 */
class SpeichereHausplanerDokument
{
    /**
     * @param  array<string,mixed>  $scene
     * @return array{ok: bool, revision: int, checksum?: string}
     */
    public function ausfuehren(HausplanerDocument $dokument, int $baseRevision, array $scene, ?int $userId): array
    {
        return DB::transaction(function () use ($dokument, $baseRevision, $scene, $userId) {
            /** @var HausplanerDocument $aktuell */
            $aktuell = HausplanerDocument::query()->whereKey($dokument->getKey())->lockForUpdate()->firstOrFail();

            if ((int) $aktuell->revision !== $baseRevision) {
                return ['ok' => false, 'revision' => (int) $aktuell->revision];
            }

            $neueRevision = (int) $aktuell->revision + 1;
            $scene['revision'] = $neueRevision; // Szene spiegelt die Server-Revision

            $checksum = self::checksum($scene);

            $aktuell->update([
                'scene_json' => $scene,
                'schema_version' => (int) ($scene['schemaVersion'] ?? $aktuell->schema_version), // Spalte folgt der Szene (v1/v2)
                'revision' => $neueRevision,
                'checksum' => $checksum,
                'updated_by' => $userId,
            ]);

            return ['ok' => true, 'revision' => $neueRevision, 'checksum' => $checksum];
        });
    }

    /** Kanonische Prüfsumme: sha256 über json mit sortierten Schlüsseln (stabil für den Undo-Byte-Vergleich). */
    public static function checksum(array $scene): string
    {
        $kanonisch = self::sortiereRekursiv($scene);

        return hash('sha256', json_encode($kanonisch, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private static function sortiereRekursiv(mixed $wert): mixed
    {
        if (!is_array($wert)) {
            return $wert;
        }
        $istListe = array_is_list($wert);
        $ergebnis = [];
        foreach ($wert as $k => $v) {
            $ergebnis[$k] = self::sortiereRekursiv($v);
        }
        if (!$istListe) {
            ksort($ergebnis);
        }

        return $ergebnis;
    }
}
