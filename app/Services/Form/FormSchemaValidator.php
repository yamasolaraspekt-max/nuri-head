<?php

namespace App\Services\Form;

/**
 * FS-02 — Server-Validierung des v2-`fields`-JSON (product_formulas.schema_version = 2).
 *
 * Reine Funktion, keine Persistenz: prüft ein dekodiertes `fields`-Array gegen das Ziel-Schema (§5) —
 * stabile Slugs (eindeutig), Typ-Whitelist, Optionspflicht bei Auswahltypen, `visible_if`-/`calculation`-
 * Shape, numerische Grenzen, Enums (source/risk_level). Gibt eine Fehlerliste zurück (leer = gültig).
 *
 * Ziel: EIN Schreibpfad validiert gegen dieses Schema, damit `fields` nicht unkontrolliert wächst
 * (JSON-Wildwuchs-Mitigation). Bindet weder Renderer noch DB.
 */
class FormSchemaValidator
{
    /** Aktuelle Schema-Version. */
    public const VERSION = 2;

    /** Feldtyp-Whitelist (portiert aus FormFieldType; real belegte + geometrische Rechen-Typen). */
    public const FIELD_TYPES = [
        'text', 'textarea', 'number', 'integer', 'decimal', 'length', 'area', 'volume', 'power',
        'select', 'multiselect', 'checkbox', 'checkbox_group', 'boolean', 'consent',
        'date', 'email', 'plz', 'image', 'file', 'calculation',
    ];

    /** Auswahltypen: brauchen nicht-leere options. */
    public const CHOICE_TYPES = ['select', 'multiselect', 'checkbox_group'];

    public const VISIBLE_IF_OPS = ['=', '!=', '>', '<', '>=', '<=', 'in', 'not_in'];

    public const SOURCES = ['manuell', 'recognition', 'import'];

    public const RISK_LEVELS = ['normal', 'fachlich-vorlaeufig'];

    /**
     * Validiert das gesamte `fields`-Array. Gibt Fehlerliste zurück (leer = gültig).
     *
     * @param  mixed  $fields  dekodiertes JSON (erwartet: Array von Feld-Objekten/Arrays)
     * @return string[]
     */
    public function validate($fields): array
    {
        $fehler = [];
        if (! is_array($fields)) {
            return ['fields muss ein Array von Feld-Objekten sein.'];
        }
        if ($fields === []) {
            return ['fields ist leer — mindestens ein Feld erforderlich.'];
        }

        $keys = [];
        foreach ($fields as $i => $feld) {
            $feld = (array) $feld;
            $pos = "Feld #{$i}";

            // key: Pflicht, stabiler Slug, eindeutig.
            $key = $feld['key'] ?? null;
            if (! is_string($key) || $key === '') {
                $fehler[] = "{$pos}: 'key' fehlt oder ist leer.";
            } elseif (! preg_match('/^[a-z0-9_]+$/', $key)) {
                $fehler[] = "{$pos}: 'key' '{$key}' ist kein gültiger Slug ([a-z0-9_]).";
            } else {
                if (isset($keys[$key])) {
                    $fehler[] = "{$pos}: 'key' '{$key}' ist doppelt (bereits Feld #{$keys[$key]}).";
                }
                $keys[$key] = $i;
                $pos = "Feld '{$key}'";
            }

            // label: Pflicht.
            if (! isset($feld['label']) || ! is_string($feld['label']) || $feld['label'] === '') {
                $fehler[] = "{$pos}: 'label' fehlt.";
            }

            // type: Pflicht + Whitelist.
            $type = $feld['type'] ?? null;
            if (! is_string($type) || ! in_array($type, self::FIELD_TYPES, true)) {
                $fehler[] = "{$pos}: 'type' '".(is_string($type) ? $type : gettype($type))."' nicht in Whitelist.";
                $type = null;
            }

            $this->validiereOptionen($feld, $type, $pos, $fehler);
            $this->validiereGrenzen($feld, $pos, $fehler);
            $this->validiereVisibleIf($feld, $pos, $fehler);
            $this->validiereCalculation($feld, $type, $pos, $fehler);
            $this->validiereEnums($feld, $pos, $fehler);
        }

        // visible_if-Referenzen müssen existierende Felder treffen.
        foreach ($fields as $i => $feld) {
            $feld = (array) $feld;
            $vi = $feld['visible_if'] ?? null;
            if (is_array($vi) && isset($vi['field']) && is_string($vi['field']) && ! isset($keys[$vi['field']])) {
                $k = $feld['key'] ?? "#{$i}";
                $fehler[] = "Feld '{$k}': visible_if referenziert unbekanntes Feld '{$vi['field']}'.";
            }
        }

        return $fehler;
    }

