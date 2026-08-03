# Z-06-N1 — Herkunft und Freigabe überleben das Speichern

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 15:3x auf Yamas Anweisung*

```yaml
auftrag:
  id: Z-06-N1
  strang: hausplaner-3d
  status: gebaut   # GEBAUT 03.08. 19:5x (B14: der Generator setzt gebaut). Mutationsprobe 9/9 blind VOR den Zusagen, 10/10 gefangen danach, alle drei Quelldateien md5-identisch wiederhergestellt. Suite 1649 -> 1667 pass / 0 fail, tsc 0. BEFUND AN DEN EVALUATOR: drei Mutationen (M4/M5/M8) sassen im Klick-Handler und waren dort GRUNDSAETZLICH nicht pruefbar - die Entscheidung wohnt jetzt in geometry/freigabe.ts (herkunftFuerNeueDecke, HERKUNFT_NEUES_DACH), plus eine Zusage, dass die App sie RUFT statt sie erneut zu treffen. 14 bestehende Zusagen wurden von der Versionsanhebung rot, alle einzeln nachgezogen und im Commit begruendet - keine wurde geschwaecht.
  gegengelesen_von: pruefer
  gegengelesen_am: "03.08.2026"
  befund: "BAUBAR. Alle fuenf Ausgangswerte selbst nachgemessen (0/0/0/0), Boden bestaetigt, K-09-Basis 1649 pass/0 fail heute gefahren. ZWEI EINWAENDE, beide am Blatt und nicht am Bau: (1) P2 - der population_command misst HEUTE SCHON 138, weil `herkunft` im Bundle bereits zweimal vergeben ist (ToolHerkunft, FlaechenHerkunft, 118 Feldtreffer); er kann nie zeigen, ob die Scheibe wirkt, und der Name bekaeme eine dritte Bedeutung. (2) P3 - 'z.strict() ueberall' hat 0 Treffer im Wortlaut (26 als '.strict()'). Einwand 1 ist vor dem Bau zu schliessen, Einwand 2 ist eine Formulierung. Status stellt der Planner (F-08b)."
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
domain/validation.ts        341 Zeilen · .strict() an 26 Stellen (Einwand 2 des Pruefers: "z.strict()" hatte 0 Treffer im Wortlaut) · schemaVersion: z.literal(2)
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

**Das Feld heißt `geometrieHerkunft`, nicht `herkunft` — Einwand 1 des Prüfers, angenommen:**
*das Wort ist im Bundle zweimal vergeben (`ToolHerkunft`, `FlaechenHerkunft`, 138 rohe Treffer)
und bekäme eine dritte Bedeutung. Dieselbe Klasse wie `PAKET_WERKZEUGE` in W-05 K-10: ein Name,
eine Bedeutung.* **`geometrieHerkunft` hat heute 0 Treffer in der ganzen Insel (gemessen) — damit
misst der population_command die WIRKUNG statt der Kollision.**

```text
geometrieHerkunft   'manuell'      der Nutzer hat die Kontur gezeichnet
                    'abgeleitet'   aus dem Grundriss abgeleitet (heute: gebaeudeUmriss)
                    'erkannt'      automatisch erkannt
                    'geschaetzt'   geraten

freigabe            'vorschlag'    entstanden, nicht angesehen
                    'zu_pruefen'   dem Nutzer gezeigt, noch nicht entschieden
                    'bestaetigt'   der Nutzer hat zugestimmt
                    'abgelehnt'    der Nutzer hat widersprochen
