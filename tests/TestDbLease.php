<?php

namespace Tests;

use RuntimeException;

/**
 * **Z0-I1-9 — die Läufe sind serialisiert, über eine Lease nach V2 §8.**
 *
 * ---
 *
 * **Der Anlassfall, und er ist von heute:** um 16:00:33 setzte ein Testlauf `ticket_testing`
 * zurück, während eine Browserabnahme darauf lief, und löschte deren Anmeldenutzer mitten im
 * Bedienweg. *Der Lauf war meiner.* Er verstieß gegen keine Regel — es gab keine, die ihn
 * gehindert hätte. **Das ist der Grund für diese Datei.**
 *
 * **Was der Namens-Guard NICHT kann:** `TestDatenbankGuard` prüft, ob die *richtige* Datenbank
 * getroffen wird. Er sagt nichts darüber, ob sie *gerade jemand anders benutzt*. Beide Riegel sind
 * nötig; der eine schützt vor der falschen Datenbank, der andere vor dem falschen Zeitpunkt.
 *
 * ## Warum eine Lease und keine `.lock`-Datei
 *
 * Das Blatt weist die Datei-Sperre ausdrücklich ab, und die Begründung ist die entscheidende:
 * **ohne `heartbeat_bis` ist ein abgestürzter Lauf eine Dauersperre.** Wer den Riegel als Datei
 * baut, tauscht ein seltenes Problem (gleichzeitige Läufe) gegen ein häufigeres (ein Lauf, der
 * beim Abbruch die Bühne für alle schließt).
 *
 * **Deshalb V2 §8, vollständig:**
 * - `counter` — zählt VERGABEN, nie Rückgaben; jede Lease bekommt einen eigenen `fencing_token`
 * - `counter.lock/` — `mkdir` ist atomar, `test -e` gefolgt von `touch` ist es nicht
 * - `active/lease.yaml` — `fencing_token`, `heartbeat_bis`, `owner`
 * - eine Lease mit abgelaufenem `heartbeat_bis` gilt als **verfallen** und darf übernommen werden
 *
 * **Der Token wird nie wiederverwendet.** Eine zurückgegebene Lease bleibt unter `freigegeben/`
 * stehen; die nächste zieht die nächste Zahl. *So bleibt im Nachhinein lesbar, wer wann hielt.*
 */
final class TestDbLease
{
    /** Wie lange eine Lease gilt, bevor sie als verfallen gilt (Sekunden). */
    public const LAUFZEIT = 3300;

    private static ?string $gehalten = null;
    private static ?int $token = null;

    /**
     * Die Ablage. **Aus der Umgebung, nicht hartcodiert** — die Proben laufen unter `TMPDIR`,
     * der Betrieb gegen die gemeinsame Steuerungsablage, in der alle Rollen ihre Leases führen.
     */
    public static function wurzel(): string
    {
        $w = getenv('TESTDB_LEASE_WURZEL');
        if (! is_string($w) || $w === '') {
            return '';
        }

        // **`~` und `${HOME}` werden HIER aufgeloest, nicht in der XML.** PHPUnit expandiert
        // nichts; wer den Pfad dort absolut schriebe, traeg die Heimatverzeichnis eines einzelnen
        // Rechners in eine versionierte Datei. *Der WEG ist versioniert, der Ort bleibt lokal.*
        $heim = (string) (getenv('HOME') ?: '');
        $w = str_replace(['${HOME}', '$HOME'], $heim, $w);
        if (str_starts_with($w, '~/')) {
            $w = $heim.substr($w, 1);
        }

        return rtrim($w, '/');
    }

    private static function ordner(string $ressource): string
    {
        return self::wurzel().'/TESTDB-'.$ressource;
    }

    /** Liest `heartbeat_bis` aus einer Lease-Datei. Unlesbar ⇒ 0 ⇒ verfallen. */
    private static function gueltigBis(string $datei): int
    {
        $inhalt = is_file($datei) ? file_get_contents($datei) : false;
        if (! is_string($inhalt)) {
            return 0;
        }
        if (preg_match('/^heartbeat_bis:\s*"?([^"\n]+)"?/m', $inhalt, $t) !== 1) {
            // **Ohne `heartbeat_bis` ist es keine gültige Lease nach V2 §8** — und eine, die als
            // Dauersperre wirken könnte, wird nicht respektiert. Genau das verlangt die Absage-Regel.
            return 0;
        }

        return (int) strtotime(trim($t[1]));
    }

