<?php

namespace App\Services\Form;

/**
 * Sichere, framework-neutrale Auswertung von Formular-Berechnungsformeln (FS-03).
 *
 * Faithful-Port der eval-freien Engine aus playground
 * (backend-laravel app/Services/Form/FormulaEvaluationService.php), aber
 * VOLLSTÄNDIG von den Eloquent-Models (DynamicForm/FormField/FormAnswer)
 * ENTKOPPELT: reiner Service mit klarem Ein-/Ausgabe-Vertrag, KEIN DB-Zugriff,
 * KEINE Persistenz, KEINE Laravel-Kopplung (kein now()/Collection).
 *
 * SICHERHEIT — KEIN eval()/new Function/create_function/call_user_func auf
 * Formelinhalt. Die Formel wird über einen internen Tokenizer (Whitelist) →
 * Shunting-Yard → RPN → Stack-Evaluator ausgewertet. Erlaubt sind
 * AUSSCHLIESSLICH:
 *   - Operatoren:  +  -  *  /   (sowie Klammern, unäres ±)
 *   - Funktionen:  SUM(a,b,…)   MENGE(a)        (Aggregat-Summe / Durchreichen)
 *                  FLAECHE(l,b) = l*b           VOLUMEN(l,b,h) = l*b*h
 *   - Operanden:   Feld-Slugs (Schlüssel im $operands-Array) + numerische Literale
 * Jedes nicht-whitelistete Zeichen/Token, jeder unbekannte Bezeichner (kein
 * Operand) führt zu status 'unvollstaendig' und KEINER Zahl — niemals zu einer
 * Ausführung. Unbekannte Bezeichner (z. B. `alert`, `require`, `__proto__`)
 * werden als Daten-Referenz geparst und, weil sie kein Operand sind, abgelehnt.
 *
 * OPERANDEN-GATE (drei Status):
 *   - 'vollstaendig'  → verbindlich=true.
 *   - 'unvollstaendig' → mind. ein referenzierter Pflicht-Operand ohne Wert
 *     (fehlt/leer/unbekannter Bezeichner) ODER Struktur-/Parse-Fehler. KEINE
 *     Zahl; die fehlenden Operanden stehen in `fehlliste`.
 *   - 'enthaelt-ungepruefte-werte' → mind. ein Operand ist is_assumption=true
 *     ODER trägt einen (nicht-leeren) status ∉ {geprueft,bestaetigt,freigegeben}
 *     ODER ist ein berechneter Operand mit ungeprüftem Status. Ergebnis wird
 *     berechnet, aber verbindlich=false.
 */
class FormulaEvaluationService
{
    /** Engine-Version für den Meta-Snapshot (bei Logikänderungen erhöhen). */
    public const ENGINE_VERSION = '1.0.0-ticket';

    public const STATUS_VOLLSTAENDIG = 'vollstaendig';
    public const STATUS_UNVOLLSTAENDIG = 'unvollstaendig';
    public const STATUS_UNGEPRUEFT = 'enthaelt-ungepruefte-werte';

    /** Als „geprüft/freigegeben" geltende Operanden-Status. */
    private const FREIGEGEBEN_STATUS = ['geprueft', 'bestaetigt', 'freigegeben'];

    /** DoS-Schutz: maximale Länge einer Formel (Zeichen). Darüber → kein Ergebnis. */
    public const MAX_FORMEL_LAENGE = 2000;

    /** DoS-Schutz: maximale Klammerschachtelungstiefe. Darüber → kein Ergebnis. */
    public const MAX_KLAMMER_TIEFE = 32;

    /** Whitelist der erlaubten Funktionen → erwartete (min) Argumentzahl. */
    private const FUNKTIONEN = [
        'SUM'     => 1,
        'MENGE'   => 1,
        'FLAECHE' => 2,
        'VOLUMEN' => 3,
    ];

    public function __construct(
        private readonly UnitConversionService $units = new UnitConversionService(),
    ) {
    }

