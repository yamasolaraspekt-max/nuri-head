# PB-023 + PB-024 — die Insel bekommt eine Regressionsfläche und hängt an den Tokens

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 11:1x*

```yaml
auftrag:
  id: PB-023+024
  status: aktiv   # das EINE aktive Blatt (S-01), ueberarbeitet 01.08. nach der Rueckgabe des Generators
```

## Warum das zusammengehört

**Zwei Befunde des Prüfers, ein Loch.** Die UI-Bauordnung schreibt den Styleguide als
**Referenzfläche für visuelle Regression** vor. Der Hausplaner ist eine Insel im CRM — und die
Insel kommt dort nicht vor. **Was nicht auf der Referenzfläche steht, kann nicht regressieren,
ohne dass es jemand zufällig sieht.**

## Bestand — gemessen 01.08. 11:1x

```text
grep -oE '\.hp-[a-zA-Z0-9-]+' resources/planner/hausplaner/hausplaner.css | sort -u | wc -l   ->  256
grep -oE 'hp-[a-zA-Z0-9-]+' resources/views/admin/styleguide/index.blade.php | sort -u | wc -l ->   0
grep -oE '#[0-9a-fA-F]{3,8}' resources/planner/hausplaner/app/studioDaten.ts | wc -l           ->  42
grep -c 'var(--sa-' resources/planner/hausplaner/hausplaner.css                                ->   0
wc -l < resources/views/admin/styleguide/index.blade.php                                       -> 192
grep -oE '\-\-sa-[a-z0-9-]+' resources/views/admin/styleguide/index.blade.php | sort -u | wc -l ->  12
```

**256 Klassen, null auf der Referenzfläche. 42 Hexwerte in der Insel, null Verweise auf die
CRM-Tokens.** *Der Prüfer nannte 175 bzw. 42 — die 175 stammen aus einer früheren Messung, die 256
sind heute. Beide Male ist die zweite Zahl null, und darauf kommt es an.*

## Die Entscheidung — **K-01 ist gestrichen.** Der Generator hat recht.

**Er hat das Blatt gezogen, gemessen und zurückgegeben, bevor er eine Zeile Code geschrieben hat.
Das ist der Weg, den die Governance vorsieht, und er hat zwei Dinge gefunden, die ich hätte finden
müssen:**

**1. Ein Canvas löst keine CSS-Variable auf — er malt still die vorherige Farbe.**

```text
ctx.fillStyle = '#000000'
ctx.fillStyle = 'var(--sa-fg)'   ->  fillStyle bleibt '#000000'   (Zuweisung verworfen)
```

*Trüge `T` künftig `var(--sa-…)`-Zeichenketten, würde jede Wand in der zuletzt gültigen Farbe
gemalt — **nicht gar nicht, sondern still falsch**. Dieselbe Klasse wie PB-047: ein falscher Wert
ist schlimmer als ein leerer, weil er richtig aussieht.*

**2. Das CRM hat 14 Tokens, die Insel 34 Rollen — 17 haben kein Gegenstück.** Für Flächen, Schrift,
Haarlinien und Zeichenflächen-Farben gibt es im CRM schlicht kein Token. **Meine geforderte Null war
nur erreichbar, indem man entweder das CRM erweitert (steht nicht im Umfang) oder die Werte in eine
Nachbardatei schiebt (im Blatt ausgeschlossen).** *Ein Kriterium, das nur über einen ausgeschlossenen
Weg erfüllbar ist, ist kein Kriterium.*

### Was daraus folgt

**`studioDaten.ts` behält seine echten Farbwerte.** Sie sind die eine Wahrheit für die Zeichenfläche;
`tokenVariablen.ts` bleibt DOM-frei und ohne Fenster prüfbar (`grep -c getComputedStyle` → 0).

**Verdrahtet wird die CSS-Seite** — dort, wo ein CRM-Gegenstück existiert. Das ist der erreichbare
Teil von PB-024 und der ganze Ertrag von PB-023.

**Zwei Nachfolger, die ich getrennt schneide und heute NICHT mitbaue:**

- **PB-024-N1** — die 17 fehlenden `--sa-`-Rollen im CRM anlegen. *Eigenes Blatt: es fasst
  `sa-ui.blade.php` an, eine CRM-weite Datei, und braucht ein eigenes Votum.*
