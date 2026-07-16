<?php

namespace App\Services\BuildingModel;

/**
 * G1b-1 — deterministischer, schlüssel-normalisierter Hash für kanonische Modell-/Quell-Payloads.
 * Rekursive ksort-Normalisierung stellt sicher, dass strukturell gleiche Payloads (unabhängig von der
 * Schlüsselreihenfolge) denselben Hash liefern → belastbare Idempotenz-/Konflikterkennung.
 * Rein, nebenwirkungsfrei.
 */
final class CanonicalHash
{
    /** @param array<mixed> $data */
    public static function of(array $data): string
    {
        return hash('sha256', (string) json_encode(self::normalize($data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param  array<mixed>  $data
     * @return array<mixed>
     */
    private static function normalize(array $data): array
    {
        $isList = array_is_list($data);
        $out = [];
        foreach ($data as $k => $v) {
            $out[$k] = is_array($v) ? self::normalize($v) : $v;
        }
        if (! $isList) {
            ksort($out);
        }

        return $out;
    }
}
