# Z-06-N1 — Herkunft und Freigabe überleben das Speichern

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 15:3x auf Yamas Anweisung*

```yaml
auftrag:
  id: Z-06-N1
  strang: hausplaner-3d
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Pruefer
  gegengelesen_von:
  gegengelesen_am:
  befund:
  fachentscheidung: "Yama, 02.08. — B10. Wortlaut: 'Automatisch erzeugte oder geschaetzte Geometrie darf ihren Unsicherheits- und Herkunftsstatus durch Speichern, Laden, Export oder Sitzungswechsel nicht verlieren.' Nicht kosmetisch, sondern ein Persistenz- und Vertrauensproblem."
```

## Warum JETZT und nicht nach Z-07/Z-08 — der Grund steht im Code

**Gemessen 02.08. 15:2x, und das ist der eigentliche Anlass für die Reihenfolge:**

```text
grep -rn "polygon: gebaeudeUmriss()" resources/planner/hausplaner/
  ->  HausplanerApp.tsx:945   roofType: 'sattel', neigungGrad: 35 …      DAS DACH
```

**Z-06 hat die Decke von der Bounding-Box befreit — beim Dach steht sie noch.** *Und Z-07/Z-08
sind geschnitten, aber nicht gebaut.* **Wer das Dach baut, bevor B10 steht, baut denselben
Bestand ein zweites Mal ein** — und dann sind es zwei Bestände statt einem.

**Z-07 und Z-08 werden deshalb zurückgestellt, bis dieses Blatt grün ist.** *Das ist keine
Vorsicht, das ist die billigere Reihenfolge: heute ein Feld, morgen ein Feld plus zwei
Wanderungen.*

## Was der Boden hergibt — gemessen, bevor entschieden wurde

```text
domain/validation.ts        341 Zeilen · z.strict() ueberall · schemaVersion: z.literal(2)
migriereSzene               v1 -> v2 existiert, ausdruecklich "REIN ADDITIV und MINIMAL"
z.enum([...])               etabliertes Muster (zoneType, oeffnungsArt, oeffnungsRichtung …)
ceilings[]                  eigene Sammlung, NICHT Teil der Node-Union
Mengenermittlung            liest die Decke HEUTE NICHT (geometry/ kennt kein ceiling)
```

**Drei Dinge machen diese Scheibe billig, und alle drei sind gemessen:** *das Migrationsmuster
existiert und ist erprobt · `z.enum` ist etabliert · und die Folgeprozesse hängen noch nicht an
der Decke.* **Die Sperre für Folgeprozesse kostet heute nichts — sie kostet, sobald das Dach dran
hängt.**

## Die Entscheidung — Herkunft und Freigabe sind ZWEI Felder, nicht eines

```text
herkunft   'manuell'      der Nutzer hat die Kontur gezeichnet
           'abgeleitet'   aus dem Grundriss abgeleitet (heute: gebaeudeUmriss)
           'erkannt'      automatisch erkannt
           'geschaetzt'   geraten

freigabe   'vorschlag'    entstanden, nicht angesehen
           'zu_pruefen'   dem Nutzer gezeigt, noch nicht entschieden
           'bestaetigt'   der Nutzer hat zugestimmt
           'abgelehnt'    der Nutzer hat widersprochen
```

**Warum zwei und nicht ein kombiniertes Feld:** *sie ändern sich zu verschiedenen Zeiten und aus
verschiedenen Gründen.* **Die Herkunft ist eine Tatsache und ändert sich nur, wenn die Geometrie
neu entsteht. Die Freigabe ist ein Urteil und ändert sich, wenn ein Mensch hinsieht.** Ein
kombiniertes Feld müsste bei jeder Bestätigung die Herkunft mitschreiben — und dann geht die
Tatsache verloren, sobald jemand zustimmt. *Genau das soll B10 verhindern.*

**`herkunft: 'manuell'` bekommt `freigabe: 'bestaetigt'` beim Entstehen** — wer selbst zeichnet,
hat bestätigt. *Alles andere beginnt bei `vorschlag`.*

## Die Sperre — und sie ist der Kern des Blattes

