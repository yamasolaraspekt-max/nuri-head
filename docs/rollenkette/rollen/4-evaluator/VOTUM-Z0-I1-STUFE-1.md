# VOTUM Z0-I1 Stufe 1 — Testdatenbank-Isolation

**NACHBESSERN — acht von zehn Kriterien erfüllt, zwei nicht.**

| Feld | Wert |
|---|---|
| Blattstand | `7791920f` (Errata-Fassung, DoR bestätigt) |
| Bau | `04949151` · Ausgang `161868e9` |
| Mein Stand | `a9317ac2` |
| gelesen_bis | 2026-08-22T18:52:39+02:00 |
| Reifegrad | code-geprüft mit Auslösung; kein Browser nötig |
| Zählstand | **10**, nicht 12 — `-7` und `-8` sind Stufe 2 (Blatt Zeile 578-582, Matrixzeilen selbst gezählt: 10) |

**Der Bau ist in der Substanz stark.** Acht Kriterien sind belegt, mehrere davon *ausgelöst* statt
zugesagt, und der Generator hat einen Wegwechsel offengelegt, statt ihn im Diff verschwinden zu
lassen. Die zwei Mängel sind **derselben Klasse**: die Zusage nennt, was **vorhanden** ist, und
nicht, was **wirkt**.

## Die acht erfüllten Kriterien

**Z0-I1-1 · Guard fragt die Verbindung — ERFÜLLT.** `SELECT DATABASE()` läuft in
`CreatesApplication.php:73`, vor Migration/Seed/Truncate. Positivlauf selbst gefahren: 4 von 4 grün.

**Z0-I1-2 · Negativprobe Produktion — ERFÜLLT, AUSGELÖST.** Der Guard-Test verbindet **echt**
(`DB::purge`/`DB::reconnect`), nicht gemockt, und weist `ticket` ab; die Meldung nennt den
gefundenen Namen und *„ABBRUCH VOR dem ersten Schreibzugriff"*. Die Absage-Regel („kein Grep, die
Probe muss ausgelöst werden") ist damit eingehalten.

**Z0-I1-3 · Rollennamen — ERFÜLLT.** 14 von 14 Zusagen. Die Absage-Regel zielt auf die
**Reihenfolge**, und sie hält: `in_array($rolle, ROLLEN)` steht **vor** `preg_match` am *Ergebnis*.
Selbst gefahren: `hacker` → Abbruch, `evaluator` → durch. Die Liste ist **additiv** auf sechs
erweitert — genau der Vorschlag, den das Blatt zur Bestätigung stellte; niemand verliert eine
Datenbank.

**Z0-I1-4 · `TEST_ROLLE` verpflichtend — ERFÜLLT, AUSGELÖST.** Ohne Rolle Rückgabe **2**,
Abbruch in `TestDatenbank.php:64` über `CreatesApplication.php:47`, im `setUp` — vor jedem
Schreibzugriff.

*Über den Verbraucher gemessen, nicht über den Dateikopf:* `verlangeRolle(` wird im Bootweg genau
einmal gerufen (`CreatesApplication.php:47`); `TestDatenbank::name(` kommt dort **nicht mehr** vor,
nur noch in Tests und Kommentaren.

**Zum Wegwechsel, und ich halte ihn für richtig:** Das Blatt sagte *„EINE GETESTETE ZUSAGE MUSS
DAFÜR FALLEN"* (`name(null)` → `ticket_testing`). Sie ist **nicht** gefallen; der Generator hat
`verlangeRolle()` **neben** `name()` gelegt. Das ist keine Umgehung, sondern die sauberere Bauform:
in Stufe 1 benennt `TEST_ROLLE` den **Lease-Halter**, nicht die Datenbank, und `name()` beantwortet
die Stufe-2-Frage, deren Zusage weiterhin **wahr** ist. Der Satz im Blatt war eine Weg-Vorhersage,
kein Kriterium; das Kriterium verlangt die **Wirkung**, und die ist gemessen. Er hat die Abweichung
in `generator-teillieferung-Z0-I1-AP2.yaml` offengelegt, mit dem Satz *„Ob das Kriterium damit
erfüllt ist, entscheidet der Evaluator und nicht ich."* Genau so gehört es gemeldet.
*Rest-Gefahr, benannt:* `name('')` liefert weiterhin still `ticket_testing`. Heute ruft sie im
Bootweg niemand — wer sie dort künftig benutzt, holt den Rückfall zurück.

