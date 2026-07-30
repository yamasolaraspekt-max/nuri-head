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

```yaml
  - id: K-01
    aussage: "Neue Eintraege landen in einer TAGESdatei, nicht in einer endlosen."
    nachweis: >
      Nach der Aenderung einen Log-Eintrag erzeugen und pruefen, welche Datei waechst.
    erwartet: "laravel-JJJJ-MM-TT.log entsteht; laravel.log waechst nicht mehr"
    gegenbeweis: >
      Setze LOG_CHANNEL zurueck auf single und wiederhole. Waechst dann WIEDER die alte
      Datei, misst die Probe wirklich den Kanal und nicht etwas anderes.

  - id: K-02
    aussage: "Die Aufbewahrung ist begrenzt und benannt."
    befehl: "grep -n \"'days'\" config/logging.php"
    erwartet: "14 — unveraendert. *Nicht heimlich hochsetzen; 14 Tage sind die bestehende Festlegung.*"

  - id: K-03
    aussage: "Die alten 404 Fehlermeldungen sind noch lesbar."
    nachweis: >
      Vor der Aenderung die Zahl der Fehlerzeilen in laravel.log zaehlen, nach der Aenderung
      dieselbe Zahl an derselben Datei (oder an ihrem verschobenen Ort).
    erwartet: "unveraendert"
    hinweis: >
      **Kein `rm` auf dem Mount** — die Datei wird per `mv` beiseite gelegt, wenn ueberhaupt.
      *Und der Ort steht im Bericht, damit sie auffindbar bleibt.*

  - id: K-04
    aussage: "Keine Zugangsdaten im Bericht."
    hinweis: >
      Der Bericht nennt Kanalnamen und Zeilenzahlen — **keine Log-Inhalte.**
      *Im Befund stand: `auth_user_id`, `auth_employee_id`, `customer_id` liegen im Klartext
      in dieser Datei. Wer sie zitiert, traegt sie weiter.*
```

## Was NICHT in diesem Auftrag ist

**Die Entscheidung, ob auf dem Server dasselbe gilt.** `LOG_CHANNEL` ist dort eine
Umgebungsvariable, die Yama setzt. *Dieser Auftrag macht die Vorgabe richtig; was auf Hetzner
steht, entscheidet er.*

**Reihenfolge: nach AUF-48**, aber **vor** den Papierposten — 219 MB wachsen weiter, solange
nichts geschieht.