```text
Eine Geometrie mit freigabe != 'bestaetigt' ist KEINE verlaessliche Grundlage.
Wer sie fuer Dach, Mengen, Statik oder einen anderen Folgeprozess benutzt, bekommt
sie nicht - er bekommt eine Ablehnung mit Grund.
```

**Das ist eine Funktion, kein Vorsatz:** `istFreigegeben(node)` bzw. `verlangeFreigabe(node)`.
*Ein Kommentar „bitte vorher prüfen" ist Stufe 3 und hat in diesem Projekt zweimal nicht
getragen. Eine Funktion, die den Wert nicht herausgibt, ist Stufe 5.*

## Audit — wann und durch wen

```text
freigabe_am    ISO-Zeitstempel, gesetzt bei jedem Wechsel der Freigabe
freigabe_von   wer (Nutzerkennung oder 'system')
```

**Bewusst KEIN vollständiges Ereignisprotokoll in dieser Scheibe.** *Yamas Anforderung lautet
„wann und durch wen bestätigt oder geändert" — zwei Felder decken das ab. Ein Protokoll mit
Historie ist eine eigene Entscheidung, weil es die Dateigröße mit jeder Änderung wachsen lässt
und eine Aufräumfrage aufwirft, die hier niemand beantwortet hat.*

## Nahtstellen