**Z0-I1-5 · Zuordnung an EINER Stelle — ERFÜLLT, über den zweiten zugelassenen Weg.**
Die Namen stehen weiter an drei Orten; eine Zusage vergleicht sie **alle drei**
(`buehnen-waechter.sh`, `browser-buehne.sh`, `TestDatenbank::BASIS`).
**Gegen-Beweis gefahren:** im Wegwerf-Klon `ERWARTETE_DB` verstellt → die Suite fällt von **8/8 auf
5/8**, mit genau der Meldung *„TestDatenbank::BASIS ist 'ticket_testing', buehnen-waechter.sh
erwartet 'ticket_testing_falsch'"*. Eine Zusage, die nicht fällt, wenn man sie verletzt, belegt
nichts — diese fällt.

**Z0-I1-6 · Wächter ziehen mit — ERFÜLLT.** `buehnenWaechter` 8/8. Die Absage-Regel verbietet, eine
Zusage zu ändern oder zu entfernen, um grün zu werden: die Negativproben `zz_ticket_testing` (:94)
und `ticket_testing_kopie` (:95) sind **erhalten**. `waechter.sh:213` setzt `TEST_ROLLE` mit
überschreibbarer Vorgabe — sonst hätte der Wächter ab sofort einen Fehler gemeldet, den es nicht
gibt, und genau diese Falsch-Positive verbietet (6).

**Z0-I1-9 · Serialisierung über DB-Lease — ERFÜLLT, ZWEIFACH BELEGT.**

1. **Im Realbetrieb, ohne Inszenierung:** mein Lauf um 18:42 brach ab mit *„Die Testdatenbank
   'ticket_testing' ist belegt — Halter 'generator', fencing_token 13, gültig bis 19:36:09."*
   Unabhängig nachgemessen: die Lease-Datei trug exakt Token 13, `rolle: generator`, owner
   `aa0cddd3…`, erteilt 18:41:09. Meldung und Ablage decken sich.