    /**
     * Wertet eine Formel gegen einen Satz Operanden aus.
     *
     * EIN-/AUSGABE-VERTRAG (framework-neutral):
     *
     * @param string $formula Die Formel-Zeichenkette (z. B. "laenge * breite").
     * @param array<string, array{
     *     value: float|int|array<int, float|int>|null,
     *     unit?: string|null,
     *     is_assumption?: bool,
     *     status?: string|null,
     *     is_calculated?: bool
     * }> $operands  Operanden je Feld-Slug. `value` = Skalar (Einzelfeld),
     *              Array (Wiederholzeilen — wird von SUM aggregiert) oder null
     *              (kein Wert → Pflicht-Operand fehlt).
     * @param array{
     *     slug?: string|null,
     *     unit?: string|null,
     *     decimals?: int|null,
     *     rundung?: string|null,
     *     min_value?: float|int|null,
     *     max_value?: float|int|null
     * } $field  Ziel-Feld-Konfiguration (Einheit, Rundung, Grenzen).
     *
     * @return array{
     *     status: string,
     *     value: float|null,
     *     verbindlich: bool,
     *     fehlliste: array<int, string>,
     *     protokoll: array<string, mixed>
     * }
     */
    public function evaluate(string $formula, array $operands, array $field = []): array
    {
        $protokoll = [
            'engine_version' => self::ENGINE_VERSION,
            'field_slug'     => $field['slug'] ?? null,
            'ziel_einheit'   => $field['unit'] ?? null,
            'formel'         => $formula,
            'hinweise'       => [],
            'operanden'      => [],
        ];

        $formel = trim($formula);
        if ($formel === '') {
            $protokoll['hinweise'][] = 'Keine Formel angegeben.';

            return $this->ergebnis(null, self::STATUS_UNVOLLSTAENDIG, false, [], $protokoll);
        }

        // Tokenisieren + auf Whitelist prüfen (Sicherheits-Gate).
        $tokens = $this->tokenize($formel);
        if ($tokens === null) {
            $protokoll['hinweise'][] = 'Formel enthält unerlaubte Zeichen/Token — Auswertung verweigert.';

            return $this->ergebnis(null, self::STATUS_UNVOLLSTAENDIG, false, [], $protokoll);
        }

        // Operanden-Werte (in Ziel-Einheit umgerechnet) + Gate-Status sammeln.
        $referenzierte = $this->referenzierteSlugs($tokens);
        $aufgeloest = $this->loeseOperandenAuf($referenzierte, $operands, $field, $protokoll);
        if ($aufgeloest['fehlend'] !== []) {
            $protokoll['hinweise'][] = 'Pflicht-Operanden ohne Wert: ' . implode(', ', $aufgeloest['fehlend']) . '.';

            return $this->ergebnis(null, self::STATUS_UNVOLLSTAENDIG, false, $aufgeloest['fehlend'], $protokoll);
        }

        // Auswerten (RPN). Bei strukturellem Fehler → unvollständig, keine Zahl.
        try {
            $roh = $this->werteAus($tokens, $aufgeloest['werte']);
        } catch (\Throwable $e) {
            $protokoll['hinweise'][] = 'Formel konnte nicht ausgewertet werden: ' . $e->getMessage();

            return $this->ergebnis(null, self::STATUS_UNVOLLSTAENDIG, false, [], $protokoll);
        }

        if ($roh === null || ! is_finite($roh)) {
            $protokoll['hinweise'][] = 'Kein gültiges numerisches Ergebnis (z. B. Division durch 0).';

            return $this->ergebnis(null, self::STATUS_UNVOLLSTAENDIG, false, [], $protokoll);
        }

        // Runden nach decimals/Modus.
        $gerundet = $this->runde($roh, $field);
        $protokoll['ergebnis_roh'] = $roh;

        // min/max-Prüfung (nur Warnung im Protokoll, kein Abbruch).
        $this->pruefeGrenzen($gerundet, $field, $protokoll);

        // Operanden-Gate: ungeprüfte Werte → nicht verbindlich.
        $status = $aufgeloest['ungeprueft']
            ? self::STATUS_UNGEPRUEFT
            : self::STATUS_VOLLSTAENDIG;
        $verbindlich = ($status === self::STATUS_VOLLSTAENDIG);
        $protokoll['verbindlich'] = $verbindlich;

        return $this->ergebnis($gerundet, $status, $verbindlich, [], $protokoll);
    }

    // ── Tokenizer (Whitelist) ────────────────────────────────────────────────