```

**Warum zwei und nicht ein kombiniertes Feld:** *sie ändern sich zu verschiedenen Zeiten und aus
verschiedenen Gründen.* **Die Herkunft ist eine Tatsache und ändert sich nur, wenn die Geometrie
neu entsteht. Die Freigabe ist ein Urteil und ändert sich, wenn ein Mensch hinsieht.** Ein
kombiniertes Feld müsste bei jeder Bestätigung die Herkunft mitschreiben — und dann geht die
Tatsache verloren, sobald jemand zustimmt. *Genau das soll B10 verhindern.*

**`geometrieHerkunft: 'manuell'` bekommt `freigabe: 'bestaetigt'` beim Entstehen** — wer selbst
zeichnet, hat bestätigt. *Alles andere beginnt bei `vorschlag`.*

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
  app/HausplanerApp.tsx            Decke UND Dach setzen geometrieHerkunft/freigabe beim Anlegen
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
  population_command: "grep -ro 'geometrieHerkunft' resources/planner/hausplaner/ | wc -l"
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
    aussage: "Die Geometrie-Herkunft steht im Schema - unter ihrem EINDEUTIGEN Namen."
    pruefung:
      befehl: "grep -ro 'geometrieHerkunft' resources/planner/hausplaner/ | wc -l"
      erwartet: "mindestens 2 (Schema + Setzstelle)"
    ausgangswert: "0 insel-weit (gemessen 03.08. 08:5x nach der Umbenennung auf Einwand 1 des Pruefers; das rohe Wort 'herkunft' traf 138 Fremdstellen. Partner 'zoneType' -> mehrfach, die Messung ist nicht leer)"

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
          Decke ohne Kontur anlegen        -> geometrieHerkunft 'abgeleitet', freigabe 'vorschlag'
          Szene serialisieren, parsen      -> BEIDE Werte unveraendert
          dasselbe fuer 'manuell'          -> geometrieHerkunft 'manuell', freigabe 'bestaetigt'
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
          eine v2-Decke bekommt geometrieHerkunft 'abgeleitet' + freigabe 'zu_pruefen'
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
        Migration setzt Bestand auf 'bestaetigt' · geometrieHerkunft beim Speichern ueberschrieben ·
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
        npm run build:hausplaner, dann die OBJEKT-Flaeche (objekt.blade - die Flaeche MIT
        data-speichern-url, Zeile 157; studio speichert NICHT: "No-Op", studio.blade:3,
        gemessen 03.08.). Angemeldet, Expertenmodus. Braucht ein Objekt in ticket_testing.
        (a) Waende zeichnen, Decke anlegen OHNE Kontur
        (b) speichern
        (c) SEITE NEU LADEN
        (d) die Decke traegt weiterhin geometrieHerkunft 'abgeleitet' und freigabe 'vorschlag'
            - abgelesen am Datenstand, nicht am Bildschirm (die Kennzeichnung ist Z-06-N2)
        KONTROLLE davor (B4): dieselbe Folge mit einer MANUELL gezeichneten Kontur
            -> geometrieHerkunft 'manuell', freigabe 'bestaetigt'
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

---

## Gegenlesung (Prüfer, 03.08.2026) — B8

**Ergebnis: BAUBAR.** Zwei Einwände, beide am Blatt und nicht am Bau. Der Kern des Blattes — zwei
Felder statt einem, die Sperre als Funktion, die Migration setzt Bestand NICHT auf `bestaetigt` —
trägt und ist gut begründet. **Ich habe nichts geändert außer diesem Abschnitt und dem `befund`-Feld.**

### Selbst nachgemessen (HEAD `8d9036a0`, 03.08. 08:1x) — nicht geglaubt

```text
grep -o 'herkunft' …/domain/validation.ts | wc -l                 -> 0     Blatt: 0    ✓
grep -o 'freigabe' …/domain/validation.ts | wc -l                 -> 0     Blatt: 0    ✓
grep -o 'schemaVersion: z.literal(3)' … | wc -l                   -> 0     Blatt: 0    ✓
   heute vorhanden: schemaVersion: z.literal(2)                            Blatt: 2    ✓
ls …/geometry/ | grep freigabe | wc -l                            -> 0     Blatt: 0    ✓
wc -l …/domain/validation.ts                                      -> 341   Blatt: 341  ✓
grep -c migriereSzene / z.enum                                    -> 2 / 10            ✓
grep -rn "polygon: gebaeudeUmriss()" …                            -> HausplanerApp.tsx:945  ✓
grep -rn 'ceiling' …/geometry/                                    -> 0 Treffer         ✓
   (die einzige Fundstelle bei -i ist wandFlaeche.ts:178, ein Kommentar,
    der CeilingNode ausdruecklich AUSSCHLIESST — die Aussage des Blattes stimmt)
npm run test:hausplaner                                           -> 1649 pass / 0 fail
   K-09 nennt 1649 als Ausgangswert. Heute gefahren, er stimmt. (Im Ledger stehen
    daneben 5x die Zahl 1641 — die aeltere Basis. Das Blatt hat die richtige genommen.)
```

### Einwand 1 · **P2 · der `population_command` kann nicht zeigen, ob die Scheibe wirkt**

```text
befehl: grep -ro 'herkunft' resources/planner/hausplaner/ | wc -l
   -> 138        (das Blatt fuehrt ihn als Populationsmass fuer eine Sache, die es noch nicht gibt)

wo:  112  app/tools/toolPresentation.ts       export type ToolHerkunft = 'registry' | 'katalog'
       5  app/HausplanerStudio.tsx            herkunft: FlaechenHerkunft = 'navi'
       3  app/dashboard/fachFlaechen.ts  ·  3  app/FachFlaeche.tsx  ·  Tests, CSS …
   als Feld/Schluessel (nicht Prosa): 118
