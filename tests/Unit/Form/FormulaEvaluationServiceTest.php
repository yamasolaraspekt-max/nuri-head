<?php

namespace Tests\Unit\Form;

use App\Services\Form\FormulaEvaluationService;
use PHPUnit\Framework\TestCase;

/**
 * FS-03 — Paritäts-/Fach- und Injection-Negativtests der eval-freien Formel-Engine.
 *
 * Framework-neutral (kein DB-/Laravel-Bootstrap nötig): der Service ist ein reiner
 * Kern mit Ein-/Ausgabe-Vertrag. Beweist u. a., dass nicht-whitelistete Zeichen und
 * unbekannte Bezeichner geparst-und-abgelehnt werden — NIE ausgeführt.
 */
class FormulaEvaluationServiceTest extends TestCase
{
    private function svc(): FormulaEvaluationService
    {
        return new FormulaEvaluationService();
    }

    // ── Fachfälle ────────────────────────────────────────────────────────────

    public function test_flaeche_rechnet_laenge_mal_breite(): void
    {
        $r = $this->svc()->evaluate('FLAECHE(laenge, breite)', [
            'laenge' => ['value' => 4.0],
            'breite' => ['value' => 5.0],
        ], ['unit' => 'm2']);

        $this->assertSame(FormulaEvaluationService::STATUS_VOLLSTAENDIG, $r['status']);
        $this->assertSame(20.0, $r['value']);
        $this->assertTrue($r['verbindlich']);
        $this->assertSame([], $r['fehlliste']);
    }

    public function test_volumen_und_grundrechenarten(): void
    {
        $r = $this->svc()->evaluate('VOLUMEN(a,b,h)', [
            'a' => ['value' => 2.0], 'b' => ['value' => 3.0], 'h' => ['value' => 4.0],
        ]);
        $this->assertSame(24.0, $r['value']);

        $r2 = $this->svc()->evaluate('(a + b) * 2 - h / 2', [
            'a' => ['value' => 10.0], 'b' => ['value' => 5.0], 'h' => ['value' => 4.0],
        ], ['decimals' => 2]);
        // (10+5)*2 - 4/2 = 30 - 2 = 28
        $this->assertSame(28.0, $r2['value']);
    }

    public function test_sum_aggregiert_wiederholzeilen(): void
    {
        $r = $this->svc()->evaluate('SUM(pos)', [
            'pos' => ['value' => [10.0, 20.0, 30.0]],
        ]);
        $this->assertSame(FormulaEvaluationService::STATUS_VOLLSTAENDIG, $r['status']);
        $this->assertSame(60.0, $r['value']);
    }

    public function test_sum_mehrere_argumente(): void
    {
        $r = $this->svc()->evaluate('SUM(a, b)', [
            'a' => ['value' => [1.0, 2.0]],
            'b' => ['value' => 3.0],
        ]);
        $this->assertSame(6.0, $r['value']);
    }

    public function test_menge_reicht_ersten_wert_durch(): void
    {
        $r = $this->svc()->evaluate('MENGE(x)', ['x' => ['value' => 7.0]]);
        $this->assertSame(7.0, $r['value']);
    }

    public function test_unaeres_minus(): void
    {
        $r = $this->svc()->evaluate('-a + 10', ['a' => ['value' => 3.0]]);
        $this->assertSame(7.0, $r['value']);
    }

    // ── Einheiten-Umrechnung ─────────────────────────────────────────────────

    public function test_einheit_wird_in_zieleinheit_umgerechnet(): void
    {
        // 100 cm → Ziel m ⇒ 1.0
        $r = $this->svc()->evaluate('laenge', [
            'laenge' => ['value' => 100.0, 'unit' => 'cm'],
        ], ['unit' => 'm']);
        $this->assertSame(1.0, $r['value']);
    }

    public function test_dimensionsfremde_einheit_nutzt_rohwert_mit_warnung(): void
    {
        $r = $this->svc()->evaluate('a', [
            'a' => ['value' => 5.0, 'unit' => 'kg'],
        ], ['unit' => 'm']);
        // Rohwert bleibt 5, aber Protokoll trägt Warnung.
        $this->assertSame(5.0, $r['value']);
        $treffer = array_filter($r['protokoll']['hinweise'], fn ($h) => str_contains($h, 'nicht in m umrechenbar'));
        $this->assertNotEmpty($treffer);
    }