    /**
     * Zerlegt die Formel in Tokens. Liefert null, sobald ein unerlaubtes
     * Zeichen/Token auftaucht (Sicherheits-Gate).
     *
     * @return array<int, array{typ: string, wert: string}>|null
     */
    private function tokenize(string $formel): ?array
    {
        $tokens = [];
        $len = mb_strlen($formel);

        // DoS-Schutz: überlange Formeln werden NICHT geparst (kein Ergebnis).
        if ($len > self::MAX_FORMEL_LAENGE) {
            return null;
        }

        $i = 0;
        $tiefe = 0; // aktuelle Klammerschachtelungstiefe (DoS-/Stack-Schutz).

        while ($i < $len) {
            $c = mb_substr($formel, $i, 1);

            // Whitespace überspringen.
            if (trim($c) === '') {
                $i++;
                continue;
            }

            // Operatoren / Klammern / Komma.
            if (in_array($c, ['+', '-', '*', '/', '(', ')', ','], true)) {
                $typ = match ($c) {
                    '(' => 'lparen',
                    ')' => 'rparen',
                    ',' => 'comma',
                    default => 'op',
                };
                // Klammerschachtelung begrenzen (verhindert exzessiven Stack-Aufwand).
                if ($typ === 'lparen') {
                    $tiefe++;
                    if ($tiefe > self::MAX_KLAMMER_TIEFE) {
                        return null;
                    }
                } elseif ($typ === 'rparen') {
                    $tiefe--;
                }
                $tokens[] = ['typ' => $typ, 'wert' => $c];
                $i++;
                continue;
            }

            // Zahl-Literal (mit optionalem Dezimalpunkt).
            if (ctype_digit($c) || $c === '.') {
                $zahl = '';
                while ($i < $len) {
                    $cc = mb_substr($formel, $i, 1);
                    if (ctype_digit($cc) || $cc === '.') {
                        $zahl .= $cc;
                        $i++;
                    } else {
                        break;
                    }
                }
                if (! is_numeric($zahl)) {
                    return null;
                }
                $tokens[] = ['typ' => 'num', 'wert' => $zahl];
                continue;
            }

            // Bezeichner (Funktion oder Feld-Slug): a-z 0-9 _ -.
            if ($this->istBezeichnerStart($c)) {
                $name = '';
                while ($i < $len) {
                    $cc = mb_substr($formel, $i, 1);
                    if ($this->istBezeichnerZeichen($cc)) {
                        $name .= $cc;
                        $i++;
                    } else {
                        break;
                    }
                }

                $gross = mb_strtoupper($name);
                if (array_key_exists($gross, self::FUNKTIONEN)) {
                    $tokens[] = ['typ' => 'func', 'wert' => $gross];
                } else {
                    $tokens[] = ['typ' => 'ident', 'wert' => $name];
                }
                continue;
            }

            // Alles andere ist unerlaubt → Auswertung verweigern.
            return null;
        }

        return $tokens;
    }

    private function istBezeichnerStart(string $c): bool
    {
        return (bool) preg_match('/[a-zA-Z_]/', $c);
    }

    private function istBezeichnerZeichen(string $c): bool
    {
        return (bool) preg_match('/[a-zA-Z0-9_\-]/', $c);
    }

    /**
     * @param array<int, array{typ:string,wert:string}> $tokens
     *
     * @return array<int, string> eindeutige referenzierte Feld-Slugs
     */
    private function referenzierteSlugs(array $tokens): array
    {
        $slugs = [];
        foreach ($tokens as $t) {
            if ($t['typ'] === 'ident') {
                $slugs[$t['wert']] = true;
            }
        }

        return array_keys($slugs);
    }

    // ── Operanden-Auflösung + Gate ───────────────────────────────────────────