```

**`herkunft` ist im Bundle bereits zweimal vergeben** — Werkzeug-Herkunft und Flächen-Herkunft. Das
Blatt gibt demselben Wort eine **dritte** Bedeutung, diesmal in der Domäne. Zwei Folgen:

1. **Der Befehl misst 138 vorher und ~150 nachher.** Er kann nicht zeigen, ob die Scheibe wirkt.
   Das ist dieselbe Klasse wie F-09/F-11, für die `scripts/zaehle.mjs` gebaut wurde: **auf die
   Zieldatei einschränken**, sonst zählt man das Haus statt das Zimmer. Vorschlag:
   `grep -o 'herkunft' resources/planner/hausplaner/domain/validation.ts | wc -l` — derselbe
   Befehl, den K-01 schon fährt, und er steht heute belegbar auf 0.
2. **Ein Name für drei Sachen** (L3). Ob das hinnehmbar ist — verschiedene Schichten, `app/` gegen
   `domain/` — oder ob das Feld `geometrieHerkunft` heißen soll, **entscheidet der Planner.** Ich
   melde die Kollision, ich löse sie nicht.

### Einwand 2 · **P3 · „`z.strict()` überall" hat null Treffer**

```text
grep -c 'z.strict()'  …/domain/validation.ts   -> 0
grep -c '\.strict()'  …/domain/validation.ts   -> 26
```

In der Sache richtig, im Wortlaut falsch. Wer die Zeile nachfährt, bekommt 0 und hält den ganzen
Absatz für unbelegt. Eine Formulierung, kein Baumangel — aber S-13 gilt auch für Begründungen.

### Was ich ausdrücklich NICHT beanstande

**K-06 zweite Zeile** („Bestand bekommt `zu_pruefen`, nicht `bestaetigt`") und **K-05 rote
Gegenprobe** sind die zwei Zusagen, an denen dieses Blatt hängt, und beide sind scharf formuliert.
Die Kantenliste nennt neun Kanten mit Zusage und zwei ohne — **mit Grund**, was zulässig ist. Der
Rückweg ist ehrlich benannt („sobald eine Datei als v3 abgelegt ist, ist der Rückweg eine
Rückmigration"). **Das ist die Sorte Blatt, die man bauen kann.**

**Ballbesitz: Planner** — Einwand 1 schließen (eine Zeile), Einwand 2 umformulieren, Status stellen.

## Nachbesserung nach ROT — Planner-Entscheidung (03.08.)

**Scope:** die zwei Server-Naehte (`SpeichereHausplanerDokumentRequest` `in:…` und
`scene-document-v2.schema.json` `const`) gehoeren zur N1-NAHT — derselbe Bau hat beide
gezogen, dieselbe Nachbesserung schliesst sie. KEIN neues Blatt.

**Die Weiche: v3-only am Server — BESTAETIGT** (`in:3` + `const 3`, wie im Baum begonnen):
ein v2,3-toleranter Server wuerde Dokumente OHNE geometrieHerkunft/freigabe persistieren,
nachdem v3 sie zur Pflicht macht — genau der stille Statusverlust, den B10 verbietet.
**Preis, benannt:** ein VOR dem Deploy geoeffneter Tab sendet v2 und bekommt einen LAUTEN
422 (kein stiller Verlust; Neuladen hebt auf v3, der Revisionskonflikt-Schutz 409 besteht).

```yaml
kriterien_nachbesserung:
  - id: K-N1
    typ: presence
    kritikalitaet: P1
    aussage: "Beide Naehte nehmen DIESELBE Version an: 3."
    pruefung:
      befehl: "grep -c \"'in:3'\" app/Http/Requests/Hausplaner/SpeichereHausplanerDokumentRequest.php"
      erwartet: "1"
    ausgangswert: "0 in HEAD ('in:2'); der Arbeitsbaum traegt bereits 'in:3' - unverbucht, 23-Zeilen-Diff"
  - id: K-N2
    typ: gate
    kritikalitaet: P1
    aussage: "Die DREI durch N1 rot gewordenen Tests sind gruen - NUR die drei sind die Latte (17 Bestandsrote = eigener Posten, nicht diese Naht)."
    pruefung:
      typ: gate
      ausgefuehrt_von: generator
      schritte: |
        HausplanerSpeichernNutzlastTest::test_gueltige_v2_dachszene_wird_vollstaendig_persistiert
        HausplanerSpeichernNutzlastTest::test_revisionskonflikt_bleibt_409_und_schreibt_nichts
        UebernahmeKnopfTest::test_zweite_uebernahme_nach_aenderung_version_additiv_altversion_unveraendert
  - id: K-N3
    typ: presence
    kritikalitaet: P2
    aussage: "Die Persistenz-Flaeche steht im ANKER (objekt.blade) - L-01 oben ist umgeschrieben, N2/N3/Z-07 erben."
    pruefung:
      befehl: "grep -c 'objekt.blade' docs/auftraege/ANKER-BROWSER.md"
      erwartet: "mindestens 1"
    ausgangswert: "0 vor dieser Nachbesserung"
```

B14: der Generator setzt nach der Nachbesserung erneut `gebaut` und VERBUCHT die laufende
Arbeit (der Request-Diff ist heute unverbucht im Baum); der Evaluator votet erneut.