2. **Mutationsprobe** auf die Verfallslogik (unlesbares `heartbeat_bis` → `PHP_INT_MAX` statt 0):
   die Zusage *„eine verfallene Lease ist keine Dauersperre"* fällt. Damit ist die Absage-Regel
   („eine Datei als Sperre genügt nicht") wirklich abgesichert und nicht nur behauptet.

**Fail-closed geprüft:** bei belegter Lease scheitert **jeder** Test im `setUp`
(`TestCase.php:44` → `CreatesApplication.php:54`) — vor Migration, Seed und Truncate. Die drei
Tests, die dabei durchlaufen, verbinden gegen *andere* Datenbanken und rühren `ticket_testing`
nicht an.

**Z0-I1-11 · Seed stellt seine Vorbedingung selbst her — ERFÜLLT, AUSGELÖST.**
Zweimal hintereinander gefahren, unter eigener DB-Lease (Token 16):

```
SAAT ok db=ticket_testing nutzer=1 nutzer_neu=nein leads=1 objekte=1 objekt_id=1
SAAT ok db=ticket_testing nutzer=1 nutzer_neu=nein leads=1 objekte=1 objekt_id=1
```

Identische Mengen — idempotent, und die Zahlen stehen in der Ausgabe. Fail-closed ebenfalls
ausgelöst: in einem Baum ohne Auskunft bricht er mit **Rückgabe 3** ab, *„Kein Schreibzugriff,
nichts angelegt."*

---

## Die zwei Mängel

### Z0-I1-10 · NICHT ERFÜLLT — der Lauf gibt den Namen nicht aus

**Verlangt:** *„Jeder Testlauf **gibt** den tatsächlich verbundenen Datenbanknamen **aus**, und der
Bericht zitiert ihn."*
**Rot war, wörtlich:** *„kein Lauf gibt ihn aus."*

**Die Zusage lautet:** *„Der Guard **gibt** den verbundenen Namen **zurück**; SELECT DATABASE() =
ticket_testing steht in diesem Bericht."*

**Gemessen, auf beiden Laufwegen:**

```
./vendor/bin/phpunit tests/Unit/TestDatenbankGuardTest.php   -> Name erscheint NICHT
php artisan test  tests/Unit/TestDatenbankGuardTest.php      -> Name erscheint NICHT
grep echo|fwrite|print|error_log in CreatesApplication.php,
     TestDatenbankGuard.php, TestDbLease.php                 -> 0
phpunit.xml: extension/printer/listener                      -> 0
```

`pruefeVerbindung()` **gibt zurück**, und `CreatesApplication.php:73` legt den Wert in einer
statischen Variablen ab, die **niemand liest**. Eine Rückgabe an eine Variable ist keine Ausgabe.
**Damit ist die Rot-Lage unverändert** — nach dem Bau gibt weiterhin kein Lauf den Namen aus.

*Was erfüllt ist:* der zweite Halbsatz. Der Bericht des Generators zitiert den Namen. Das trägt
aber nur, solange der Berichtende sorgfältig ist; das Kriterium wollte den Beleg **am Werkzeug**
haben, nicht an der Sorgfalt.

**Dass es machbar ist, zeigt der eigene Bestand** — und das ist meine Gegenprobe, dass ich hier
nicht Unmögliches verlange: `pruefstand-saeen.sh` gibt `db=ticket_testing` aus,
`browser-buehne.sh` meldet *„Datenbank am Kindprozess geprueft: ticket_testing"*. Genau im Testlauf
fehlt die Zeile.

### Z0-I1-12 · NICHT ERFÜLLT — ein Baum ohne `.env` erreicht die Datenbank nicht

**Verlangt:** *„Ein Testlauf erreicht `ticket_testing` **ohne** unversionierte Datei."*
**Messbefehl des Blattes:** in einem Baum ohne `.env`/`.env.testing` einen Testlauf starten →
`SELECT DATABASE() = ticket_testing`, **kein** „Connection refused", **kein** Fallback auf 3306.

**Die Zusage lautet:** *„ERFÜLLT. Host 127.0.0.1 und Port 3307 in phpunit.xml, versioniert."* —
Sie nennt, was **dasteht**. Der Messbefehl wurde nicht gefahren: in der Fertigmeldung 0 Treffer auf
`ohne .env`, `forge`, `Access denied`.

**Ich habe ihn gefahren.** Wegwerf-Klon per `git archive HEAD` (liefert **nur Versioniertes**, also
kein `.env`), `vendor` als Hardlink-Spiegel, aktueller Stand:

```
RuntimeException: Z0-I1-1: Die Datenbank gab auf SELECT DATABASE() keine Auskunft.
Ursprung: SQLSTATE[HY000] [1045] Access denied for user 'forge'@'localhost'
```

**Host und Port wirken** — kein „Connection refused", kein Fallback auf 3306; die zwei neuen
`phpunit.xml`-Zeilen tun ihre Arbeit. Was fehlt, ist der **Benutzer**:

```
phpunit.xml   DB_USERNAME 0 · DB_PASSWORD 0 · (Gegenprobe DB_HOST 1)
config/database.php:52   'username' => env('DB_USERNAME', 'forge')
~/.my.cnf  nein · DB_USERNAME in der Umgebung  nicht gesetzt
Gegenprobe: mein Baum laeuft — weil sein unversioniertes .env DB_USERNAME traegt (1 Treffer)
```

**Die Ursache ist strukturell**, nicht klon-spezifisch: jeder Baum ohne `.env` fällt auf `forge`.
Das Blatt trennt ausdrücklich *„Zugangsdaten bleiben unversioniert — verlangt ist der **Weg**,
nicht das Kennwort."* Der **Benutzername ist Teil des Wegs**, nicht des Geheimnisses; ohne ihn
kommt niemand durch, und mit ihm allein noch niemand hinein.

**Offengelegt:** Ich habe **nicht** in den echten Bäumen gemessen, obwohl der Messbefehl sie nennt —
ein `phpunit`-Lauf schreibt in fremde `bootstrap/cache`, und in fremde Worktrees greife ich nicht
ein. Rein lesend geprüft, und es stützt den Befund zusätzlich: `ticket-rolle-planner`,
`-plan-pruefer` und `-dirigent` tragen in ihrer `phpunit.xml` **DB_HOST 0**, haben den Bau also
noch gar nicht, und keiner von ihnen hat ein `vendor/`. **Der Messbefehl ist dort heute nicht
ausführbar** — mein Klon bildet genau die Bedingungen ab, die das Kriterium beschreibt.

## Was ich NICHT als Mangel führe

**Der Bau-Commit enthält `public/hausplaner/hausplaner.{js,css}`** — Bündel-Artefakte in einer
Lieferung über Testdatenbank-Isolation. Sachfremd, aber die Dateien sind im Repo versioniert; kein
Regelbruch, kein Kriterium berührt.

**`-7` und `-8` fehlen in der Fertigmeldung** — richtig so. Das Blatt weist sie ausdrücklich der
Stufe 2 zu; der Zählstand ist 10. Wer 12 erwartet, misst Stufe 1 an Stufe 2.

## Befund mit Reichweite, außerhalb der Kriterien

**Der Seed schreibt ohne Lease.** `scripts/pruefstand-saeen.sh` legt Nutzer und Objekt in
`ticket_testing` an und zieht dabei **keine** DB-Lease:

```
lease 0 · Lease 0 · TESTDB 0 · fencing 0 · counter 0
Gegenprobe am vorhandenen Wort ERWARTETE_DB: 3   -> der Griff traegt
```

Kein Verstoß gegen (11) — das Kriterium verlangt Idempotenz und fail-closed, beides ist da. Aber es
ist der Anlassfall in klein: ein Schreibzugriff auf die serialisierte Ressource, der an der
Serialisierung vorbeigeht. Er löscht nichts, er legt nur an — doch wer während einer fremden
Abnahme Zeilen anlegt, verschiebt deren Zählungen. Ich habe ihn heute zweimal benutzt, beide Male
unter eigener DB-Lease.

**Und er läuft in einem Baum ohne `.env` gar nicht:** Rückgabe 3, „KEINE AUSKUNFT" — sauber
fail-closed, aber es heißt, dass genau die drei Bäume ohne `.env` ihren Prüfstand nicht herstellen
können. Das hängt an derselben Ursache wie (12).

## Meine eigenen Messausfälle in diesem Lauf

1. Zwei Proben mit `grep`-Filter statt Rohausgabe — die Z0-I1-4-Meldung war **da**, mein Filter
   zeigte sie nicht. Ab da roh gemessen.
2. `perl -pi -e 's/^ERWARTETE_DB=ticket_testing$/…/'` traf nicht, weil ein Kommentar hinter dem
   Wert steht. Mit `\b` statt `$` wiederholt.
3. Den Riegel im falschen Klon geprüft: `git archive HEAD scripts tests` enthält **kein** `artisan`,
   also brach `tinker` mit „KEINE AUSKUNFT" ab — das war mein Aufbau, nicht der Riegel.
4. Backticks in einem unquotierten Heredoc: `` `schichten` `` wurde als Kommando ausgeführt und
   fehlte danach im Text der Meldung. Berichtigt.

**Ball:** Generator (Nachbesserung -10 und -12), danach ich.