    /**
     * Löst die referenzierten Slugs zu (umgerechneten) Werten auf und ermittelt
     * den Gate-Status (ungeprüft) sowie fehlende Pflicht-Operanden.
     *
     * WIEDERHOLGRUPPEN: Ist der `value` eines Operanden ein Array, bildet jede
     * Zeile einen Listeneintrag. Aggregatfunktionen (SUM) summieren über die
     * gesamte Liste; skalare Operatoren (* / + -) und MENGE/FLAECHE/VOLUMEN
     * nutzen den ERSTEN Einzelwert. Ein skalarer `value` entspricht einer
     * 1-elementigen Liste (identisches Verhalten wie Einzelfeld).
     *
     * @param array<int, string>   $slugs
     * @param array<string, mixed> $operands
     * @param array<string, mixed> $field
     * @param array<string, mixed> $protokoll (per Referenz erweitert)
     *
     * @return array{
     *     werte: array<string, array{wert: float, liste: array<int, float>}>,
     *     ungeprueft: bool,
     *     fehlend: array<int, string>
     * }
     */
    private function loeseOperandenAuf(
        array $slugs,
        array $operands,
        array $field,
        array &$protokoll,
    ): array {
        $werte = [];
        $fehlend = [];
        $ungeprueft = false;

        $zielEinheit = $field['unit'] ?? null;

        foreach ($slugs as $slug) {
            // Unbekannter Bezeichner (kein Operand) → fehlender Pflicht-Operand.
            // So werden auch Injection-Bezeichner (alert, require, __proto__ …)
            // abgelehnt: sie sind kein Operand und laufen NIE als Code.
            if (! array_key_exists($slug, $operands)) {
                $fehlend[] = $slug;
                $protokoll['hinweise'][] = "Operand {$slug} ist kein bekanntes Feld — kein Wert.";
                $protokoll['operanden'][] = [
                    'slug' => $slug, 'value' => null, 'unit' => null,
                    'is_assumption' => false, 'status' => 'fehlt',
                ];
                continue;
            }

            $operand = is_array($operands[$slug]) ? $operands[$slug] : [];
            $quellEinheit = $operand['unit'] ?? null;
            $rohWerte = $this->rohWerteListe($operand['value'] ?? null);

            $liste = [];
            $operandUngeprueft = $this->istUngeprueft($operand);

            foreach ($rohWerte as $rohWert) {
                $konvertiert = $rohWert;
                if ($zielEinheit !== null && $quellEinheit !== null) {
                    $c = $this->units->convert($rohWert, $quellEinheit, $zielEinheit);
                    if ($c === null) {
                        // Dimensionsfremd → Warnung, Rohwert verwenden (nie stille Änderung).
                        $protokoll['hinweise'][] =
                            "Operand {$slug}: Einheit {$quellEinheit} nicht in {$zielEinheit} umrechenbar — Rohwert verwendet.";
                    } else {
                        $konvertiert = $c;
                    }
                }
                $liste[] = (float) $konvertiert;
            }

            $snapshot = [
                'slug' => $slug,
                'unit' => $quellEinheit,
                'is_assumption' => (bool) ($operand['is_assumption'] ?? false),
            ];

            if ($liste === []) {
                // Kein numerischer Wert (null / leeres Array) → Pflicht-Operand fehlt.
                $fehlend[] = $slug;
                $snapshot['value'] = null;
                $snapshot['status'] = 'fehlt';
                $protokoll['operanden'][] = $snapshot;
                continue;
            }

            $snapshot['value'] = $liste[0];
            $snapshot['status'] = $operandUngeprueft ? 'ungeprueft' : 'geprueft';
            if (count($liste) > 1) {
                $snapshot['wiederholzeilen'] = count($liste);
                $snapshot['summe'] = array_sum($liste);
            }

            if ($operandUngeprueft) {
                $ungeprueft = true;
            }

            $werte[$slug] = [
                'wert'  => (float) $liste[0],
                'liste' => $liste,
            ];
            $protokoll['operanden'][] = $snapshot;
        }

        return ['werte' => $werte, 'ungeprueft' => $ungeprueft, 'fehlend' => $fehlend];
    }

    /**
     * Normalisiert den `value` eines Operanden auf eine Liste numerischer Werte.
     * Skalar → 1-elementige Liste; Array → je numerische Zeile ein Eintrag
     * (nicht-numerische/leere Zeilen werden übersprungen); null → leere Liste.
     *
     * @param mixed $value
     *
     * @return array<int, float>
     */
    private function rohWerteListe($value): array
    {
        if ($value === null) {
            return [];
        }

        $roh = is_array($value) ? array_values($value) : [$value];
        $liste = [];
        foreach ($roh as $v) {
            if ($v === null || $v === '' || ! is_numeric($v)) {
                continue;
            }
            $liste[] = (float) $v;
        }

        return $liste;
    }

