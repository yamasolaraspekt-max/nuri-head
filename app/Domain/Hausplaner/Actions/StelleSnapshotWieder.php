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
                'revision' => $neueRevision,
                'checksum' => $checksum,
                'updated_by' => $userId,
            ]);

            return ['revision' => $neueRevision, 'checksum' => $checksum];
        });
    }
}