```text
Hier wird geschrieben:
  domain/validation.ts             die zwei enums, vier Felder, schemaVersion 2 -> 3
                                   und migriereSzene v2 -> v3
  domain/scene.types.ts            die Typen dazu
  app/HausplanerApp.tsx            Decke UND Dach setzen herkunft/freigabe beim Anlegen
  geometry/freigabe.ts             NEU: istFreigegeben() + verlangeFreigabe()

Hier bewusst NICHT:
  Die sichtbare Kennzeichnung      Z-06-N2. Sie braucht diese Felder, nicht umgekehrt -
                                   und ein Blatt, das Schema UND Oberflaeche anfasst,
                                   ist zwei Blaetter in einem Umschlag.
  Der Bestaetigungs-Knopf          Z-06-N3. Ohne ihn steht alles auf `vorschlag`, und
                                   genau das ist der ehrliche Zustand, bis jemand hinsieht.
  Ein Ereignisprotokoll            Eigene Entscheidung (Dateigroesse, Aufraeumfrage).
  `type`, `objectType`, `zoneType`, `routeType`
                                   Stehende Sperre. Es werden Felder HINZUGEFUEGT,
                                   nichts umbenannt.
```

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/domain/validation.ts
    - resources/planner/hausplaner/domain/scene.types.ts
    - resources/planner/hausplaner/app/HausplanerApp.tsx
    - resources/planner/hausplaner/geometry/freigabe.ts
  population_command: "grep -ro 'herkunft' resources/planner/hausplaner/ | wc -l"
  ausschluesse:
    - stelle: "Die sichtbare Kennzeichnung nach dem Laden"
      grund: "Z-06-N2. Braucht diese Felder, nicht umgekehrt. Schema und Oberflaeche in einem Blatt sind zwei Blaetter in einem Umschlag."
      entschieden_von: planner
    - stelle: "Der Bestaetigungs-Knopf"
      grund: "Z-06-N3. Ohne ihn steht alles auf `vorschlag` - der ehrliche Zustand, bis jemand hinsieht."
      entschieden_von: planner
    - stelle: "Ein vollstaendiges Ereignisprotokoll"
      grund: "Zwei Felder decken 'wann und durch wen' ab. Historie laesst die Datei wachsen und wirft eine Aufraeumfrage auf, die niemand beantwortet hat."
      entschieden_von: planner
    - stelle: "type, objectType, zoneType, routeType"
      grund: "Stehende Sperre. Es werden Felder HINZUGEFUEGT, nichts umbenannt."
      entschieden_von: yama

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Herkunft steht im Schema."
    pruefung:
      befehl: "grep -o 'herkunft' resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 02.08. 15:2x; Partner 'zoneType' -> mehrfach, die Messung ist nicht leer)"

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Die Freigabe steht im Schema."
    pruefung:
      befehl: "grep -o 'freigabe' resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0"

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Die Schema-Version ist angehoben - eine alte Datei wird nicht stillschweigend als neu gelesen."
    pruefung:
      befehl: "grep -o 'schemaVersion: z.literal(3)' resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (heute z.literal(2))"
    gegenbeweis: |
      Bleibt die Version bei 2, laedt eine Datei OHNE die neuen Felder als gueltig - und dann
      hat eine Bestandsdecke keinen Herkunftsstatus, sieht aber aus wie eine mit. Das ist
      genau der Zustand, den B10 verbietet, nur unsichtbarer als vorher.

  - id: K-04
    typ: presence
    kritikalitaet: P1
    aussage: "Die Freigabe-Sperre ist eine FUNKTION, kein Kommentar."
    pruefung:
      befehl: "ls resources/planner/hausplaner/geometry/ | grep freigabe | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (ueber das VERZEICHNIS gemessen, weil die Datei vor dem Bau nicht existiert - `zaehle.mjs <datei>` wuerde ENOENT werfen)"

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE KERNZUSAGE VON B10: der Status ueberlebt Speichern UND Laden."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion, nicht ueber den Schirm:
          Decke ohne Kontur anlegen        -> herkunft 'abgeleitet', freigabe 'vorschlag'
          Szene serialisieren, parsen      -> BEIDE Werte unveraendert
          dasselbe fuer 'manuell'          -> herkunft 'manuell', freigabe 'bestaetigt'
          dasselbe fuer das DACH           -> gleiche Werte, gleicher Weg
        UND die rote Gegenprobe:
          ein Dokument OHNE die neuen Felder, schemaVersion 3  -> wird ABGELEHNT
          dasselbe Dokument mit schemaVersion 2                -> migriert, Felder gesetzt
        Die letzten zwei Zeilen sind die eigentliche Zusage: sie fallen, wenn jemand die
        Felder `.optional()` macht, um die Migration zu sparen.
      erwartet: "sechs Zusagen, davon eine ROTE"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Migration v2 -> v3 ist ADDITIV und verliert nichts."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Das Muster steht schon da (migriereSzene v1 -> v2, "REIN ADDITIV und MINIMAL") und wird
        fortgesetzt, nicht neu erfunden:
          eine v2-Szene mit Waenden, Decken, Daechern, Zonen  -> nach der Migration
            sind ALLE Sammlungen zeichengleich, nur die vier neuen Felder kommen dazu
          eine v2-Decke bekommt herkunft 'abgeleitet' + freigabe 'zu_pruefen'
            NICHT 'bestaetigt' - der Bestand ist nicht geprueft, nur alt
        Die zweite Zeile ist die scharfe: wer Bestandsdaten auf 'bestaetigt' setzt, macht
        die Migration gruen und B10 wirkungslos.
      erwartet: "zwei Zusagen, die zweite ist die tragende"

  - id: K-07
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE SPERRE GREIFT: unbestaetigte Geometrie wird nicht herausgegeben."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        istFreigegeben(node) und verlangeFreigabe(node) gegen alle vier Freigabewerte:
          'bestaetigt'  -> istFreigegeben true  · verlangeFreigabe liefert die Geometrie
          'vorschlag'   -> false · verlangeFreigabe wirft/liefert Ablehnung MIT GRUND
          'zu_pruefen'  -> false · dito
          'abgelehnt'   -> false · dito
        Der GRUND ist Teil der Zusage, nicht Zierrat: eine Ablehnung ohne Grund schickt den
        naechsten Leser suchen - dieselbe Auflage wie bei der Erlaubnisliste in W-01.
      erwartet: "vier Zusagen, drei davon Ablehnungen mit Grund"

  - id: K-08
    typ: behavioural
    aussage: "Der Zeitstempel und der Urheber werden bei JEDEM Wechsel gesetzt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        freigabe wechselt -> freigabe_am und freigabe_von sind gesetzt und veraendert.
        freigabe wechselt NICHT -> beide bleiben, wie sie waren.
        Die zweite Zeile faengt den Fall, in dem jemand bei jedem Speichern stempelt -
        dann sagt der Zeitstempel nichts mehr ueber die Bestaetigung.
      erwartet: "zwei Zusagen, die zweite ist die scharfe"

  - id: K-09
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Ausgangswert 1649 pass / 0 fail (Generator, 02.08. 13:4x). Danach mehr oder gleich, nie weniger."

  - id: K-10
    typ: absence
    kritikalitaet: P1
    aussage: "Kein persistierter Wert wurde umbenannt."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/domain/validation.ts | grep '^-' | grep -oE \"literal\\('(ceiling|wall|window|door)'\\)\" | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Die stehende Sperre von Yama: `type`, `objectType`, `zoneType`, `routeType` bleiben.
      Diese Scheibe FUEGT HINZU. Verschwindet eines der Literale aus dem Schema, ist eine
      abgelegte Datei nicht mehr lesbar - und das faellt erst beim Kunden auf.

  - id: K-11
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 9 Mutationen: Felder `.optional()` gemacht · schemaVersion nicht angehoben ·
        Migration setzt Bestand auf 'bestaetigt' · herkunft beim Speichern ueberschrieben ·
        freigabe beim Laden auf Vorgabe zurueckgesetzt · istFreigegeben gibt immer true ·
        Ablehnung ohne Grund · Zeitstempel bei jedem Speichern · Dach setzt die Felder nicht.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - der Status ueberlebt das NEULADEN."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, angemeldet, Expertenmodus.
        (a) Waende zeichnen, Decke anlegen OHNE Kontur
        (b) speichern
        (c) SEITE NEU LADEN
        (d) die Decke traegt weiterhin herkunft 'abgeleitet' und freigabe 'vorschlag'
            - abgelesen am Datenstand, nicht am Bildschirm (die Kennzeichnung ist Z-06-N2)
        KONTROLLE davor (B4): dieselbe Folge mit einer MANUELL gezeichneten Kontur
            -> herkunft 'manuell', freigabe 'bestaetigt'
        Erst weil die Kontrolle anders ausfaellt, bedeutet das Ergebnis etwas.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Die Felder werden `.optional()`, um die Migration zu sparen.          -> K-05 rote Gegenprobe
