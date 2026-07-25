<?php

namespace Tests\Feature\Hausplaner;

use Illuminate\Support\Facades\Blade;
use ParseError;
use Tests\TestCase;

/**
 * AUF-64 — die Hausplaner-Blades muessen sich zu gueltigem PHP kompilieren.
 *
 * **Was passiert war:** `objekt.blade.php` traegt beim Uebernahme-Knopf die **einzeilige**
 * Klammer-Form der PHP-Direktive. Die hat kein schliessendes Gegenstueck. Blade zieht seine
 * Rohbloecke non-greedy heraus (`@php … @endphp`) und tut das, **bevor** es Kommentare entfernt.
 * AUF-60 hat weiter unten einen Block in der Schluesselwort-Form eingefuegt — dessen schliessendes
 * Gegenstueck wurde mit der frueheren einzeiligen Oeffnung gepaart. Alles dazwischen (Formular,
 * CSRF-Direktive, Ausgabe-Klammern) landete als roher PHP-Code im Kompilat; PHP stolperte ueber
 * `class` im `<form>`, und `/admin/hausplaner/objekt/203` war nicht mehr benutzbar.
 *
 * **Warum genau dieser Test:** Der Fehler war im Browser sofort sichtbar und in der gesamten
 * gruenen Gate-Kette unsichtbar — die vier Hausplaner-Gates pruefen TypeScript, Schema, die
 * Insel-Tests und den Bundle-Bau. **Keines davon fasst eine Blade-Datei an.** Dieser Test schliesst
 * genau die Luecke: er kompiliert die Vorlage und laesst PHP selbst urteilen.
 *
 * `token_get_all(..., TOKEN_PARSE)` parst den Code wirklich und wirft bei ungueltiger Syntax einen
 * `ParseError` — ohne die Vorlage auszufuehren und ohne Unterprozess. Kein Datenbankzugriff.
 */
class BladeKompiliertTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function blades(): array
    {
        // Der Provider laeuft, BEVOR die Anwendung hochgefahren ist — `resource_path()` gibt es
        // hier noch nicht. Deshalb der Pfad relativ zu dieser Datei.
        $verzeichnis = dirname(__DIR__, 3) . '/resources/views/admin/hausplaner';

        return collect(glob($verzeichnis . '/*.blade.php'))
            ->mapWithKeys(fn ($pfad) => [basename($pfad) => [$pfad]])
            ->all();
    }

    /** @dataProvider blades */
    public function test_blade_kompiliert_zu_gueltigem_php(string $pfad): void
    {
        $kompiliert = Blade::compileString(file_get_contents($pfad));

        try {
            token_get_all($kompiliert, TOKEN_PARSE);
        } catch (ParseError $e) {
            $this->fail(sprintf(
                "%s kompiliert zu ungueltigem PHP: %s\n"
                . 'Haeufigste Ursache: einzeilige und Block-Form der PHP-Direktive in derselben '
                . 'Datei gemischt — das schliessende Gegenstueck paart sich dann mit der falschen '
                . 'Oeffnung (auch wenn die Marke nur im Kommentartext steht).',
                basename($pfad),
                $e->getMessage(),
            ));
        }

        $this->assertTrue(true, basename($pfad) . ' kompiliert sauber');
    }

    /**
     * Die Ursache selbst, nicht nur ihre Wirkung: solange `objekt.blade.php` die einzeilige Form
     * traegt, darf in derselben Datei kein schliessendes Gegenstueck vorkommen — auch nicht in
     * einem Kommentar, denn die Rohblock-Erkennung laeuft davor.
     */
    public function test_objekt_blade_mischt_die_beiden_php_formen_nicht(): void
    {
        $roh = file_get_contents(resource_path('views/admin/hausplaner/objekt.blade.php'));

        $einzeilig = preg_match_all('/@php\s*\(/', $roh);
        $schliessend = substr_count($roh, '@' . 'endphp');

        $this->assertGreaterThan(0, $einzeilig, 'die einzeilige Form ist die Grundlage dieses Tests');
        $this->assertSame(
            0,
            $schliessend,
            'diese Datei mischt beide Formen — das Kompilat verschluckt dann alles dazwischen',
        );
    }

    /**
     * Der Gegenbeweis: eine Vorlage, die beide Formen mischt, MUSS hier durchfallen. Ohne ihn
     * wuerde ein Test, der nie ausschlaegt, wie ein bestandener aussehen.
     */
    public function test_der_bekannte_fehlerfall_wird_wirklich_erkannt(): void
    {
        $kaputt = "@php(\$a = 1)\n<form class=\"x\">\n</form>\n@php\n\$b = 2;\n@" . "endphp\n";

        $this->expectException(ParseError::class);
        token_get_all(Blade::compileString($kaputt), TOKEN_PARSE);
    }
}
