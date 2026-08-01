# PB-023 + PB-024 — die Insel bekommt eine Regressionsfläche und hängt an den Tokens

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 11:1x*

```yaml
auftrag:
  id: PB-023+024
  status: bereit
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

## Die Entscheidung

**Nicht alle 256 in den Styleguide.** Ein Styleguide mit 256 Kacheln liest niemand, und ein Blatt,
das 256 Einträge verlangt, wird nicht gebaut, sondern abgebrochen.

**Stattdessen: die Familien.** Die Klassen tragen Präfixe (`hp-ep-`, `hp-gz-`, `hp-schiene-`, …).
**Je Familie ein Musterblock im Styleguide**, der ihre tragenden Zustände zeigt. Das ist die
Referenzfläche, die die Bauordnung meint — nicht ein Katalog.

**Und die Tokens zuerst.** Ein Musterblock, der 42 Hexwerte zeigt, verewigt sie. **Erst die Insel
an `--sa-*` hängen, dann in den Styleguide.**

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/studioDaten.ts
    - resources/planner/hausplaner/hausplaner.css
    - resources/views/admin/styleguide/index.blade.php
  population_command: "grep -oE '#[0-9a-fA-F]{3,8}' resources/planner/hausplaner/app/studioDaten.ts | wc -l"
  ausschluesse:
    - stelle: "Ein Musterblock je Klasse (256 Stueck)"
      grund: "Ein Styleguide mit 256 Kacheln wird nicht gelesen. Gefordert ist die Referenzflaeche je Familie, nicht der Katalog."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "Die Insel-Palette nennt keinen rohen Hexwert mehr."
    pruefung:
      befehl: "grep -oE '#[0-9a-fA-F]{3,8}' resources/planner/hausplaner/app/studioDaten.ts | wc -l"
      erwartet: "0"
    ausgangswert: "42 (gemessen 01.08.)"
    gegenbeweis: |
      Rotprobe des Planners: Kopie der Datei plus eine Zeile mit einem Hexwert -> 43.
      Der Zaehler kann steigen, also kann das Kriterium rot werden.
      Achtung: die Null allein ist auch erreichbar, indem man die Werte in eine Nachbardatei
      schiebt. Deshalb zaehlt K-02 gegen.

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Die Insel-Stilschicht verweist auf die CRM-Tokens."
    pruefung:
      befehl: "grep -o 'var(--sa-' resources/planner/hausplaner/hausplaner.css | wc -l"
      erwartet: "mindestens 12 - so viele Tokens fuehrt der Styleguide heute"
    ausgangswert: "0"
    gegenbeweis: |
      Rotprobe des Planners: eine erfundene Zeile 'a { color: var(--sa-fg); }' -> 1.
      Der Befehl trifft. HINWEIS: hier stand zuerst `grep -c`. Der Validator hat es gefangen -
      `grep -c` liefert bei null Treffern `exit 1`, und ein Kriterium, das schon am Ausgangswert
      abbricht, misst nichts. `grep -o ... | wc -l` loest es. Das ist genau die Falle, die in
      meinen eigenen Notizen steht - und ich bin trotzdem hineingelaufen. Zusammen mit K-01 heisst das: die Farben sind nicht verschwunden,
      sie sind verdrahtet.

  - id: K-03
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

  - id: K-04
    typ: behavioural
    aussage: "Nichts an der Insel sieht anders aus."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: "gruen, Zusagen unveraendert"

  - id: K-05
    typ: behavioural
    aussage: "Browsertest: Styleguide UND Insel."
    pruefung:
      typ: browser
      schritte: |
        /admin/styleguide - die neuen Musterbloecke sind da und sehen aus wie in der Insel.
        /admin/hausplaner/studio - getBoundingClientRect von Schienenkopf und Panel
        vor und nach dem Umbau gleich.
```

## Rückweg

Reine Stil- und Vorlagenänderung, kein Datenpfad. Der Commit lässt sich zurückdrehen.
**Entdeckung:** fällt etwas um, fällt es auf der Referenzfläche auf — das ist ihr ganzer Zweck.
