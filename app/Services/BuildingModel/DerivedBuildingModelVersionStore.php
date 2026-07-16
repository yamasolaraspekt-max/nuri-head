<?php

namespace App\Services\BuildingModel;

use App\Models\BuildingModelVersion;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * G1b-1 — Derived-only Store für abgeleitete kanonische Gebäudemodell-Versionen.
 *
 * Speichert AUSSCHLIESSLICH einen bereits erzeugten, kanonischen Modell-Payload (G1a-Format) als
 * unveränderlichen Snapshot. Er interpretiert KEINE Altgeometrie, erfindet KEINE Pflichtoperanden, rät KEINE
 * Wandreferenzen/Nordwinkel, transformiert KEINE Öffnungen, liest KEINEN Request und verändert KEINE Tabelle
 * außer `building_model_versions`. Die kanonische Schreibwahrheit bleibt `anforderungsprofile.gebaeude_geometrie`.
 *
 * Ablauf (Startblock §9): Quelle entgegennehmen → Quell-Hash → Payload normalisieren → Validator →
 * bei Fehlern keine Zeile → Payload-Hash → Idempotenzschlüssel prüfen (identisch → bestehende Zeile;
 * gleicher Schlüssel, anderer Hash → Projektionskonflikt) → neue gültige Projektion append-only speichern.
 * Prüfung und Anlage laufen in einer Transaktion; das Unique-Constraint ist die letzte Schutzlinie.
 */
class DerivedBuildingModelVersionStore
{
    /** Version der Projektionslogik dieses Stores (Teil des Idempotenzschlüssels). */
    public const PROJECTION_VERSION = '1';

    public function __construct(
        private readonly CanonicalBuildingModelValidator $validator = new CanonicalBuildingModelValidator,
    ) {}

    /**
     * @param  array<string,mixed>  $canonicalModel  bereits gebautes kanonisches Modell (G1a-Schema)
     *
     * @throws CanonicalModelInvalidException  wenn der Payload den Vertrag nicht erfüllt (keine Zeile)
     * @throws ProjectionConflictException     bei gleichem Schlüssel und abweichendem Payload
     */
    public function projiziere(array $canonicalModel, SourceGeometryRef $source): BuildingModelVersion
    {
        // 1. Validieren — kein Schreiben bei Fehlern, Fehlersammlung bleibt erhalten.
        $validation = $this->validator->validate($canonicalModel);
        if (! $validation->isValid()) {
            throw new CanonicalModelInvalidException($validation);
        }

        $schemaVersion = (string) ($canonicalModel['schema_version'] ?? '');
        $payloadHash = CanonicalHash::of($canonicalModel);
        $idempotencyKey = $this->idempotencyKey($source->profileId(), $source->version(), $schemaVersion);

        return DB::transaction(function () use ($canonicalModel, $source, $schemaVersion, $payloadHash, $idempotencyKey, $validation) {
            // 2. Idempotenz: bestehende Version unter demselben Schlüssel sperren + lesen.
            $existing = $this->findByKey($source->profileId(), $source->version(), $schemaVersion)->lockForUpdate()->first();
            if ($existing !== null) {
                if ($existing->payload_hash === $payloadHash) {
                    return $existing; // identische Projektion → dieselbe Zeile, kein Duplikat
                }
                throw new ProjectionConflictException($idempotencyKey, (string) $existing->payload_hash, $payloadHash);
            }

            // 3. Neue gültige Projektion append-only anlegen.
            try {
                return BuildingModelVersion::create([
                    'id' => (string) Str::uuid(),                                  // surrogater Zeilen-PK
                    'revision_id' => (string) $canonicalModel['revision_id'],
                    'model_id' => (string) $canonicalModel['model_id'],
                    'parent_revision_id' => $canonicalModel['parent_revision_id'] ?? null,
                    'object_id' => (int) $canonicalModel['object_id'],
                    'source_type' => SourceGeometryRef::SOURCE_TYPE,
                    'source_profile_id' => $source->profileId(),
                    'source_version' => $source->version(),
                    'source_hash' => $source->sourceHash(),
                    'source_captured_at' => $source->capturedAt(),
                    'schema_version' => $schemaVersion,
                    'projection_version' => self::PROJECTION_VERSION,
                    'payload' => $canonicalModel,
                    'payload_hash' => $payloadHash,
                    'validation_status' => 'valid',
                    'warnings' => $this->warnungen($validation),
                    'projected_at' => now(),
                    'projection_role' => BuildingModelVersion::ROLE_DERIVED_ONLY,
                ]);
            } catch (QueryException $e) {
                // 4. Wettlauf: das Unique-Constraint hat gegriffen → erneut lesen und idempotent auflösen.
                if (! $this->isUniqueViolation($e)) {
                    throw $e;
                }
                $row = $this->findByKey($source->profileId(), $source->version(), $schemaVersion)->first();
                if ($row !== null && $row->payload_hash === $payloadHash) {
                    return $row;
                }
                throw new ProjectionConflictException($idempotencyKey, (string) ($row->payload_hash ?? ''), $payloadHash);
            }
        });
    }

    /** @return \Illuminate\Database\Eloquent\Builder<BuildingModelVersion> */
    private function findByKey(int $profileId, int $version, string $schemaVersion)
    {
        return BuildingModelVersion::query()
            ->where('source_profile_id', $profileId)
            ->where('source_version', $version)
            ->where('schema_version', $schemaVersion)
            ->where('projection_version', self::PROJECTION_VERSION);
    }

    private function idempotencyKey(int $profileId, int $version, string $schemaVersion): string
    {
        return "{$profileId}:{$version}:{$schemaVersion}:".self::PROJECTION_VERSION;
    }

    /** @return array<int,array{code:string,path:string,message:string,severity:string}> */
    private function warnungen(CanonicalModelValidationResult $validation): array
    {
        return array_values(array_filter(
            $validation->issues(),
            fn ($i) => $i['severity'] === CanonicalModelValidationResult::SEVERITY_WARNING
        ));
    }

    private function isUniqueViolation(QueryException $e): bool
    {
        // MySQL 1062 / SQLSTATE 23000 — Duplicate entry.
        return ($e->errorInfo[1] ?? null) === 1062 || ($e->getCode() === '23000');
    }
}
