# KLEINAUFTRAG an den GENERATOR — PB-044 und PB-043, beide klein, beide seit Stunden offen

**Geschnitten 30.07. 21:26, ausgelöst durch Mahnung 2 des Prüfers.** *Sein Satz trifft:
„In derselben Zeit zwei Auftragsblätter und 30 geänderte Dateien — **es wird gearbeitet, nur
nicht an den Befunden.**"*

## Mein Fehler, der das verursacht hat

**Ich habe PB-044 als „liegt bei Yama" geführt.** Gemessen ist es das nicht: `.env.testing`
anlegen ist **eine Datei mit einer Zeile**, die jede Instanz schreiben kann. Ich habe eine
Aufgabe bei Yama geparkt, die nie bei ihm lag — und dadurch stand sie seit **12:29** still.

*Dieselbe Klasse wie heute Abend schon zweimal: eine Zuordnung, die niemand widerlegt, weil
niemand sie prüft. Der Prüfer hat sie geprüft.*

---

## A — PB-044: `--env=testing` trifft die Arbeits-Datenbank

**Der Befund (Prüfer, 12:29):** ohne `.env.testing` fällt `php artisan test --env=testing` auf
die Standard-Umgebung zurück und arbeitet gegen die **Arbeits-DB `ticket`**. Die Test-DB ist nur
in `phpunit.xml` erzwungen — wer den Schalter benutzt, glaubt „testing" und bekommt „ticket".

**Der Schalter sagt testing und bedeutet ticket.** Das ist kein Schönheitsfehler: dort liegen die
Seeder-Bestände, gegen die gearbeitet wird.

```yaml
  - id: K-01
    aussage: "--env=testing trifft die Test-Datenbank, nicht die Arbeits-Datenbank."
    nachweis: >
      .env.testing anlegen mit DB_DATABASE=ticket_testing (und was sonst zwingend nötig ist,
      NICHT mehr). Dann: php artisan tinker --env=testing --execute="echo config('database.connections.mysql.database');"
    erwartet: "ticket_testing"
    gegenbeweis: >
      Denselben Befehl OHNE --env=testing fahren. Kommt dort auch `ticket_testing`,
      misst die Probe nicht den Schalter, sondern etwas anderes.
  - id: K-02
    aussage: "Keine Zugangsdaten im Repo."
    befehl: "git check-ignore -v .env.testing"
    erwartet: >
      Treffer — die Datei ist ignoriert wie .env. Ist sie es NICHT, gehört sie in .gitignore,
      BEVOR sie angelegt wird. *Kein Passwort, kein Schlüssel in die Datei.*
```

## B — PB-043: zwei unbedingte `Log::info` blähen das Log auf 212 MB

**Der Befund (Prüfer, 12:12):** `ChatController` Zeile 70 und 281 loggen bei **jedem** Aufruf
eines gepollten Endpunkts. 64 086 Zeilen stammen aus diesen zwei Stellen; **404 echte
Fehlermeldungen liegen dazwischen begraben.** Kanal ist `single` — eine Datei ohne Rotation.
Der `daily`-Kanal mit 14 Tagen ist konfiguriert und unbenutzt.

*Datenschutz-Kante, ausdrücklich als Kante gemeldet und nicht als Leck:* `auth_user_id`,
`auth_employee_id`, `customer_id` stehen im Klartext.

```yaml
  - id: K-03
    aussage: "Der gepollte Endpunkt loggt nicht mehr bei jedem Aufruf."
    befehl: "grep -n 'Log::info' app/Http/Controllers/**/ChatController.php"
    erwartet: >
      Die zwei unbedingten Stellen sind fort oder auf Log::debug herabgestuft.
      *Nicht löschen, wenn sie einen Zweck haben — dann hinter eine Bedingung.*
  - id: K-04
    aussage: "Die 404 Fehlermeldungen sind noch da."
    gegenbeweis: >
      Zähle die Fehlerzeilen VOR und NACH der Änderung. Sinkt die Zahl,
      hat die Änderung mehr entfernt als den Lärm — das wäre der Befund.
```

**Nicht in diesem Auftrag:** der Wechsel von `single` auf `daily`. Das ist eine
Umgebungsentscheidung (`LOG_CHANNEL`) und gehört Yama — *auf Hetzner kann sie anders stehen,
und das sehe ich von hier nicht.*

---

**Spur A** (Umgebung und ein Controller) · **Heimat: ticket** · **Vor AUF-48-S4b?**
Nein — **danach**, aber vor S4c. *Beide Teile zusammen sind kleiner als eine halbe Scheibe.*
