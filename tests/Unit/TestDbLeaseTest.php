<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\TestDbLease;

/**
 * **Z0-I1-9, ausgelöst — und ausschliesslich unter `TMPDIR`.**
 *
 * Diese Datei legt Leases an und wieder ab. Liefe sie gegen die gemeinsame Steuerungsablage,
 * sperrte sie im Fehlerfall den Prüfstand aller Rollen aus — *eine Probe, die im Fehlerfall selbst
 * Schaden anrichtet, ist keine Probe.* Deshalb setzt jeder Fall `TESTDB_LEASE_WURZEL` auf ein
 * eigenes Wegwerf-Verzeichnis und räumt es hinterher weg.
 *
 * Erbt von `PHPUnit\Framework\TestCase`: keine Anwendung, keine Datenbank. *Eine Zusage über die
 * Serialisierung der Datenbank, die selbst eine Datenbank öffnet, prüft ihre Voraussetzung mit.*
 */
final class TestDbLeaseTest extends TestCase
{
    private string $wurzel = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->wurzel = sys_get_temp_dir().'/z0i1-lease-'.bin2hex(random_bytes(6));
        mkdir($this->wurzel, 0o755, true);
        putenv('TESTDB_LEASE_WURZEL='.$this->wurzel);
    }

    protected function tearDown(): void
    {
        TestDbLease::freigeben();
        putenv('TESTDB_LEASE_WURZEL');
        // Wegraeumen — nur unterhalb von TMPDIR, und das wird geprueft statt angenommen.
        if (str_starts_with($this->wurzel, sys_get_temp_dir().'/z0i1-lease-')) {
            exec('rm -rf '.escapeshellarg($this->wurzel));
        }
        parent::tearDown();
    }

    private function schreibeFremdeLease(string $heartbeat, int $token = 7): void
    {
        $o = $this->wurzel.'/TESTDB-ticket_testing/active';
        if (! is_dir($o)) {
            mkdir($o, 0o755, true);
        }
        file_put_contents($o.'/lease.yaml',
            "ressource: ticket_testing\nfencing_token: {$token}\nrolle: evaluator\n"
            ."owner: fremde-sitzung\nheartbeat_bis: \"{$heartbeat}\"\n");
    }

    public function test_z0i1_9_die_erste_lease_wird_erteilt_und_traegt_v2_paragraf_8(): void
    {
        $token = TestDbLease::ziehen('ticket_testing', 'generator', 'meine-sitzung');

        $this->assertSame(1, $token);
        $datei = $this->wurzel.'/TESTDB-ticket_testing/active/lease.yaml';
        $this->assertFileExists($datei);
        $inhalt = (string) file_get_contents($datei);
        foreach (['fencing_token: 1', 'heartbeat_bis:', 'owner: meine-sitzung', 'rolle: generator'] as $feld) {
            $this->assertStringContainsString($feld, $inhalt, "V2 §8 verlangt {$feld}");
        }
        $this->assertSame('1', trim((string) file_get_contents($this->wurzel.'/TESTDB-ticket_testing/counter')));
    }

    /** **Der Kern:** eine gültige fremde Lease lässt den zweiten Lauf NICHT mitlaufen. */
    public function test_z0i1_9_ein_zweiter_lauf_bricht_ab_und_nennt_halter_und_token(): void
    {
        $this->schreibeFremdeLease(date('Y-m-d\TH:i:sO', time() + 600), 7);

        try {
            TestDbLease::ziehen('ticket_testing', 'generator', 'meine-sitzung');
            $this->fail('Der zweite Lauf lief mit — genau der Vorfall von 16:00:33.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Z0-I1-9', $e->getMessage());
            $this->assertStringContainsString('evaluator', $e->getMessage());    // Halter
            $this->assertStringContainsString('7', $e->getMessage());            // fencing_token
            $this->assertStringContainsString('laeuft NICHT mit', $e->getMessage());
        }
    }

    /**
     * **Die Absage-Regel, als Zusage:** eine Lease mit abgelaufenem `heartbeat_bis` ist verfallen.
     * *Ohne diesen Fall wäre ein abgestürzter Lauf eine Dauersperre — und der Riegel schlimmer als
     * das Problem.*
     */
    public function test_z0i1_9_eine_verfallene_lease_ist_keine_dauersperre(): void
    {
        $this->schreibeFremdeLease(date('Y-m-d\TH:i:sO', time() - 60), 7);

        $this->assertNull(TestDbLease::fremderHalter('ticket_testing'), 'verfallen muss frei heissen');
        $token = TestDbLease::ziehen('ticket_testing', 'generator', 'meine-sitzung');
        // Der Token zaehlt weiter — er wird NIE wiederverwendet.
        $this->assertGreaterThan(0, $token);
    }

    /** Eine Datei OHNE `heartbeat_bis` ist keine gueltige Lease und darf nicht sperren. */
    public function test_z0i1_9_eine_sperrdatei_ohne_heartbeat_sperrt_nicht(): void
    {
        $o = $this->wurzel.'/TESTDB-ticket_testing/active';
        if (! is_dir($o)) {
            mkdir($o, 0o755, true);
        }
        file_put_contents($o.'/lease.yaml', "ressource: ticket_testing\nfencing_token: 3\n");

        $this->assertNull(TestDbLease::fremderHalter('ticket_testing'));
        $this->assertGreaterThan(0, TestDbLease::ziehen('ticket_testing', 'generator', 'meine-sitzung'));
    }

    public function test_z0i1_9_freigeben_legt_die_lease_ab_und_gibt_den_token_nicht_zurueck(): void
    {
        TestDbLease::ziehen('ticket_testing', 'generator', 'meine-sitzung');
        TestDbLease::freigeben();

        $this->assertFileDoesNotExist($this->wurzel.'/TESTDB-ticket_testing/active/lease.yaml');
        $this->assertCount(1, glob($this->wurzel.'/TESTDB-ticket_testing/freigegeben/*.yaml') ?: []);
        // counter zaehlt VERGABEN, nicht Rueckgaben: die naechste Lease bekommt 2, nicht wieder 1.
        $this->assertSame(2, TestDbLease::ziehen('ticket_testing', 'generator', 'meine-sitzung'));
    }

    /** Ohne Ablage wird nicht stillschweigend weitergelaufen. */
    public function test_z0i1_9_ohne_wurzel_faellt_es_geschlossen_aus(): void
    {
        putenv('TESTDB_LEASE_WURZEL');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/TESTDB_LEASE_WURZEL/');
        TestDbLease::ziehen('ticket_testing', 'generator', 'meine-sitzung');
    }
}
