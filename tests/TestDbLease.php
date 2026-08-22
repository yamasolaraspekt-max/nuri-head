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

    /**
     * **Was dieser Prozess haelt — als LISTE, nicht als Einzelwert.**
     *
     * ⚠ Die erste Fassung hielt genau eine Ressource. Die Probe-Datei zieht ihre eigene Lease und
     * ueberschrieb damit den Halter des ECHTEN Laufs; beim Prozessende war er verloren und die
     * Lease blieb liegen. *Ein `$nur`-Parameter beim Freigeben half nicht — der Schaden entsteht
     * beim ZIEHEN.* Gefunden, weil `active` nach der vollen Suite auf 1 stand statt auf 0, und
     * erst im dritten Anlauf richtig zugeordnet.
     *
     * @var array<string,int> Ressource => fencing_token
     */
    private static array $gehalten = [];

    /**
     * **Zeitstempel in ORTSZEIT mit Offset — nicht UTC.**
     *
     * ⚠ Die erste Fassung schrieb `date(...)` direkt und lieferte `+0000`: PHP-CLI steht auf UTC,
     * unabhaengig von `config('app.timezone')` (gemessen: `date_default_timezone_get()` = UTC,
     * `app.timezone` = Europe/Berlin, System = CEST +0200). Eine Lease, deren `erteilt` zwei
     * Stunden in der Vergangenheit zu liegen scheint, laedt jeden Leser zu dem Schluss ein, sie
     * sei alt. **Die Zeitkonvention dieses Hauses verlangt Ortszeit mit Offset.**
     */
    private static function jetzt(int $plus = 0): string
    {
        // `app.timezone` ist die Wahrheit dieses Hauses (Europe/Berlin); PHP-CLI steht daneben auf
        // UTC. **`function_exists('config')` genuegt NICHT** — der Helfer ist geladen, wirft aber
        // ohne Anwendungs-Container, und die Probe laeuft bewusst ohne Anwendung. Deshalb der
        // Versuch mit Auffangen; ohne Container bleibt die PHP-Zeitzone.
        // ⚠ **Die Lease wird VOR dem Bootstrap gezogen** — es gibt dort keinen Container, `config()`
        // wirft, und `date_default_timezone_get()` steht auf UTC. Genau deshalb trugen die ersten
        // Leases `+0000`. Die Zeitzone kommt daher aus der VERSIONIERTEN Quelle, mit derselben
        // Begruendung wie Host und Port in Z0-I1-12: sie muss dort stehen, wo jeder Baum sie hat.
        $zone = (string) (getenv('APP_TIMEZONE') ?: '');
        if ($zone === '') {
            try {
                $zone = function_exists('config') ? (string) (config('app.timezone') ?: '') : '';
            } catch (\Throwable) {
                $zone = '';
            }
        }
        $ort = new \DateTimeZone($zone !== '' ? $zone : date_default_timezone_get());

        return (new \DateTimeImmutable('@'.(time() + $plus)))->setTimezone($ort)->format('Y-m-d\TH:i:sO');
    }

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

            $bis = self::jetzt(self::LAUFZEIT);
            $yaml = "ressource: {$ressource}\n"
                ."fencing_token: {$token}\n"
                ."rolle: {$rolle}\n"
                ."owner: {$sitzung}\n"
                .'pid: '.getmypid()."\n"
                .'erteilt: "'.self::jetzt()."\"\n"
                ."heartbeat_bis: \"{$bis}\"\n";

            // tmp + rename: eine halb geschriebene Lease waere schlimmer als keine.
            $tmp = $ordner.'/active/.lease.tmp';
            file_put_contents($tmp, $yaml);
            rename($tmp, $ordner.'/active/lease.yaml');
        } finally {
            @rmdir($sperre);
        }

        self::$gehalten[$ressource] = $token;

        return $token;
    }

    /**
     * Gibt die Lease zurück — die Datei wandert nach `freigegeben/`, der Token bleibt vergeben.
     *
     * ⚠ **`$nur` ist die Lehre aus einem eigenen Fehler:** die Probe-Datei rief `freigeben()` in
     * ihrem `tearDown()` und räumte damit den statischen Zustand des ECHTEN Laufs ab — beim
     * Prozessende war `$gehalten` bereits `null`, und die Lease des Laufs blieb liegen. *Eine
     * verwaiste Lease, erzeugt von genau dem Test, der Verwaisung verhindern soll.*
     * Wer `$nur` setzt, gibt **nur** frei, wenn er diese Ressource auch hält.
     */
    public static function freigeben(?string $nur = null): void
    {
        foreach (self::$gehalten as $ressource => $token) {
            if ($nur !== null && $ressource !== $nur) {
                continue;
            }
            $ordner = self::ordner($ressource);
            $aktiv = $ordner.'/active/lease.yaml';
            if (is_file($aktiv)) {
                $ziel = $ordner.'/freigegeben/lease-token'.$token.'-'.date('Y-m-d-His').'.yaml';
                @rename($aktiv, $ziel);
            }
            unset(self::$gehalten[$ressource]);
        }
    }
}
