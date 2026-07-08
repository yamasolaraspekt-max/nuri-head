<?php

namespace App\Services\Form;

/**
 * FS-04 — Bedingte Sichtbarkeit (`visible_if`). Server-autoritative Auswertung von `Feld op Wert`
 * gegen den aktuellen Antwortstand. Die Alpine-Sicht (x-show) spiegelt dieselbe Logik nur clientseitig;
 * verbindlich ist der Server (Pflichtfeld-Prüfung, Persistenz).
 *
 * Kernregeln:
 * - `visible_if = null` ⇒ immer sichtbar.
 * - Kaskade: ein Feld ist nur sichtbar, wenn auch das referenzierte Bedingungsfeld sichtbar ist
 *   (kein „verwaistes" sichtbares Kind unter unsichtbarem Elternteil).
 * - **Unsichtbares Pflichtfeld blockt NICHT** (fehlendePflichtfelder liefert nur sichtbare Pflichtfelder).
 *
 * Reine Funktion, keine Persistenz.
 */
class VisibleIfService
{
    /** Wertet eine einzelne Bedingung {field, op, value} gegen $answers aus. null ⇒ sichtbar. */
    public function istSichtbar(?array $cond, array $answers): bool
    {
        if ($cond === null || ! isset($cond['field'], $cond['op'])) {
            return true;
        }
        $ist = $answers[$cond['field']] ?? null;
        $soll = $cond['value'] ?? null;

        return match ($cond['op']) {
            '=' => $this->gleich($ist, $soll),
            '!=' => ! $this->gleich($ist, $soll),
            '>' => $this->num($ist) > $this->num($soll),
            '<' => $this->num($ist) < $this->num($soll),
            '>=' => $this->num($ist) >= $this->num($soll),
            '<=' => $this->num($ist) <= $this->num($soll),
            'in' => is_array($soll) && in_array($ist, $soll, false),
            'not_in' => is_array($soll) && ! in_array($ist, $soll, false),
            default => true, // unbekannter Operator: nicht verstecken (Validator hat ihn ohnehin abgelehnt)
        };
    }

    /**
     * Liefert die aktuell sichtbaren Felder (mit Kaskade über die visible_if-Referenzen).
     *
     * @param  array<int, array>  $fields  v2-Feld-Objekte
     * @return array<int, array>
     */
    public function sichtbareFelder(array $fields, array $answers): array
    {
        $byKey = [];
        foreach ($fields as $f) {
            if (isset($f['key'])) {
                $byKey[$f['key']] = $f;
            }
        }

        $cache = [];
        $sichtbar = fn (array $f) => $this->sichtbarKaskade($f, $byKey, $answers, $cache);

        return array_values(array_filter($fields, $sichtbar));
    }

    /**
     * Sichtbare Pflichtfelder ohne (befüllten) Wert. Unsichtbare Pflichtfelder werden bewusst
     * ausgelassen — sie dürfen die Speicherung nicht blockieren.
     *
     * @return string[] Liste der fehlenden Feld-Keys
     */
    public function fehlendePflichtfelder(array $fields, array $answers): array
    {
        $fehlend = [];
        foreach ($this->sichtbareFelder($fields, $answers) as $f) {
            if (($f['required'] ?? false) && $this->leer($answers[$f['key']] ?? null)) {
                $fehlend[] = $f['key'];
            }
        }

        return $fehlend;
    }

    /** Alpine-`x-show`-Ausdruck für ein Feld (Scope-2-Rendering). Erwartet ein reaktives `answers`-Objekt. */
    public function alpineAusdruck(?array $cond): string
    {
        if ($cond === null || ! isset($cond['field'], $cond['op'])) {
            return 'true';
        }
        $feld = "answers['".addslashes((string) $cond['field'])."']";
        $val = $cond['value'] ?? null;

        return match ($cond['op']) {
            '=' => "{$feld} == ".$this->js($val),
            '!=' => "{$feld} != ".$this->js($val),
            '>' => "Number({$feld}) > ".$this->js($val),
            '<' => "Number({$feld}) < ".$this->js($val),
            '>=' => "Number({$feld}) >= ".$this->js($val),
            '<=' => "Number({$feld}) <= ".$this->js($val),
            'in' => $this->js($val).".includes({$feld})",
            'not_in' => '!'.$this->js($val).".includes({$feld})",
            default => 'true',
        };
    }

    private function sichtbarKaskade(array $f, array $byKey, array $answers, array &$cache): bool
    {
        $key = $f['key'] ?? spl_object_id((object) $f);
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }
        $cache[$key] = true; // Zyklenschutz (vorläufig sichtbar während der Auswertung)

        $cond = $f['visible_if'] ?? null;
        $ok = $this->istSichtbar($cond, $answers);
        if ($ok && is_array($cond) && isset($cond['field']) && isset($byKey[$cond['field']])) {
            $ok = $this->sichtbarKaskade($byKey[$cond['field']], $byKey, $answers, $cache);
        }

        return $cache[$key] = $ok;
    }

    private function gleich(mixed $a, mixed $b): bool
    {
        if (is_bool($b) || is_bool($a)) {
            return (bool) $a === (bool) $b;
        }

        return (string) $a === (string) $b;
    }

    private function num(mixed $v): float
    {
        return is_numeric($v) ? (float) $v : NAN;
    }

    private function leer(mixed $v): bool
    {
        return $v === null || $v === '' || $v === [];
    }

    private function js(mixed $v): string
    {
        return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
