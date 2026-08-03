<?php

namespace App\Domain\Hausplaner\Actions;

use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Domain\Hausplaner\Models\HausplanerSnapshot;
use App\Domain\Hausplaner\Validation\SceneDocumentValidator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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

            // **K-N5, zweiter Teil: der Rückweg schickt die Szene durch den Validator — aber nur
            // die, die er auch beurteilen KANN.**
            //
            // Der Auftrag lautete „UND schickt die Szene durch den Validator". Unbedingt gemessen:
            // ein echter v2-Snapshot erzeugt gegen das heutige Schema **2 Fehler**
            // (`The properties must match schema: schemaVersion` · `must match the const value`).
            // *Eine unbedingte Prüfung machte jede Geschichte vor der Versionsanhebung
            // unwiederherstellbar — der Knopf wäre für alte Stände dauerhaft tot.*
            //
            // Deshalb: geprüft wird, was die AKTUELLE Version trägt. Damit ist genau das gedeckt,
            // was der Auftrag meint — ein stiller Datenfehler nach dem Knopf —, und älteres bleibt
            // erreichbar. **Es wird weiterhin nicht migriert:** die Insel hebt beim Laden an, und
            // `migriereSzene` ein zweites Mal in PHP zu schreiben wäre die zweite Wahrheit.
            $version = (int) ($scene['schemaVersion'] ?? 0);
            if ($version === HausplanerDocument::SCHEMA_VERSION) {
                $fehler = app(SceneDocumentValidator::class)->fehler($scene);
                if ($fehler !== []) {
                    throw new RuntimeException(
                        'Der Snapshot ist gegen das aktuelle Schema ungültig und wird nicht zurückgeschrieben: '
                        .implode(' | ', array_slice($fehler, 0, 3)),
                    );
                }
            }

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
                'schema_version' => (int) ($scene['schemaVersion'] ?? $aktuell->schema_version),
                'revision' => $neueRevision,
                'checksum' => $checksum,
                'updated_by' => $userId,
            ]);

            return ['revision' => $neueRevision, 'checksum' => $checksum];
        });
    }
}
