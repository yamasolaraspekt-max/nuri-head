<?php

namespace Tests\Feature\Product\Identity;

use Tests\TestCase;

/**
 * AUF-P1-S4 · Die Verriegelung (Spez 11-…md §8, Kriterium K-01).
 *
 * Läuft über den QUELLTEXT, nicht über das Verhalten: kein Pfad in app/ oder
 * database/seeders darf Product anlegen, außer dem ProductIdentityService.
 * Kommt eine zehnte Stelle dazu, wird dieser Test rot — das ist der eigentliche
 * Wert von Schritt 4: das Problem kann nicht mehr unbemerkt wachsen.
 */
class ProductIdentityVerriegelungTest extends TestCase
{
    public function test_kein_pfad_legt_produkte_am_identitaetsdienst_vorbei_an(): void
    {
        $treffer = $this->grepProjekt(
            '(Product::(firstOrCreate|updateOrCreate|create)|new\s+Product\s*\()',
            verzeichnisse: ['app', 'database/seeders'],
            ausnahmen: ['app/Services/Product/Identity/ProductIdentityService.php'],
        );

        $this->assertSame([], $treffer,
            "Diese Stellen legen Produkte an, ohne durch ProductIdentityService zu gehen:\n"
            . implode("\n", $treffer));
    }

    /**
     * Zeilenweiser Grep über das Projekt. Liefert "pfad:zeile: inhalt" je Treffer.
     *
     * @param  string  $muster         PCRE-Muster ohne Delimiter
     * @param  array<string>  $verzeichnisse  relativ zu base_path()
     * @param  array<string>  $ausnahmen      Dateipfade relativ zu base_path()
     * @return array<string>
     */
    private function grepProjekt(string $muster, array $verzeichnisse, array $ausnahmen = []): array
    {
        $basis = rtrim(base_path(), '/');
        $treffer = [];

        foreach ($verzeichnisse as $verzeichnis) {
            $wurzel = $basis . '/' . $verzeichnis;

            if (!is_dir($wurzel)) {
                $this->fail("grepProjekt: Verzeichnis fehlt: {$verzeichnis}");
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($wurzel, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $datei) {
                /** @var \SplFileInfo $datei */
                if ($datei->getExtension() !== 'php') {
                    continue;
                }

                $relativ = ltrim(str_replace($basis, '', $datei->getPathname()), '/');

                if (in_array($relativ, $ausnahmen, true)) {
                    continue;
                }

                foreach (file($datei->getPathname()) as $index => $zeile) {
                    if (preg_match('~' . $muster . '~', $zeile)) {
                        $treffer[] = $relativ . ':' . ($index + 1) . ': ' . trim($zeile);
                    }
                }
            }
        }

        sort($treffer);

        return $treffer;
    }
}