    /**
     * Operanden-Gate: Ein Operand gilt als UNGEPRÜFT (Ergebnis nicht verbindlich),
     * wenn er als Annahme markiert ist (is_assumption=true) ODER einen nicht-leeren
     * status ∉ {geprueft,bestaetigt,freigegeben} trägt ODER ein berechneter Operand
     * (is_calculated=true) mit ungeprüftem Status ist (Vererbung über Berechnungs-
     * ketten). Ein Operand OHNE gesetzten status ist NICHT allein deshalb ungeprüft.
     *
     * @param array<string, mixed> $operand
     */
    private function istUngeprueft(array $operand): bool
    {
        if (($operand['is_assumption'] ?? false) === true) {
            return true;
        }

        $status = $operand['status'] ?? null;
        if ($status !== null && $status !== '' && ! in_array((string) $status, self::FREIGEGEBEN_STATUS, true)) {
            return true;
        }

        // Vererbung: berechneter Operand mit ungeprüftem Eigen-Status.
        if (($operand['is_calculated'] ?? false) === true && (string) $status === self::STATUS_UNGEPRUEFT) {
            return true;
        }

        return false;
    }

    // ── RPN-Auswertung (Shunting-Yard) ───────────────────────────────────────

    /**
     * Wertet die Tokenliste aus. Nutzt Shunting-Yard zur RPN-Umwandlung und
     * einen Stack-Evaluator. Wirft bei strukturellen Fehlern eine Exception
     * (vom Aufrufer als 'unvollstaendig' behandelt). Liefert null bei
     * Division durch 0.
     *
     * WIEDERHOLGRUPPEN: Jeder Stack-Eintrag ist eine „Wertzelle"
     * ['wert' => float, 'liste' => float[]]. Skalare Operatoren rechnen mit dem
     * Einzelwert; SUM summiert über die LISTEN aller Argumente.
     *
     * @param array<int, array{typ:string,wert:string}>                   $tokens
     * @param array<string, array{wert: float, liste: array<int, float>}> $werte
     */
    private function werteAus(array $tokens, array $werte): ?float
    {
        $rpn = $this->toRpn($tokens);
        $stack = [];

        foreach ($rpn as $t) {
            switch ($t['typ']) {
                case 'num':
                    $stack[] = $this->skalarZelle((float) $t['wert']);
                    break;

                case 'ident':
                    if (! array_key_exists($t['wert'], $werte)) {
                        throw new \RuntimeException("Operand {$t['wert']} ohne Wert.");
                    }
                    $stack[] = $werte[$t['wert']];
                    break;

                case 'op':
                    if (count($stack) < 2) {
                        throw new \RuntimeException('Operator ohne genügend Operanden.');
                    }
                    $b = array_pop($stack);
                    $a = array_pop($stack);
                    $r = $this->wendeOperatorAn($t['wert'], $a['wert'], $b['wert']);
                    if ($r === null) {
                        return null; // z. B. Division durch 0
                    }
                    $stack[] = $this->skalarZelle($r);
                    break;

                case 'func':
                    $argc = (int) $t['argc'];
                    if (count($stack) < $argc) {
                        throw new \RuntimeException("Funktion {$t['wert']} ohne genügend Argumente.");
                    }
                    $args = [];
                    for ($k = 0; $k < $argc; $k++) {
                        array_unshift($args, array_pop($stack));
                    }
                    $stack[] = $this->skalarZelle($this->wendeFunktionAn($t['wert'], $args));
                    break;

                default:
                    throw new \RuntimeException("Unerwartetes Token im RPN: {$t['typ']}.");
            }
        }

        if (count($stack) !== 1) {
            throw new \RuntimeException('Formel ist unvollständig oder fehlerhaft.');
        }

        return (float) $stack[0]['wert'];
    }

    /**
     * Hüllt einen Skalar in eine 1-elementige Wertzelle (Literal/Zwischenergebnis).
     *
     * @return array{wert: float, liste: array<int, float>}
     */
    private function skalarZelle(float $wert): array
    {
        return ['wert' => $wert, 'liste' => [$wert]];
    }

