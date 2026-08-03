<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\TestDatenbank;

/**
 * PB-056 — **die Riegel vor der Test-Datenbank, gemessen statt zugesagt.**
 *
 * Diese Datei erbt bewusst von `PHPUnit\Framework\TestCase` und nicht von `Tests\TestCase`:
 * sie braucht keine Anwendung und keine Datenbank. *Eine Zusage über die Datenbankwahl, die
 * selbst eine Datenbank öffnet, prüft ihre eigene Voraussetzung mit.*
 */
class TestDatenbankTest extends TestCase
{
    public function test_ohne_rolle_bleibt_es_beim_bestand(): void
    {
        // Der Bestand ändert sich nicht: wer nichts setzt, läuft weiter wie bisher.
        $this->assertSame('ticket_testing', TestDatenbank::name(null));
        $this->assertSame('ticket_testing', TestDatenbank::name(''));
        $this->assertSame('ticket_testing', TestDatenbank::name('   '));
    }

    public function test_jede_bekannte_rolle_bekommt_ihre_eigene(): void
    {
        foreach (TestDatenbank::ROLLEN as $rolle) {
            $this->assertSame("ticket_testing_{$rolle}", TestDatenbank::name($rolle));
        }
        // Und sie sind verschieden — sonst wäre die ganze Trennung eine Schreibweise.
        $namen = array_map([TestDatenbank::class, 'name'], TestDatenbank::ROLLEN);
        $this->assertSame(count($namen), count(array_unique($namen)));
    }

    /**
     * **Die tragende Zusage (P1).** Ohne sie ist die Bequemlichkeit die Falle: eine Variable,
     * die jeder setzen kann, entscheidet, welche Datenbank `RefreshDatabase` leerräumt.
     *
     * @dataProvider gefaehrlicheWerte
     */
    public function test_alles_ausser_einer_bekannten_rolle_bricht_ab(string $wert): void
    {
        $this->expectException(RuntimeException::class);
        TestDatenbank::name($wert);
    }

    /** @return array<string, array{string}> */
    public static function gefaehrlicheWerte(): array
    {
        return [
            'Pfad nach oben' => ['../produktion'],
            'die Arbeits-DB' => ['ticket'],
            'absoluter Pfad' => ['/var/lib/mysql/ticket'],
            'Semikolon' => ['generator; DROP DATABASE ticket'],
            'Grossschreibung' => ['Generator'],
            'mit Leerzeichen' => ['gene rator'],
            'leerer Praefix-Trick' => ['_'],
            'Punkt' => ['generator.ticket'],
        ];
    }

    public function test_der_zweite_riegel_sitzt_am_ERGEBNIS_nicht_an_der_eingabe(): void
    {
        // Belegt am Code, weil sich der zweite Riegel heute nicht auslösen lässt: die
        // Rollen-Liste fängt vorher. *Er ist die Reserve für den Tag, an dem jemand die Liste
        // öffnet* — und genau dann ist niemand mehr da, der sich an diesen Grund erinnert.
        $quelle = file_get_contents(__DIR__.'/../TestDatenbank.php');
        $this->assertMatchesRegularExpression(
            '/preg_match\(.*BASIS.*\$name\)/',
            $quelle,
            'der zweite Riegel prüft nicht mehr den fertigen Namen',
        );
    }
}
