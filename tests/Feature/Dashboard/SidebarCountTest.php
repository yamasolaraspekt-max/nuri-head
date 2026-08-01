<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PB-047 — die Seitenleisten-Zaehler rissen seit dem 07.07., 464 Mal.
 *
 * -------------------------------------------------------------------------------------------
 * DER BEFUND (Pruefer, aus dem Log gezaehlt)
 *
 *   SidebarCountController:16   $employeeId = $user?->name;                <- Zeichenkette
 *   SidebarCountController:162  countInquiryUnpublished(?int $employeeId)  <- fordert ?int
 *
 * In diesem Projekt traegt `users.name` die `employees.id` — ABER NICHT BEI JEDEM KONTO.
 * Steht dort ein echter Name, ist die Zeichenkette nicht numerisch, PHP kann sie nicht zu
 * `int` machen, und der Aufruf wirft. Dann kommt die GANZE JSON-Antwort nicht zustande —
 * fuer den, der die Seitenleiste ansieht, sind alle Zaehler fort.
 *
 * -------------------------------------------------------------------------------------------
 * WARUM DIESE DATEI ZUERST GESCHRIEBEN UND ROT GEFAHREN WURDE
 *
 * K-03 des Blattes verlangt es ausdruecklich: *"VOR der Aenderung denselben Fall fahren: er MUSS
 * den TypeError zeigen. Zeigt er ihn nicht, ist der Testfall nicht der Fall aus dem Log, und
 * K-03 beweist nichts."*
 *
 * Gemessen am Stand VOR der Behebung:
 *   nicht-numerischer Name   ->  TypeError, HTTP 500
 *   numerischer Name         ->  HTTP 200, Zaehler vollstaendig
 *
 * -------------------------------------------------------------------------------------------
 * WAS HIER BEWUSST NICHT GEPRUEFT WIRD
 *
 * Die 131 anderen Stellen im Bestand (78 stille `(int)`-Casts, 53 rohe Zugriffe). Sie sind
 * derselbe Fehler und haben eigene Blaetter — der Pruefer hat sie gezaehlt, sie sind nicht
 * vergessen.
 */
class SidebarCountTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = '/api/sidebar-counts';

    /**
     * Ein Konto mit dem gewuenschten `name`.
     *
     * **Warum das Kennwort hier neu gesetzt wird:** die Factory traegt einen fest verdrahteten
     * Hash mit Kostenfaktor 10, `phpunit.xml` erzwingt aber `BCRYPT_ROUNDS=4`. Laravel prueft die
     * Konfiguration beim Anmelden und wirft sonst *"Could not verify the hashed value's
     * configuration"* — ein Fehler der Testumgebung, der wie ein Fehler des Endpunkts aussieht.
     * *Beim ersten Lauf ist mir genau das passiert.*
     */
    private function konto(string $name): User
    {
        return User::factory()->create(['name' => $name, 'password' => Hash::make('probe')]);
    }

    /**
     * DER FALL AUS DEM LOG. Ein Konto, dessen `name` ein echter Name ist.
     *
     * Vor der Behebung: TypeError, HTTP 500, keine einzige Zahl.
     */
    public function test_konto_mit_nicht_numerischem_namen_bekommt_zaehler_statt_fehler(): void
    {
        $user = $this->konto('Anna Beispiel');

        $antwort = $this->actingAs($user)->getJson(self::ROUTE);

        $antwort->assertOk();
        $antwort->assertJsonStructure(['counts']);
        $this->assertIsArray(
            $antwort->json('counts'),
            'die Zaehler fehlen ganz — genau der Zustand, den der Befund beschreibt'
        );
    }

    /**
     * **Die eigentliche Aussage von PB-047, und sie ist schaerfer als "kein Fehler mehr".**
     *
     * Der naheliegende Weg waere `(int) $user->name` gewesen. Aus "Anna Beispiel" wuerde damit
     * still die `0`, und die persoenlichen Zaehler zeigten die Posten des Mitarbeiters mit der
     * `id` 0 — **ein falscher Zaehler ist schlimmer als ein leerer, weil er richtig aussieht.**
     */
    public function test_ohne_mitarbeiter_id_bleiben_die_persoenlichen_zaehler_leer_statt_fremd(): void
    {
        $user = $this->konto('Anna Beispiel');

        $counts = $this->actingAs($user)->getJson(self::ROUTE)->json('counts');

        $this->assertNull(
            $user->employeeId(),
            'die Vorbedingung stimmt nicht — dieses Konto hat doch eine Mitarbeiter-id'
        );
        foreach (array_keys($counts) as $feld) {
            if (! str_starts_with($feld, 'my_')) {
                continue;
            }
            $this->assertSame(
                0,
                $counts[$feld],
                "der persoenliche Zaehler `{$feld}` zeigt fremde Posten, obwohl das Konto keine Mitarbeiter-id hat"
            );
        }
    }

    /**
     * DIE REGRESSIONSSCHRANKE (K-04). `employeeId()` muss fuer numerische Namen genau das
     * liefern, was der alte Weg lieferte — gemessen, nicht angenommen.
     */
    public function test_konto_mit_numerischem_namen_zaehlt_unveraendert(): void
    {
        $user = $this->konto('42');

        $antwort = $this->actingAs($user)->getJson(self::ROUTE);

        $antwort->assertOk();
        $this->assertSame(42, $user->employeeId(), 'die eine Wahrheit liefert nicht mehr denselben Wert');
        $this->assertIsArray($antwort->json('counts'));
    }

    /**
     * Die Grenze des Blattes: **kein zweiter Rechenweg** in diesem Controller.
     *
     * Eine eigene `is_numeric`-Pruefung dort waere eine zweite Wahrheit ueber dieselbe Frage —
     * und sie altert getrennt von der in `User::employeeId()`.
     */
    public function test_der_controller_rechnet_die_mitarbeiter_id_nicht_selbst_aus(): void
    {
        $quelle = file_get_contents(app_path('Http/Controllers/Dashboard/SidebarCountController.php'));

        $this->assertStringContainsString('employeeId()', $quelle, 'die vorhandene Wahrheit wird nicht benutzt');
        $this->assertStringNotContainsString('is_numeric', $quelle, 'der Controller rechnet die id ein zweites Mal aus');
        $this->assertDoesNotMatchRegularExpression(
            '/\(int\)\s*\$user/',
            $quelle,
            'ein stiller Cast verschiebt den Fehler, statt ihn zu beheben — aus einem Namen wuerde die id 0'
        );
    }
}