    /**
     * Shunting-Yard: Infix-Tokens → RPN. Behandelt Operatorrang, Klammern sowie
     * Funktionen mit kommagetrennten Argumenten.
     *
     * @param array<int, array{typ:string,wert:string}> $tokens
     *
     * @return array<int, array<string, mixed>>
     */
    private function toRpn(array $tokens): array
    {
        $output = [];
        $stack = [];      // Operator-/Klammer-/Funktions-Stack
        $funcStack = [];  // je offener Funktion: ['argc' => int, 'hatArg' => bool]

        $praezedenz = ['+' => 1, '-' => 1, '*' => 2, '/' => 2];

        foreach ($tokens as $idx => $t) {
            switch ($t['typ']) {
                case 'num':
                case 'ident':
                    $output[] = $t;
                    $this->markiereArgument($funcStack);
                    break;

                case 'func':
                    $stack[] = $t;
                    break;

                case 'comma':
                    while ($stack !== [] && end($stack)['typ'] !== 'lparen') {
                        $output[] = array_pop($stack);
                    }
                    if ($stack === [] || $funcStack === []) {
                        throw new \RuntimeException('Komma außerhalb von Funktionsargumenten.');
                    }
                    $funcStack[count($funcStack) - 1]['argc']++;
                    $funcStack[count($funcStack) - 1]['hatArg'] = false;
                    break;

                case 'op':
                    // Unäres Minus/Plus als (0 op x) abbilden.
                    $prev = $idx > 0 ? $tokens[$idx - 1] : null;
                    $istUnaer = $prev === null
                        || in_array($prev['typ'], ['op', 'lparen', 'comma', 'func'], true);
                    if ($istUnaer && in_array($t['wert'], ['+', '-'], true)) {
                        $output[] = ['typ' => 'num', 'wert' => '0'];
                        $this->markiereArgument($funcStack);
                    }
                    while (
                        $stack !== []
                        && end($stack)['typ'] === 'op'
                        && $praezedenz[end($stack)['wert']] >= $praezedenz[$t['wert']]
                    ) {
                        $output[] = array_pop($stack);
                    }
                    $stack[] = $t;
                    break;

                case 'lparen':
                    // Gehört diese Klammer zu einer Funktion (Token direkt davor)?
                    $istFunktionsklammer = $stack !== [] && end($stack)['typ'] === 'func';
                    $stack[] = $t;
                    if ($istFunktionsklammer) {
                        $funcStack[] = ['argc' => 1, 'hatArg' => false];
                    } else {
                        // Normale Gruppierungsklammer: kein eigener Argumentkontext.
                        $funcStack[] = ['argc' => 0, 'hatArg' => false, 'gruppe' => true];
                    }
                    break;

                case 'rparen':
                    while ($stack !== [] && end($stack)['typ'] !== 'lparen') {
                        $output[] = array_pop($stack);
                    }
                    if ($stack === []) {
                        throw new \RuntimeException('Unbalancierte Klammern.');
                    }
                    array_pop($stack); // lparen entfernen
                    $kontext = array_pop($funcStack);
                    // Folgt eine Funktion direkt unter der Klammer → mit Argumentzahl ausgeben.
                    if ($stack !== [] && end($stack)['typ'] === 'func') {
                        $func = array_pop($stack);
                        // Leere Argumentliste FUNC() → 0 Argumente.
                        $func['argc'] = ($kontext['hatArg'] ?? false) ? ($kontext['argc'] ?? 1) : 0;
                        $output[] = $func;
                    }
                    break;

                default:
                    throw new \RuntimeException("Unbekanntes Token: {$t['typ']}.");
            }
        }

        while ($stack !== []) {
            $top = array_pop($stack);
            if (in_array($top['typ'], ['lparen', 'rparen'], true)) {
                throw new \RuntimeException('Unbalancierte Klammern am Ende.');
            }
            $output[] = $top;
        }

        return $output;
    }

    /**
     * Markiert im obersten Funktions-/Gruppenkontext, dass mindestens ein Wert
     * vorliegt (für die Unterscheidung FUNC() ohne Argumente vs. FUNC(x)).
     *
     * @param array<int, array<string, mixed>> $funcStack
     */
    private function markiereArgument(array &$funcStack): void
    {
        if ($funcStack !== []) {
            $funcStack[count($funcStack) - 1]['hatArg'] = true;
        }
    }

    private function wendeOperatorAn(string $op, float $a, float $b): ?float
    {
        return match ($op) {
            '+' => $a + $b,
            '-' => $a - $b,
            '*' => $a * $b,
            '/' => $b == 0.0 ? null : $a / $b,
            default => throw new \RuntimeException("Unerlaubter Operator: {$op}."),
        };
    }

