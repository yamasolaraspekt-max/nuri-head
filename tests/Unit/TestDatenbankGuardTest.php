<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;
use Tests\TestDatenbankGuard;

/**
 * **Z0-I1-1/-2: der Riegel wird AUSGELÖST, nicht behauptet.**
 *
 * ---
 *
 * **Warum ohne `RefreshDatabase`:** dieser Test stellt die Verbindung absichtlich auf eine
 * *andere* Datenbank. Liefe `RefreshDatabase` mit, migrierte er genau dorthin — der Test würde
 * anrichten, wogegen er schützt. **Er schreibt nichts; er fragt nur.**
 *
 * **Warum `ticket_g1b1_testing` und nicht `ticket`:** die Wirkung ist dieselbe (ein Name, der
 * nicht `ticket_testing` ist), aber der Weg dorthin berührt keine Produktivdaten. *Die
 * Produktions-Probe steht darunter und ist so gebaut, dass sie auch dann nichts anfasst, wenn der
 * Riegel fiele* — sie ruft den Guard, nicht die Migration.
 */
final class TestDatenbankGuardTest extends TestCase
{
    /** Stellt die Verbindung auf einen anderen Namen um — ohne zu schreiben. */
    private function verbindeMit(string $db): void
    {
        DB::purge('mysql');
        config(['database.connections.mysql.database' => $db]);
        DB::reconnect('mysql');
    }

    protected function tearDown(): void
    {
        // Die Verbindung MUSS zurück, sonst erbt der nächste Test eine fremde Datenbank.
        DB::purge('mysql');
        config(['database.connections.mysql.database' => TestDatenbankGuard::ERLAUBT]);
        DB::reconnect('mysql');
        parent::tearDown();
    }

    public function test_z0i1_1_die_erlaubte_datenbank_kommt_durch_und_wird_zurueckgegeben(): void
    {
        $name = TestDatenbankGuard::pruefeVerbindung();

        // Der Rückgabewert ist der Beleg aus Z0-I1-10 — die TATSACHE, nicht die Konfiguration.
        $this->assertSame(TestDatenbankGuard::ERLAUBT, $name);
    }

    public function test_z0i1_2_eine_fremde_testdatenbank_wird_abgewiesen_und_der_name_steht_in_der_meldung(): void
    {
        $this->verbindeMit('ticket_g1b1_testing');

        try {
            TestDatenbankGuard::pruefeVerbindung();
            $this->fail('Der Guard liess eine fremde Datenbank durch — dann schuetzt er nichts.');
        } catch (RuntimeException $e) {
            // Die Meldung muss den GEFUNDENEN Namen nennen. Wer nur „falsche Datenbank" liest,
            // weiss nicht, welche er getroffen hat.
            $this->assertStringContainsString('ticket_g1b1_testing', $e->getMessage());
            $this->assertStringContainsString(TestDatenbankGuard::ERLAUBT, $e->getMessage());
            $this->assertStringContainsString('VOR dem ersten Schreibzugriff', $e->getMessage());
        }
    }

    /**
     * **Die Produktions-Probe, und sie ist der Kern von Z0-I1-2.**
     *
     * Sie ruft den Guard gegen `ticket` — und **nur** den Guard. Selbst wenn der Riegel fiele,
     * würde hier nichts geschrieben: es gibt kein `RefreshDatabase`, keine Migration, kein Seed.
     * *Eine Schutzprobe, die im Fehlerfall selbst Schaden anrichtet, ist keine Probe.*
     */
    public function test_z0i1_2_die_produktionsdatenbank_wird_abgewiesen(): void
    {
        $this->verbindeMit('ticket');

        try {
            TestDatenbankGuard::pruefeVerbindung();
            $this->fail('Der Guard liess die PRODUKTIONSDATENBANK durch.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString("'ticket'", $e->getMessage());
            $this->assertStringContainsString('ABBRUCH', $e->getMessage());
        }
    }

    /**
     * **`ticket_testing_kopie` trägt dieselben Daten wie das Original** — ein Präfix-Vergleich
     * liesse sie durch, ein exakter nicht.
     *
     * ⚠ **Meine erste Fassung dieses Tests war wertlos:** sie prüfte
     * `str_starts_with($fast, ERLAUBT) && $fast === ERLAUBT` — ein Ausdruck, der für jeden
     * abweichenden Namen zwangsläufig `false` ist. *Grün, ohne je etwas geprüft zu haben.*
     * Dieselbe Fehlerklasse, gegen die dieses ganze Blatt gebaut wird.
     *
     * **Jetzt wird der Quelltext des Guards gemessen:** er darf `!==` benutzen und **kein**
     * `str_starts_with`, `strpos`, `preg_match` oder `LIKE` auf den erlaubten Namen. *Die
     * Auslösung des Vergleichs selbst steht in den beiden Proben darüber — dort mit echten
     * Verbindungen auf `ticket_g1b1_testing` und `ticket`.*
     */
    public function test_z0i1_3_der_vergleich_ist_exakt_kein_praefix_muster(): void
    {
        $quelle = file_get_contents(__DIR__.'/../TestDatenbankGuard.php');
        $this->assertIsString($quelle);

        // Der exakte Vergleich muss da sein.
        $this->assertStringContainsString('$gefunden !== self::ERLAUBT', $quelle);

        // Und kein aufweichender Vergleich daneben. Kommentare zaehlen nicht — sie erklaeren
        // gerade, WARUM kein Muster benutzt wird; ein Griff, der sie mitliest, faende sich selbst.
        $ohneKommentare = preg_replace('#/\*.*?\*/|//[^
]*#s', '', $quelle);
        foreach (['str_starts_with', 'strpos', 'preg_match', 'fnmatch'] as $weich) {
            $this->assertStringNotContainsString(
                $weich,
                (string) $ohneKommentare,
                "Der Guard benutzt {$weich} — ein Muster liesse ticket_testing_kopie durch.",
            );
        }
    }
}