    /** Bequemer Gesamt-Check inkl. Kopf-Version. */
    public function validateFormula(int $schemaVersion, $fields): array
    {
        if ($schemaVersion !== self::VERSION) {
            return ["schema_version {$schemaVersion} wird von diesem Validator (v".self::VERSION.') nicht geprüft.'];
        }

        return $this->validate($fields);
    }

    private function validiereOptionen(array $feld, ?string $type, string $pos, array &$fehler): void
    {
        $istAuswahl = $type !== null && in_array($type, self::CHOICE_TYPES, true);
        $options = $feld['options'] ?? null;

        if ($istAuswahl) {
            if (! is_array($options) || $options === []) {
                $fehler[] = "{$pos}: Auswahltyp '{$type}' braucht nicht-leere 'options'.";

                return;
            }
            foreach ($options as $j => $opt) {
                $opt = (array) $opt;
                if (! array_key_exists('value', $opt) || ! isset($opt['label']) || $opt['label'] === '') {
                    $fehler[] = "{$pos}: option #{$j} braucht 'value' und 'label'.";
                }
            }
        } elseif ($options !== null && $options !== []) {
            $fehler[] = "{$pos}: 'options' nur bei Auswahltypen erlaubt.";
        }
    }

    private function validiereGrenzen(array $feld, string $pos, array &$fehler): void
    {
        foreach (['min', 'max', 'decimals'] as $num) {
            if (array_key_exists($num, $feld) && $feld[$num] !== null && ! is_numeric($feld[$num])) {
                $fehler[] = "{$pos}: '{$num}' muss numerisch oder null sein.";
            }
        }
        if (isset($feld['min'], $feld['max']) && is_numeric($feld['min']) && is_numeric($feld['max'])
            && (float) $feld['min'] > (float) $feld['max']) {
            $fehler[] = "{$pos}: 'min' > 'max'.";
        }
        if (isset($feld['decimals']) && is_numeric($feld['decimals']) && (int) $feld['decimals'] < 0) {
            $fehler[] = "{$pos}: 'decimals' darf nicht negativ sein.";
        }
    }

    private function validiereVisibleIf(array $feld, string $pos, array &$fehler): void
    {
        $vi = $feld['visible_if'] ?? null;
        if ($vi === null) {
            return; // immer sichtbar
        }
        if (! is_array($vi)) {
            $fehler[] = "{$pos}: 'visible_if' muss null oder Objekt {field,op,value} sein.";

            return;
        }
        if (! isset($vi['field']) || ! is_string($vi['field']) || $vi['field'] === '') {
            $fehler[] = "{$pos}: visible_if.field fehlt.";
        }
        $op = $vi['op'] ?? null;
        if (! is_string($op) || ! in_array($op, self::VISIBLE_IF_OPS, true)) {
            $fehler[] = "{$pos}: visible_if.op '".(is_string($op) ? $op : gettype($op))."' ungültig.";
        }
        if (! array_key_exists('value', $vi)) {
            $fehler[] = "{$pos}: visible_if.value fehlt.";
        }
    }

    private function validiereCalculation(array $feld, ?string $type, string $pos, array &$fehler): void
    {
        $calc = $feld['calculation'] ?? null;
        if ($type === 'calculation') {
            if (! is_array($calc) || ! isset($calc['formel']) || ! is_string($calc['formel']) || $calc['formel'] === '') {
                $fehler[] = "{$pos}: type=calculation braucht calculation.formel (nicht-leerer String).";
            } elseif (mb_strlen($calc['formel']) > 2000) {
                $fehler[] = "{$pos}: calculation.formel überschreitet 2000 Zeichen (DoS-Grenze).";
            }
        } elseif ($calc !== null && $calc !== []) {
            $fehler[] = "{$pos}: 'calculation' nur bei type=calculation erlaubt.";
        }
    }

    private function validiereEnums(array $feld, string $pos, array &$fehler): void
    {
        if (isset($feld['source']) && ! in_array($feld['source'], self::SOURCES, true)) {
            $fehler[] = "{$pos}: 'source' '{$feld['source']}' ungültig (manuell|recognition|import).";
        }
        if (isset($feld['risk_level']) && ! in_array($feld['risk_level'], self::RISK_LEVELS, true)) {
            $fehler[] = "{$pos}: 'risk_level' '{$feld['risk_level']}' ungültig (normal|fachlich-vorlaeufig).";
        }
    }
}