2  Die Migration setzt Bestandsdecken auf 'bestaetigt'.                  -> K-06 zweite Zeile
3  Das DACH bekommt die Felder nicht - und der naechste Bau setzt
   `polygon: gebaeudeUmriss()` erneut ohne Herkunft.                     -> K-05 vierte Zeile, K-11
4  `istFreigegeben` gibt immer true zurueck.                             -> K-07
5  Eine Ablehnung ohne Grund.                                            -> K-07
6  Der Zeitstempel wird bei jedem Speichern gesetzt.                     -> K-08 zweite Zeile
7  Ein persistiertes Literal wird umbenannt.                             -> K-10
8  Herkunft und Freigabe werden zu EINEM Feld zusammengefasst.
   OHNE ZUSAGE, mit Grund: die Entscheidung steht oben im Blatt und ist der Kern -
   die Herkunft ist eine Tatsache, die Freigabe ein Urteil. Eine Zusage darueber waere
   eine Zusage ueber die Gestalt, nicht ueber die Wirkung (F-06). Wer sie zusammenlegt,
   faellt bei K-05 durch, weil 'manuell' + 'bestaetigt' dann nicht mehr trennbar sind.
9  Ein Nutzer bestaetigt eine Decke, und spaeter aendert sich die Kontur.
   OHNE ZUSAGE, mit Grund: das ist Z-06-N3 (der Bestaetigungs-Knopf) - die Regel „Aenderung
   setzt die Freigabe zurueck" gehoert dorthin, wo bestaetigt wird. Hier gibt es noch
   keinen Weg, etwas zu bestaetigen.
```

## Rückweg und Entdeckung

**Rückweg:** eine Schema-Version zurück, die Migration entfernen. **Aber ehrlich benannt:** *sobald
eine Datei als v3 abgelegt ist, ist der Rückweg eine Rückmigration und keine Rücknahme.* **Das ist
die teuerste Scheibe seit Wochen — und genau deshalb steht sie vor Z-07/Z-08 und nicht danach.**

**Entdeckung:** K-06 zweite Zeile. **Wenn die Migration Bestandsdaten auf `bestaetigt` setzt, ist
alles grün und B10 wirkungslos** — und niemand sieht es, weil der Bestand ja „schon immer da war".

## Danach

**Z-06-N2** (sichtbare Kennzeichnung nach dem Laden) · **Z-06-N3** (Bestätigungs-Knopf, Rücksetzen
bei Änderung) · **dann erst Z-07/Z-08** (Dach).