    // ── Rundung ──────────────────────────────────────────────────────────────

    public function test_rundung_kaufmaennisch_auf_ab(): void
    {
        $ops = ['a' => ['value' => 10.0], 'b' => ['value' => 3.0]];

        $kfm = $this->svc()->evaluate('a / b', $ops, ['decimals' => 2]);
        $this->assertEqualsWithDelta(3.33, $kfm['value'], 0.0001);

        $auf = $this->svc()->evaluate('a / b', $ops, ['decimals' => 1, 'rundung' => 'auf']);
        $this->assertEqualsWithDelta(3.4, $auf['value'], 0.0001);

        $ab = $this->svc()->evaluate('a / b', $ops, ['decimals' => 1, 'rundung' => 'ab']);
        $this->assertEqualsWithDelta(3.3, $ab['value'], 0.0001);
    }

    // ── Division durch 0 ─────────────────────────────────────────────────────

    public function test_division_durch_null_liefert_keine_zahl(): void
    {
        $r = $this->svc()->evaluate('a / b', [
            'a' => ['value' => 10.0], 'b' => ['value' => 0.0],
        ]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNVOLLSTAENDIG, $r['status']);
        $this->assertNull($r['value']);
    }

    // ── Operanden-Gate ───────────────────────────────────────────────────────

    public function test_fehlender_pflichtoperand_ist_unvollstaendig_mit_fehlliste(): void
    {
        $r = $this->svc()->evaluate('a * b', [
            'a' => ['value' => 5.0],
            // b fehlt komplett
        ]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNVOLLSTAENDIG, $r['status']);
        $this->assertNull($r['value']);
        $this->assertFalse($r['verbindlich']);
        $this->assertContains('b', $r['fehlliste']);
    }

    public function test_operand_mit_nullwert_ist_unvollstaendig(): void
    {
        $r = $this->svc()->evaluate('a * b', [
            'a' => ['value' => 5.0],
            'b' => ['value' => null],
        ]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNVOLLSTAENDIG, $r['status']);
        $this->assertContains('b', $r['fehlliste']);
        $this->assertNull($r['value']);
    }

    public function test_annahme_operand_ist_ungeprueft_und_nicht_verbindlich(): void
    {
        $r = $this->svc()->evaluate('a * b', [
            'a' => ['value' => 5.0],
            'b' => ['value' => 4.0, 'is_assumption' => true],
        ]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNGEPRUEFT, $r['status']);
        $this->assertSame(20.0, $r['value']); // berechnet …
        $this->assertFalse($r['verbindlich']); // … aber nicht verbindlich
    }

    public function test_operand_mit_ungeprueftem_status_ist_nicht_verbindlich(): void
    {
        $r = $this->svc()->evaluate('a', [
            'a' => ['value' => 5.0, 'status' => 'entwurf'],
        ]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNGEPRUEFT, $r['status']);
        $this->assertFalse($r['verbindlich']);
    }

    public function test_operand_mit_freigegebenem_status_ist_verbindlich(): void
    {
        foreach (['geprueft', 'bestaetigt', 'freigegeben'] as $status) {
            $r = $this->svc()->evaluate('a', [
                'a' => ['value' => 5.0, 'status' => $status],
            ]);
            $this->assertSame(FormulaEvaluationService::STATUS_VOLLSTAENDIG, $r['status'], "Status {$status}");
            $this->assertTrue($r['verbindlich']);
        }
    }

    public function test_berechneter_operand_mit_ungepruefter_kette_vererbt(): void
    {
        $r = $this->svc()->evaluate('a + 1', [
            'a' => ['value' => 5.0, 'is_calculated' => true, 'status' => FormulaEvaluationService::STATUS_UNGEPRUEFT],
        ]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNGEPRUEFT, $r['status']);
        $this->assertFalse($r['verbindlich']);
    }

    // ── min/max nur Warnung ──────────────────────────────────────────────────

    public function test_min_max_ist_nur_warnung_kein_abbruch(): void
    {
        $r = $this->svc()->evaluate('a', [
            'a' => ['value' => 100.0],
        ], ['max_value' => 50.0]);
        $this->assertSame(FormulaEvaluationService::STATUS_VOLLSTAENDIG, $r['status']);
        $this->assertSame(100.0, $r['value']); // Wert bleibt, kein Abbruch
        $this->assertSame('max', $r['protokoll']['grenze_verletzt']);
    }

