<?php

namespace App\Services\BuildingModel;

use RuntimeException;

/**
 * G1b-1 — wird geworfen, wenn versucht wird, eine bestehende abgeleitete Modellversion zu ändern oder zu
 * löschen. `building_model_versions` ist append-only/unveränderlich: eine neue Fassung erzeugt eine neue
 * Version, nie ein In-place-Update oder Delete.
 */
final class BuildingModelVersionImmutableException extends RuntimeException {}
