# PB-043 Teil 2 — das Log rotiert nicht

**Spur A** *(Umgebungs-/Konfigurationsverhalten, betrifft jeden Fehlerpfad)* · **Heimat: ticket**
*Geschnitten 30.07. 22:20, nach der Mahnung des Pruefers.*

**Teil 1 ist erledigt** (`fddec527`): das Chat-Polling loggt nicht mehr bei jedem Aufruf.
**Die Quelle ist zugedreht, das Fass bleibt voll.**

## Selbst nachgemessen (nicht aus dem Befund uebernommen)

```text
config/logging.php:57    'stack' => channels: ['single']     <- eine Datei, kein Boden
config/logging.php:68    'daily' => days: 14                 <- konfiguriert, UNBENUTZT
storage/logs/laravel.log                          219 MB
```

**Der billigere Weg steht in derselben Datei, elf Zeilen tiefer.** *Der `daily`-Kanal ist
vollstaendig konfiguriert — Pfad, Level, 14 Tage — und niemand benutzt ihn.*

## Die Grenze, die diesen Auftrag klein haelt

**`LOG_CHANNEL` kommt aus der Umgebung.** Auf Hetzner kann er anders stehen, und **das sehe ich
von hier nicht.** Deshalb:

- **Geaendert wird die VORGABE** in `config/logging.php` (`env('LOG_CHANNEL', 'stack')` bzw. die
  Kanalliste des `stack`), **nicht** eine `.env`.
- **Die vorhandene 219-MB-Datei wird NICHT geloescht.** *Sie enthaelt 404 Fehlermeldungen, die
  niemand gelesen hat. Wer sie wegwirft, um Platz zu schaffen, wirft den Befund mit weg.*
  Sie darf per `mv` beiseite — ausserhalb des Repos, ausserhalb von `storage/logs`.

## Kriterien

*Auf das Validator-Schema umgestellt am 30.07. 23:31 (VORLAGE-Regel 8).*

```yaml
scope:
  datei: config/logging.php
  population_command: "stat -c %s storage/logs/laravel.log"
  ausschluesse:
    - stelle: "LOG_CHANNEL auf dem Server"
      grund: "Umgebungsvariable, die Yama setzt. Dieser Auftrag macht die VORGABE richtig; was auf Hetzner steht, entscheidet er."
      entschieden_von: yama
    - stelle: "Loeschen der alten laravel.log"
      grund: "rm ist auf dem Mount gesperrt. Wenn ueberhaupt, wird per mv beiseite gelegt, und der Ort steht im Bericht."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: behavioural
    kritikalitaet: P1
    aussage: "Neue Eintraege landen in einer TAGESdatei, nicht in einer endlosen."
    ausgefuehrt_von: generator
    pruefung:
      typ: verfahren
      schritte: >
        Nach der Aenderung einen Log-Eintrag erzeugen und pruefen, welche Datei waechst.
      erwartet: "laravel-JJJJ-MM-TT.log entsteht; laravel.log waechst nicht mehr"
    gegenbeweis: >
      Setze LOG_CHANNEL zurueck auf single und wiederhole. Waechst dann WIEDER die alte
      Datei, misst die Probe wirklich den Kanal und nicht etwas anderes.

  - id: K-02
    typ: presence
    aussage: "Die Aufbewahrung ist begrenzt und benannt - und bleibt bei 14 Tagen."
    pruefung:
      befehl: "grep -oE \"'days'[^0-9]+[0-9]+\" config/logging.php | head -1"
      erwartet: "'days' gefolgt von 14"
    ausgangswert: "config/logging.php:68 - daily mit days 14, konfiguriert und UNBENUTZT"
    hinweis: >
      Der Befehl vermeidet den Pfeil absichtlich und sucht mit [^0-9]+ darueber hinweg. Grund:
      die Denylist von auftrag-pruefen.mjs liest das Zeichen als Umleitung und UEBERSPRINGT den
      Befehl - ein uebersprungener Befehl sieht im Bericht harmlos aus und ist ungeprueft.
      Selbst erlebt, in genau diesem Kriterium, am 30.07. 23:31.
    gegenbeweis: >
      Nicht heimlich hochsetzen. 14 Tage sind die bestehende Festlegung; wer sie aendert,
      aendert die Aufbewahrungsfrist und nicht die Rotation.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die alten 404 Fehlermeldungen sind noch lesbar."
    ausgefuehrt_von: generator
    pruefung:
      typ: verfahren
      schritte: >
        VOR der Aenderung die Zahl der Fehlerzeilen in laravel.log zaehlen, NACH der Aenderung
        dieselbe Zahl an derselben Datei oder an ihrem verschobenen Ort.
      erwartet: "unveraendert"
    begruendung: >
      Kein rm auf dem Mount. Die Datei wird per mv beiseite gelegt, wenn ueberhaupt, und der
      Ort steht im Bericht, damit sie auffindbar bleibt.

  - id: K-04
    typ: absence
    kritikalitaet: P1
    aussage: "Keine Zugangsdaten und keine Log-Inhalte im Bericht."
    pruefung:
      typ: verfahren
      schritte: >
        Der Bericht nennt Kanalnamen und Zeilenzahlen, keine Log-Inhalte.
      erwartet: "0 zitierte Log-Zeilen"
    begruendung: >
      Im Befund stand: auth_user_id, auth_employee_id, customer_id liegen im Klartext in
      dieser Datei. Wer sie zitiert, traegt sie weiter.
```

## Was NICHT in diesem Auftrag ist

**Die Entscheidung, ob auf dem Server dasselbe gilt.** `LOG_CHANNEL` ist dort eine
Umgebungsvariable, die Yama setzt. *Dieser Auftrag macht die Vorgabe richtig; was auf Hetzner
steht, entscheidet er.*

**Reihenfolge: nach AUF-48**, aber **vor** den Papierposten — 219 MB wachsen weiter, solange
nichts geschieht.