    // ── DoS-Grenzen ──────────────────────────────────────────────────────────

    public function test_ueberlange_formel_wird_abgelehnt(): void
    {
        $lang = str_repeat('a+', 1200) . 'a'; // > 2000 Zeichen
        $r = $this->svc()->evaluate($lang, ['a' => ['value' => 1.0]]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNVOLLSTAENDIG, $r['status']);
        $this->assertNull($r['value']);
    }

    public function test_zu_tiefe_klammerung_wird_abgelehnt(): void
    {
        $formel = str_repeat('(', 40) . 'a' . str_repeat(')', 40);
        $r = $this->svc()->evaluate($formel, ['a' => ['value' => 1.0]]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNVOLLSTAENDIG, $r['status']);
        $this->assertNull($r['value']);
    }

    public function test_leere_formel_ist_unvollstaendig(): void
    {
        $r = $this->svc()->evaluate('   ', ['a' => ['value' => 1.0]]);
        $this->assertSame(FormulaEvaluationService::STATUS_UNVOLLSTAENDIG, $r['status']);
        $this->assertNull($r['value']);
    }

    // ── INJECTION-NEGATIVTESTS (docs/formular-sicherheitsbefund.md) ───────────

    /**
     * Jeder dieser Strings darf NIE ausgeführt werden und NIE eine Zahl aus dem
     * Injection-Teil liefern. Erwartung: Status 'unvollstaendig', value === null,
     * kein Seiteneffekt. Bei whitelisteten Zeichen mit unbekanntem Bezeichner
     * (z. B. alert) landet der Bezeichner in der Fehlliste — als Daten, nie als Code.
     *
     * @dataProvider injectionProvider
     */
    public function test_injection_wird_geparst_und_abgelehnt(string $formel): void
    {
        // Operanden mit gleichnamigen Slugs bereitstellen wäre der Worst Case;
        // wir stellen bewusst KEINE bereit — unbekannte Bezeichner müssen scheitern.
        $r = $this->svc()->evaluate($formel, []);

        $this->assertSame(
            FormulaEvaluationService::STATUS_UNVOLLSTAENDIG,
            $r['status'],
            "Injection '{$formel}' hätte abgelehnt werden müssen."
        );
        $this->assertNull($r['value'], "Injection '{$formel}' hat eine Zahl geliefert.");
        $this->assertFalse($r['verbindlich']);
    }

    /**
     * @return array<int, array{0: string}>
     */
    public static function injectionProvider(): array
    {
        return [
            ['alert(1)'],
            ["this.constructor.constructor('return 1')()"],
            ['__proto__'],
            ['1;DROP TABLE users'],
            ['${7*7}'],
            ["require('fs')"],
            ['`whoami`'],
            ['() => 1'],
            ['a => a'],
            ['process.env'],
            ['window["x"]'],
            ['eval("2+2")'],
            ['0x1F'],            // Hex → x ist Bezeichnerteil, gesamt kein Operand
            ['a && b'],
            ['a | b'],
            ['a == b'],
            ['a["k"]'],
            ['\\x41'],
            ['system("ls")'],
            ['#{7*7}'],
        ];
    }

    public function test_unbekannter_bezeichner_landet_in_fehlliste_nie_als_code(): void
    {
        // alert(1): 'alert' ist ein Bezeichner (kein Operand) → Fehlliste, keine Zahl.
        $r = $this->svc()->evaluate('alert(1)', []);
        $this->assertSame(FormulaEvaluationService::STATUS_UNVOLLSTAENDIG, $r['status']);
        $this->assertNull($r['value']);
        $this->assertContains('alert', $r['fehlliste']);
    }

    public function test_unbekannter_bezeichner_der_wie_operand_aussieht_bleibt_datenreferenz(): void
    {
        // Selbst wenn ein Operand 'alert' existiert: er ist ein DATENwert, kein Code.
        $r = $this->svc()->evaluate('alert', ['alert' => ['value' => 42.0]]);
        $this->assertSame(FormulaEvaluationService::STATUS_VOLLSTAENDIG, $r['status']);
        $this->assertSame(42.0, $r['value']); // 42, NICHT das Resultat eines alert()-Aufrufs
    }
}