- **PB-024-N2** — die Brücke für die Zeichenfläche: sollen Canvas-Farben zur Laufzeit aus den
  Tokens aufgelöst werden (`getComputedStyle`)? *Eigene Entscheidung mit eigenem Risiko — fehlt ein
  Token, ist die Farbe leer. Nicht nebenbei.*

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/hausplaner.css
    - resources/views/admin/styleguide/index.blade.php
  population_command: "grep -o 'var(--sa-' resources/planner/hausplaner/hausplaner.css | wc -l"
  ausschluesse:
    - stelle: "Die 42 Hexwerte in studioDaten.ts"
      grund: "Konva loest keine CSS-Variable auf und malt sonst still die vorherige Farbe. 17 der 34 Rollen haben ausserdem kein CRM-Gegenstueck. Gemeldet vom Generator am 01.08. vor dem Bau; K-01 daraufhin gestrichen. Nachfolger PB-024-N1 und N2."
      entschieden_von: planner
    - stelle: "resources/views/admin/layouts/partials/sa-ui.blade.php"
      grund: "CRM-weite Token-Datei. Eine Erweiterung braucht ein eigenes Blatt und ein eigenes Votum."
      entschieden_von: planner
    - stelle: "Ein Musterblock je Klasse (256 Stueck)"
      grund: "Ein Styleguide mit 256 Kacheln wird nicht gelesen. Gefordert ist die Referenzflaeche je Familie, nicht der Katalog."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Insel-Stilschicht verweist auf die CRM-Tokens, wo es eines gibt."
    pruefung:
      befehl: "grep -o 'var(--sa-' resources/planner/hausplaner/hausplaner.css | wc -l"
      erwartet: "mindestens 12"
    ausgangswert: "0 (gemessen 01.08. 11:1x)"
    gegenbeweis: |
      Rotprobe des Planners: eine erfundene Zeile 'a { color: var(--sa-fg); }' -> 1, der Befehl trifft.
      HINWEIS: hier stand zuerst `grep -c`. Der Validator hat es gefangen - `grep -c` liefert bei
      null Treffern `exit 1`, und ein Kriterium, das schon am Ausgangswert abbricht, misst nichts.
      Die Falle steht in meinen eigenen Notizen, und ich bin trotzdem hineingelaufen.

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Die Zeichenflaechen-Palette bleibt frei von CSS-Variablen."
    pruefung:
      befehl: "grep -o 'var(--' resources/planner/hausplaner/app/studioDaten.ts | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Dieses Kriterium schuetzt gegen die FALSCHE Behebung. Wer die Hexwerte durch
      `var(--sa-...)`-Zeichenketten ersetzt, macht die Datei "sauber" - und Konva malt danach
      still die zuletzt gueltige Farbe. Rotprobe des Planners: Kopie plus eine Zeile mit
      `var(--sa-fg)` -> 1.

  - id: K-03
    typ: absence
    aussage: "tokenVariablen.ts bleibt DOM-frei - ohne Fenster pruefbar."
    pruefung:
      befehl: "grep -o 'getComputedStyle\\|document\\.' resources/planner/hausplaner/app/stil/tokenVariablen.ts | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Die Laufzeit-Aufloesung ist der naheliegende Kurzschluss, wenn K-01 nicht weit genug traegt.
      Sie ist eine eigene Entscheidung (PB-024-N2), nicht ein Nebenweg dieses Blattes.
      Rotprobe des Planners: Kopie plus eine Zeile mit getComputedStyle(document.body) -> 3
      (der Befehl zaehlt beide Muster, und `document.` kommt in der Zeile zweimal vor -
      die Zahl ist also nicht 1; erwartet wird schlicht "groesser als 0" beim roten Fall).

  - id: K-04
    typ: presence
    kritikalitaet: P1
    aussage: "Die Insel steht auf der Referenzflaeche - je Familie ein Musterblock."
    pruefung:
      befehl: "grep -oE 'hp-[a-zA-Z0-9-]+' resources/views/admin/styleguide/index.blade.php | sort -u | wc -l"
      erwartet: "mindestens 6 - eine je Familie"
    ausgangswert: "0"
    gegenbeweis: |
      Rotprobe des Planners: Kopie des Styleguide plus eine Zeile mit class="hp-gz-kopf" -> 1.
      Die Familien werden VORHER gezaehlt und im Bericht genannt:
      grep -oE 'hp-[a-z]+-' resources/planner/hausplaner/hausplaner.css | sort -u

  - id: K-05
    typ: behavioural
    aussage: "Nichts an der Insel sieht anders aus."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: "gruen, Zusagen unveraendert"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Browsertest: Styleguide UND Zeichenflaeche."
    pruefung:
      typ: browser
      schritte: |
        /admin/styleguide - die neuen Musterbloecke sind da und sehen aus wie in der Insel.
        /admin/hausplaner/studio - eine Wand zeichnen und die gemalte Farbe ablesen
        (ctx.fillStyle bzw. Pixelprobe). Sie muss dieselbe sein wie vorher.
        Das ist die Probe gegen den Befund des Generators: eine still falsche Farbe
        sieht richtig aus, solange man nicht misst.
```

## Rückweg

Reine Stil- und Vorlagenänderung, kein Datenpfad. Der Commit lässt sich zurückdrehen.
**Entdeckung:** fällt etwas um, fällt es auf der Referenzfläche auf — das ist ihr ganzer Zweck.
