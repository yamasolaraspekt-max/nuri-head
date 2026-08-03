<?php

namespace App\Domain\Hausplaner\Actions;

use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Domain\Hausplaner\Models\HausplanerSnapshot;
use Illuminate\Support\Facades\DB;

/**
 * Hausplaner P0 — Snapshot wiederherstellen, append-only:
 * 1) aktueller Stand wird VOR der Wiederherstellung selbst als Snapshot gesichert
 *    (reason 'vor_wiederherstellung') — nichts geht verloren,
 * 2) Snapshot-Szene wird zum neuen Stand mit revision+1 (Historie läuft weiter, nie zurück).
 */
class StelleSnapshotWieder
{
    /** @return array{revision: int, checksum: string} */
    public function ausfuehren(HausplanerDocument $dokument, HausplanerSnapshot $snapshot, ?int $userId): array
    {
        return DB::transaction(function () use ($dokument, $snapshot, $userId) {
            /** @var HausplanerDocument $aktuell */
            $aktuell = HausplanerDocument::query()->whereKey($dokument->getKey())->lockForUpdate()->firstOrFail();

            HausplanerSnapshot::query()->create([
                'hausplaner_document_id' => $aktuell->id,
                'revision' => $aktuell->revision,
                'scene_json' => $aktuell->scene_json,
                'label' => null,
                'reason' => 'vor_wiederherstellung',
                'created_by' => $userId,
            ]);

            $scene = $snapshot->scene_json;
            $neueRevision = (int) $aktuell->revision + 1;
            $scene['revision'] = $neueRevision;
            $checksum = SpeichereHausplanerDokument::checksum($scene);

            $aktuell->update([
                'scene_json' => $scene,
                // **Die Spalte folgt der Szene — auch auf dem RÜCKWEG** (Z-06-N1, vierte Naht,
                // gefunden vom Evaluator). Der Hinweg stellt diese Kopplung ausdrücklich her
                // (`SpeichereHausplanerDokument:39`); ohne dieselbe Zeile hier bricht der Rückweg
                // sie: ein zurückgeholter v2-Snapshot landete in einem Dokument, dessen Spalte
                // weiter 3 sagt — und `objekt.blade.php` zeigte dem Nutzer „Schema v3" über
                // einem v2-Inhalt. *Eine Anzeige, die lügt, ist schlimmer als keine.*
                //
                // **Bewusst NICHT validiert und NICHT migriert.** Ein Snapshot wurde geprüft, als
                // er entstand — gegen das Schema SEINER Zeit. Ihn heute gegen das aktuelle Schema
                // zu prüfen hiesse, gültige Geschichte abzulehnen; ihn hier zu migrieren hiesse,
                // `migriereSzene` ein zweites Mal in PHP zu schreiben. *Zwei Wahrheiten über
                // dieselbe Migration sind teurer als der eine Ladeschritt, der ohnehin läuft:*
                // die Insel hebt beim Laden an, die nächste Speicherung schreibt v3.
                'schema_version' => (int) ($scene['schemaVersion'] ?? $aktuell->schema_version),
                'revision' => $neueRevision,
                'checksum' => $checksum,
                'updated_by' => $userId,
            ]);

            return ['revision' => $neueRevision, 'checksum' => $checksum];
        });
    }
}