    /**
     * Wendet eine Whitelist-Funktion auf ihre Argumente an. Jedes Argument ist eine
     * Wertzelle ['wert' => float, 'liste' => float[]].
     *
     * - SUM ist eine AGGREGATFUNKTION: sie summiert über die vollständigen LISTEN
     *   aller Argumente (Wiederholgruppen). SUM(slug) = Summe aller Wiederholzeilen.
     * - MENGE/FLAECHE/VOLUMEN sind skalare Funktionen: sie rechnen mit dem Einzel-
     *   wert (erste Zeile) jedes Arguments.
     *
     * @param array<int, array{wert: float, liste: array<int, float>}> $args
     */
    private function wendeFunktionAn(string $func, array $args): float
    {
        return match ($func) {
            'SUM'     => $this->summiereListen($args),
            'MENGE'   => (float) ($args[0]['wert'] ?? 0.0),
            'FLAECHE' => (float) ($args[0]['wert'] ?? 0.0) * (float) ($args[1]['wert'] ?? 0.0),
            'VOLUMEN' => (float) ($args[0]['wert'] ?? 0.0) * (float) ($args[1]['wert'] ?? 0.0) * (float) ($args[2]['wert'] ?? 0.0),
            default   => throw new \RuntimeException("Unerlaubte Funktion: {$func}."),
        };
    }

    /**
     * Summiert die LISTEN aller Argumente (Aggregation über Wiederholgruppen).
     *
     * @param array<int, array{wert: float, liste: array<int, float>}> $args
     */
    private function summiereListen(array $args): float
    {
        $summe = 0.0;
        foreach ($args as $arg) {
            $liste = $arg['liste'] ?? [$arg['wert'] ?? 0.0];
            foreach ($liste as $wert) {
                $summe += (float) $wert;
            }
        }

        return $summe;
    }

    // ── Runden + Grenzen ─────────────────────────────────────────────────────

    /**
     * @param array<string, mixed> $field
     */
    private function runde(float $wert, array $field): float
    {
        $decimals = isset($field['decimals']) && $field['decimals'] !== null ? (int) $field['decimals'] : 2;
        if ($decimals < 0) {
            $decimals = 0;
        }

        $modus = isset($field['rundung']) && is_string($field['rundung']) ? $field['rundung'] : 'kaufmaennisch';

        return match ($modus) {
            'auf', 'aufrunden'   => $this->rundeRichtung($wert, $decimals, true),
            'ab', 'abrunden'     => $this->rundeRichtung($wert, $decimals, false),
            default              => round($wert, $decimals),
        };
    }

    private function rundeRichtung(float $wert, int $decimals, bool $aufwaerts): float
    {
        $faktor = 10 ** $decimals;
        if ($aufwaerts) {
            return ceil($wert * $faktor) / $faktor;
        }

        return floor($wert * $faktor) / $faktor;
    }

    /**
     * @param array<string, mixed> $field
     * @param array<string, mixed> $protokoll
     */
    private function pruefeGrenzen(float $wert, array $field, array &$protokoll): void
    {
        $min = $field['min_value'] ?? null;
        $max = $field['max_value'] ?? null;

        if ($min !== null && $wert < (float) $min) {
            $protokoll['hinweise'][] = "Ergebnis {$wert} unterschreitet das Minimum {$min}.";
            $protokoll['grenze_verletzt'] = 'min';
        }
        if ($max !== null && $wert > (float) $max) {
            $protokoll['hinweise'][] = "Ergebnis {$wert} überschreitet das Maximum {$max}.";
            $protokoll['grenze_verletzt'] = 'max';
        }
    }

    /**
     * @param array<int, string>   $fehlliste
     * @param array<string, mixed> $protokoll
     *
     * @return array{
     *     status: string,
     *     value: float|null,
     *     verbindlich: bool,
     *     fehlliste: array<int, string>,
     *     protokoll: array<string, mixed>
     * }
     */
    private function ergebnis(?float $wert, string $status, bool $verbindlich, array $fehlliste, array $protokoll): array
    {
        $protokoll['status'] = $status;
        $protokoll['calculated_at'] = (new \DateTimeImmutable('now'))->format(\DateTimeInterface::ATOM);

        return [
            'status'      => $status,
            'value'       => $wert,
            'verbindlich' => $verbindlich,
            'fehlliste'   => array_values($fehlliste),
            'protokoll'   => $protokoll,
        ];
    }
}
