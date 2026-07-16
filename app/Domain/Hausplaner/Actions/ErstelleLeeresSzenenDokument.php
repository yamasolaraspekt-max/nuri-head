<?php

namespace App\Domain\Hausplaner\Actions;

use App\Domain\Hausplaner\Models\HausplanerDocument;
use Illuminate\Support\Str;

/**
 * Hausplaner (ticket) — legt für ein OBJEKT das (eine) Dokument mit leerer Standard-Szene an.
 * Anker ▲T1 = alternative_id. Das Szenen-Feld `projectId` (SceneDocument-Schema, number) trägt
 * bewusst den Objekt-Wert — so bleibt das Insel-Bundle (Zod-Validierung) unverändert.
 * Standard-Szene = Schema v1, mm, ein Geschoss „Erdgeschoss", Raster 100 mm, Snap an.
 */
class ErstelleLeeresSzenenDokument
{
    public function ausfuehren(int $alternativeId, ?int $userId): HausplanerDocument
    {
        $jetzt = now()->toIso8601String();

        $scene = [
            'id' => (string) Str::uuid(),
            'projectId' => $alternativeId, // Objekt-Anker im Szenen-Schema (number, Bundle unverändert)
            'schemaVersion' => 1,
            'revision' => 1,
            'units' => 'mm',
            'settings' => ['gridSize' => 100, 'snapEnabled' => true, 'angleSnap' => 15],
            'levels' => [[
                'id' => 'level-eg',
                'name' => 'Erdgeschoss',
                'elevation' => 0,
                'defaultWallHeight' => 2500,
                'floorThickness' => 200,
                'sortOrder' => 0,
            ]],
            'nodes' => [],
            'materials' => [],
            'metadata' => ['createdAt' => $jetzt, 'updatedAt' => $jetzt],
        ];

        return HausplanerDocument::query()->create([
            'alternative_id' => $alternativeId,
            'schema_version' => 1,
            'revision' => 1,
            'scene_json' => $scene,
            'checksum' => SpeichereHausplanerDokument::checksum($scene),
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }
}
