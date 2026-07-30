# PB-047 — die Seitenleisten-Zähler reißen seit dem 07.07., 464 Mal

**Spur A** *(Live-Endpunkt im CRM mit ~3000 Kundendatensätzen, Auth-nah)* · **Heimat: ticket**
**Basis: HEAD beim Ziehen** · *Geschnitten 31.07. 01:00, aus dem Befund des Prüfers (`1a46f034`).*

**Vorrang vor AUF-91.** *Der einzige Posten heute Nacht, der eine LAUFENDE Anwendung betrifft
und nicht den Hausplaner.* Reißt der Aufruf, kommt die ganze JSON-Antwort der Zähler nicht
zustande — für den, der die Seitenleiste ansieht.

## Der Befund

```text
app/Http/Controllers/Dashboard/SidebarCountController.php
  Zeile  16   $employeeId = $user?->name;                       <- Zeichenkette
  Zeile 162   countInquiryUnpublished(?int $employeeId = null)  <- fordert ?int
  Zeile 268   countMyTasks(?int $employeeId)                    <- fordert ?int

Log: 464 Treffer seit 07.07. - 57 am 30.07., 14 in der Nacht zum 31.07.
     (gezaehlt vom Pruefer, PB-047)
```

**Warum es genau bei manchen Konten reißt:** `users.name` trägt in diesem Projekt die
`employees.id` — **aber nicht bei jedem Konto.** Steht dort ein echter Name, ist die Zeichenkette
nicht numerisch, PHP kann sie nicht zu `int` machen, und der Aufruf wirft.

## Die eine Wahrheit steht schon da — und wird hier nicht benutzt

```text
app/Models/User.php:81
  public function employeeId(): ?int
  {
      return is_numeric($this->name) ? (int) $this->name : null;
  }
```

**Genau der fehlende Schutz, fertig gebaut, mit Kommentar.** Gemessen, wer sie benutzt:

```text
employeeId() aufgerufen                     92 Stellen
(int) $user->name  - stiller Cast           78 Stellen
$user->name roh als employeeId              53 Stellen
$user->employee_id                          27 Stellen
```

*Zwölftes Vorkommen desselben Musters heute Nacht: etwas ist gebaut, benannt und wird an der
Stelle, an der es zählt, umgangen.*

## Die Entscheidung

**`$user?->name` wird durch `$user?->employeeId()` ersetzt — an dieser einen Datei.**

**NICHT `(int)` davorschreiben.** Das ist der naheliegende Weg und der falsche: aus einem echten
Namen würde stillschweigend `0`, und die Zähler zeigten dann die Posten des Mitarbeiters mit der
`id` 0, statt gar keine. **Ein falscher Zähler ist schlimmer als ein leerer** — er sieht richtig
aus.

**Nicht in diesem Auftrag:** die 131 anderen Stellen (78 stille Casts, 53 rohe Zugriffe). *Sie
sind derselbe Fehler und gehören eigene Blätter — aber ein Blatt, das 131 Stellen anfasst, ist
kein Blatt, sondern eine Umbauwelle.* **Der Prüfer hat sie gezählt, sie sind nicht vergessen.**

## Kriterien

```yaml
scope:
  datei: app/Http/Controllers/Dashboard/SidebarCountController.php
  population_command: "grep -o 'employeeId' app/Http/Controllers/Dashboard/SidebarCountController.php | wc -l"
  ausschluesse:
    - stelle: "die 131 anderen Stellen mit (int) user->name oder user->name roh"
      grund: "Derselbe Fehler, aber 131 Stellen sind eine Umbauwelle und kein Blatt. Eigene Blaetter, nach diesem."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "Die Datei liest die Mitarbeiter-id nicht mehr roh aus dem Namen."
    pruefung:
      befehl: "grep -oE 'user[?]-.name' app/Http/Controllers/Dashboard/SidebarCountController.php | wc -l"
      erwartet: "0"
    ausgangswert: "1 (Zeile 16). Der Befehl meidet das Pfeilzeichen absichtlich - die Denylist von auftrag-pruefen.mjs liest es als Umleitung und ueberspringt den Befehl ungeprueft. Zweites Mal heute Nacht."
    gegenbeweis: >
      Auch der stille Cast muss 0 bleiben. Zusatzpruefung:
      grep -oE '[(]int[)] .user' app/Http/Controllers/Dashboard/SidebarCountController.php | wc -l  -> 0
      Wer (int) schreibt, hat den Fehler verschoben, nicht behoben.

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Sie benutzt die vorhandene eine Wahrheit."
    pruefung:
      befehl: "grep -o 'employeeId()' app/Http/Controllers/Dashboard/SidebarCountController.php | wc -l"
      erwartet: "1"
    ausgangswert: "0"
    gegenbeweis: >
      Keine zweite Hilfsfunktion in diesem Controller anlegen, die dasselbe noch einmal rechnet.
      Zusatzpruefung: grep -o 'is_numeric' auf die Datei -> 0.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Ein Konto mit nicht-numerischem Namen bekommt Zaehler statt eines Fehlers."
    ausgefuehrt_von: generator
    pruefung:
      typ: verfahren
      schritte: >
        Einen Testfall mit einem Konto, dessen name NICHT numerisch ist, gegen den Endpunkt fahren.
        Erwartet: HTTP 200 und ein vollstaendiges counts-Objekt. Die persoenlichen Zaehler
        (my_*) stehen dabei auf dem Wert, den ein null-employeeId ergibt - nicht auf den Zahlen
        eines fremden Mitarbeiters.
      erwartet: "200, counts vollstaendig, keine TypeError-Zeile im Log"
    gegenbeweis: >
      VOR der Aenderung denselben Fall fahren: er MUSS den TypeError zeigen. Zeigt er ihn nicht,
      ist der Testfall nicht der Fall aus dem Log, und K-03 beweist nichts.

  - id: K-04
    typ: behavioural
    aussage: "Ein Konto mit numerischem Namen zaehlt UNVERAENDERT."
    ausgefuehrt_von: generator
    pruefung:
      typ: verfahren
      schritte: >
        Vor und nach der Aenderung dieselben counts fuer ein Konto mit numerischem name abrufen
        und Feld fuer Feld vergleichen.
      erwartet: "identisch"
    begruendung: >
      Das ist die Regressionsschranke. employeeId() liefert fuer numerische Namen denselben Wert
      wie der bisherige Weg - das muss gemessen und nicht angenommen werden.

  - id: K-05
    typ: behavioural
    aussage: "Die Gates bleiben gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "php artisan test"
      erwartet: "801/0 unveraendert oder mehr Zusagen, keine rote"
    begruendung: "Der Planner faehrt keine Gates; der Bauende legt die Rohausgabe bei."
```

## Rückweg und Entdeckung

**Rückweg:** ein Commit ohne Datenmigration — `git revert` genügt. Keine Schemaänderung, keine
Datenänderung.
**Entdeckung:** die Zeile `TypeError` in `storage/logs/laravel.log` verschwindet. *Sie war heute
Nacht 14-mal da; nach der Änderung darf sie für diesen Controller nicht mehr auftauchen.*

## Der Zusammenhang, der wichtiger ist als der Fehler

Der Prüfer hat ihn gefunden, **weil er wissen wollte, wer die 47 036 Byte je Takt ins Log
schreibt.** Sein Satz: *„PB-043 war nie ein Aufräum-Befund, sondern der Grund, warum dieser hier
24 Tage unentdeckt blieb."*

**Ein 229-MB-Log liest niemand.** Das ist das Argument für PB-043 Teil 2 — nicht der Plattenplatz.
