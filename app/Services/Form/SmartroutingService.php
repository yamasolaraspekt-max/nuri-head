<?php

namespace App\Services\Form;

use Illuminate\Support\Facades\DB;

/**
 * FS-05 — Smartrouting: ermittelt aus einem Kontext (Anker) die passenden Checklisten-Formulare.
 *
 * Regel-Semantik (product_formula_routing_rules):
 * - Jede aktive Regel hat Anker-Spalten (article_group_id, lead_product_list_id, object_type).
 *   NULL-Anker = Wildcard (passt immer). Ein gesetzter Anker muss den Kontextwert exakt treffen.
 * - **Spezifität** = Anzahl der gesetzten (nicht-Wildcard) Anker einer Regel. Spezifischere Regeln
 *   gewinnen. Bei Gleichstand entscheidet **priority** (höher zuerst).
 * - Kein Treffer ⇒ leere Liste + Hinweis (definierter Fallback, kein stiller Fehlschlag).
 *
 * Reine Lese-Operation, keine Persistenz.
 */
class SmartroutingService
{
    private const ANKER = ['article_group_id', 'lead_product_list_id', 'object_type'];

    /**
     * @param  array{article_group_id?:int|null, lead_product_list_id?:int|null, object_type?:string|null}  $context
     * @return array{formula_ids:int[], rules:array<int,array>, ambiguous:bool, hinweis:?string}
     */
    public function route(array $context): array
    {
        $rules = DB::table('product_formula_routing_rules')->where('is_active', true)->get();

        $treffer = [];
        foreach ($rules as $rule) {
            $spezifitaet = 0;
            $passt = true;
            foreach (self::ANKER as $anker) {
                if ($rule->$anker === null) {
                    continue; // Wildcard
                }
                $spezifitaet++;
                $ctx = $context[$anker] ?? null;
                if ($ctx === null || (string) $ctx !== (string) $rule->$anker) {
                    $passt = false;
                    break;
                }
            }
            if ($passt) {
                $treffer[] = ['rule' => $rule, 'spezifitaet' => $spezifitaet, 'priority' => (int) $rule->priority];
            }
        }

        // Sortierung: Spezifität desc, dann priority desc, dann rule-id asc (stabil).
        usort($treffer, fn ($a, $b) => $b['spezifitaet'] <=> $a['spezifitaet']
            ?: $b['priority'] <=> $a['priority']
            ?: $a['rule']->id <=> $b['rule']->id);

        // Eindeutige Formular-IDs in Trefferreihenfolge.
        $formulaIds = [];
        $regeln = [];
        foreach ($treffer as $t) {
            $fid = (int) $t['rule']->product_formula_id;
            if (! in_array($fid, $formulaIds, true)) {
                $formulaIds[] = $fid;
            }
            $regeln[] = ['rule_id' => (int) $t['rule']->id, 'product_formula_id' => $fid,
                'spezifitaet' => $t['spezifitaet'], 'priority' => $t['priority']];
        }

        $ambiguous = count($formulaIds) > 1;
        $hinweis = match (true) {
            $formulaIds === [] => 'Kein Formular für diesen Kontext gefunden (Fallback: leere Liste).',
            $ambiguous => 'Mehrere Formulare treffen zu — nach Spezifität/Priorität sortiert.',
            default => null,
        };

        return ['formula_ids' => $formulaIds, 'rules' => $regeln, 'ambiguous' => $ambiguous, 'hinweis' => $hinweis];
    }
}
