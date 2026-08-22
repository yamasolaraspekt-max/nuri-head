<?php

namespace App\Domain\Hausplaner\Validation;

use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use RuntimeException;

/** Validiert den Save-Pfad gegen das aus Zod erzeugte v2-Schema plus Querbezüge. */
class SceneDocumentValidator
{
    private const SCHEMA_PATH = 'resources/planner/hausplaner/domain/scene-document-v2.schema.json';

    private static ?object $schema = null;

    /** @param array<string, mixed> $scene @return list<string> */
    public function fehler(array $scene): array
    {
        $json = json_encode($scene, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return ['Die Szene kann nicht als JSON gelesen werden.'];
        }

        $daten = json_decode($json);
        $ergebnis = (new Validator)->validate($daten, $this->schema());
        if ($ergebnis->hasError()) {
            return array_map(
                static fn (string $meldung): string => 'Schema: '.$meldung,
                (new ErrorFormatter)->formatFlat($ergebnis->error()),
            );
        }

        return $this->integritaetsfehler($scene);
    }

    private function schema(): object
    {
        if (self::$schema !== null) {
            return self::$schema;
        }

        $inhalt = file_get_contents(base_path(self::SCHEMA_PATH));
        $schema = $inhalt === false ? null : json_decode($inhalt);
        if (! is_object($schema)) {
            throw new RuntimeException('Hausplaner-JSON-Schema ist nicht lesbar.');
        }

        return self::$schema = $schema;
    }

    /**
     * Zod-superRefine und dokumentweite Referenzen sind nicht als JSON Schema ausdrueckbar.
     * Diese Regeln spiegeln sceneDocumentSchema + validateSceneIntegrity.
     *
     * **Seit Z1-E4-1 (d2) gilt das auch fuer die drei Sammlungen** nodes/ceilings/roofs/
     * foundationSlabs — vorher stand hier „exakt", und fuer ceilings/roofs stimmte es nicht.
     * *Was den Satz traegt, sind die Feature-Tests je Sammlung, nicht dieser Satz.*
     *
     * @param array<string, mixed> $scene
     * @return list<string>
     */
    private function integritaetsfehler(array $scene): array
    {
        $fehler = [];
        $levelIds = [];
        foreach ($scene['levels'] as $level) {
            $levelIds[(string) $level['id']] = true;
        }

        // **Z1-E4-1 (d2) — die drei Sammlungen, die bis heute NICHT gespiegelt waren.**
        //
        // `validateSceneIntegrity` (domain/validation.ts) prueft `ceilings`, `roofs` und seit
        // diesem Blatt `foundationSlabs` gegen die Level-Liste; PHP pruefte nur `nodes`. **Der
        // Kommentar oben sagte trotzdem „spiegeln exakt".** Damit lehnte der Client ab, was der
        // Server annahm — eine Decke auf einem geloeschten Geschoss kam durch den Speicherweg.
        //
        // *Gefunden beim Lesen fuer den Schema-Sprung, gemeldet statt im Vorbeigehen behoben;
        // der Dirigent hat die Behebung diesem Blatt zugewiesen, weil E4 diese Datei ohnehin
        // anfasst und es nach Schema 4 eine Migration waere.* **Drei Schleifen, drei Tests** —
        // der Kommentar wird durch Zusagen wahr gemacht, nicht durch Behauptung.
        foreach ([
            'ceilings' => 'Decke',
            'roofs' => 'Dach',
            'foundationSlabs' => 'Bodenplatte',
        ] as $sammlung => $bezeichnung) {
            foreach (($scene[$sammlung] ?? []) as $eintrag) {
                if (! is_array($eintrag)) {
                    continue;
                }
                $levelId = (string) ($eintrag['levelId'] ?? '');
                if (! isset($levelIds[$levelId])) {
                    $id = (string) ($eintrag['id'] ?? '?');
                    $fehler[] = "{$bezeichnung} {$id}: unbekanntes Level {$levelId}.";
                }
            }
        }

        $waende = [];
        foreach ($scene['nodes'] as $node) {
            $id = (string) $node['id'];
            $levelId = (string) $node['levelId'];
            if (! isset($levelIds[$levelId])) {
                $fehler[] = "Node {$id}: unbekanntes Level {$levelId}.";
            }

            if ($node['type'] === 'wall') {
                $dx = $node['end']['x'] - $node['start']['x'];
                $dy = $node['end']['y'] - $node['start']['y'];
                $laenge = hypot($dx, $dy);
                if ($laenge <= 0) {
                    $fehler[] = "Wand {$id}: hat Laenge 0.";
                } elseif ($laenge < $node['thickness']) {
                    $fehler[] = "Wand {$id}: ist kuerzer als ihre Dicke.";
                }
                $waende[$id] = ['laenge' => $laenge, 'levelId' => $levelId];
            }

            if ($node['type'] === 'zone' && $node['zoneType'] === 'room' && ! $node['derived']) {
                $fehler[] = "Zone {$id}: zoneType room muss derived=true sein.";
            }
        }

        foreach ($scene['nodes'] as $node) {
            if (! in_array($node['type'], ['window', 'door', 'opening'], true)) {
                continue;
            }

            $id = (string) $node['id'];
            $hostWallId = (string) $node['hostWallId'];
            $wand = $waende[$hostWallId] ?? null;
            if ($wand === null) {
                $fehler[] = "Oeffnung {$id}: Wirtswand {$hostWallId} existiert nicht.";
                continue;
            }
            if ($wand['levelId'] !== (string) $node['levelId']) {
                $fehler[] = "Oeffnung {$id}: liegt auf anderem Geschoss als ihre Wirtswand.";
            }
            if ($node['offsetFromWallStart'] + $node['width'] > $wand['laenge']) {
                $fehler[] = "Oeffnung {$id}: ragt ueber das Wandende.";
            }
        }

        return $fehler;
    }
}