    /** Wer hält gerade? Gibt `null` zurück, wenn frei oder verfallen. */
    public static function fremderHalter(string $ressource): ?array
    {
        $datei = self::ordner($ressource).'/active/lease.yaml';
        if (! is_file($datei)) {
            return null;
        }
        if (self::gueltigBis($datei) <= time()) {
            return null;   // verfallen — kein Dauerriegel
        }
        $inhalt = (string) file_get_contents($datei);
        $feld = static function (string $name) use ($inhalt): string {
            return preg_match('/^'.$name.':\s*"?([^"\n]+)"?/m', $inhalt, $t) === 1 ? trim($t[1]) : '?';
        };

        return [
            'fencing_token' => $feld('fencing_token'),
            'rolle' => $feld('rolle'),
            'owner' => $feld('owner'),
            'heartbeat_bis' => $feld('heartbeat_bis'),
        ];
    }

    /**
     * Zieht die Lease oder bricht ab.
     *
     * @throws RuntimeException wenn ein anderer sie gültig hält
     */
    public static function ziehen(string $ressource, string $rolle, string $sitzung): int
    {
        if (self::wurzel() === '') {
            // **Fail closed, aber mit Grund.** Ohne Ablage kann nicht serialisiert werden; das
            // still zu übergehen hiesse, den Riegel zu haben und ihn nicht zu benutzen.
            throw new RuntimeException(
                'Z0-I1-9: TESTDB_LEASE_WURZEL ist nicht gesetzt — ohne Ablage gibt es keine '
                .'Serialisierung. Der Wert steht in phpunit.xml (versioniert, geheimnisfrei).',
            );
        }

        $fremd = self::fremderHalter($ressource);
        if ($fremd !== null) {
            throw new RuntimeException(
                "Z0-I1-9: Die Testdatenbank '{$ressource}' ist belegt — Halter '{$fremd['rolle']}', "
                ."fencing_token {$fremd['fencing_token']}, gueltig bis {$fremd['heartbeat_bis']}. "
                .'Dieser Lauf laeuft NICHT mit; er wuerde die Daten des anderen zuruecksetzen.',
            );
        }

        $ordner = self::ordner($ressource);
        // `is_dir` VOR `mkdir`: das Unterdrueckungszeichen haelt die Warnung nicht von PHPUnit
        // fern, und eine Warnung im Aufbau saehe aus wie ein Mangel am Riegel.
        // Hier ist die Nicht-Atomizitaet unkritisch — es geht um die Ordnerstruktur, nicht um die
        // Sperre. DIE bleibt ein nacktes `mkdir` ohne Vorpruefung, und genau deshalb traegt sie.
        foreach ([$ordner.'/active', $ordner.'/freigegeben'] as $noetig) {
            if (! is_dir($noetig)) {
                mkdir($noetig, 0o755, true);
            }
        }

        // `mkdir` ist atomar — `is_dir` gefolgt von `mkdir` waere es nicht.
        $sperre = $ordner.'/counter.lock';
        $versuche = 0;
        while (! @mkdir($sperre, 0o755)) {
            if (++$versuche > 50) {
                throw new RuntimeException(
                    "Z0-I1-9: Die Vergabesperre {$sperre} ist seit 5 Sekunden belegt. Kein Lauf.",
                );
            }
            usleep(100_000);
        }

        try {
            // Beim ersten Mal gibt es den Zaehler noch nicht. `is_file` statt Unterdrueckung:
            // das `@` haelt die Warnung nicht von PHPUnit fern, und eine Warnung im Normalfall
            // waere ein Rauschen, das echte Warnungen unsichtbar macht.
            $zaehlerDatei = $ordner.'/counter';
            $zaehler = is_file($zaehlerDatei) ? (int) file_get_contents($zaehlerDatei) : 0;
            $token = $zaehler + 1;
            file_put_contents($ordner.'/counter', (string) $token);

            $bis = date('Y-m-d\TH:i:sO', time() + self::LAUFZEIT);
            $yaml = "ressource: {$ressource}\n"
                ."fencing_token: {$token}\n"
                ."rolle: {$rolle}\n"
                ."owner: {$sitzung}\n"
                .'pid: '.getmypid()."\n"
                .'erteilt: "'.date('Y-m-d\TH:i:sO')."\"\n"
                ."heartbeat_bis: \"{$bis}\"\n";

            // tmp + rename: eine halb geschriebene Lease waere schlimmer als keine.
            $tmp = $ordner.'/active/.lease.tmp';
            file_put_contents($tmp, $yaml);
            rename($tmp, $ordner.'/active/lease.yaml');
        } finally {
            @rmdir($sperre);
        }

        self::$gehalten = $ressource;
        self::$token = $token;

        return $token;
    }

    /** Gibt die Lease zurück — die Datei wandert nach `freigegeben/`, der Token bleibt vergeben. */
    public static function freigeben(): void
    {
        if (self::$gehalten === null) {
            return;
        }
        $ordner = self::ordner(self::$gehalten);
        $aktiv = $ordner.'/active/lease.yaml';
        if (is_file($aktiv)) {
            $ziel = $ordner.'/freigegeben/lease-token'.self::$token.'-'.date('Y-m-d-His').'.yaml';
            @rename($aktiv, $ziel);
        }
        self::$gehalten = null;
        self::$token = null;
    }
}
