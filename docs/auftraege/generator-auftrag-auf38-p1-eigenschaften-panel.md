# AUF-38-P1 — Inline-Stile aus `EigenschaftenPanel.tsx` in die Stilschicht

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 30.07. 22:41*

## Warum dieses Blatt „P1" heisst und nicht „Scheibe 7"

**Die alte Scheibe 7 gibt es nicht mehr.** Sie war beschrieben als *„`HausplanerApp.tsx`, 78
Stellen"* und **gesperrt** mit dem gemessenen Grund *„drei Posten in einer 2305-Zeilen-Datei"*.

**AUF-48 hat diesen Grund aufgeloest, nicht erfuellt:**

```text
HausplanerApp.tsx   2511 -> 1130 Zeilen,  von 78 Inline-Stellen bleiben 4
```

*Wer „Scheibe 7" heute zieht, sucht 78 Stellen in einer Datei, die 4 hat.* **Stattdessen ein
Blatt je Datei — und dieses ist das erste, weil es zwei Drittel traegt.**

## Bestand (gemessen, bevor ein Kriterium formuliert wurde)

```text
Datei                      rahmen/EigenschaftenPanel.tsx     551 Zeilen
letzter Commit             15de0857 (30.07. 22:17, AUF-48-S4d)
Tests, die sie einlesen    __tests__/eigenschaftenPanel.test.ts · __tests__/_zerlegteApp.ts
style={{                    71
style={bezeichner}          58    <- BEIDE Schreibweisen (Evaluator-Auflage zu AUF-38)
className=                   2
                           ---
SUMME                      129    von 196 ueber alle sechs Dateien
```

**Die Stilschicht existiert und ist erprobt:** `public/hausplaner/hausplaner.css` mit **207**
`hp-*`-Klassen, gespeist aus `app/stil/tokenVariablen.ts`, verriegelt durch
`__tests__/stilschicht.test.ts` (171 Zusagen). *Scheibe 1 bis 3 haben die Mechanik bewiesen —
hier wird sie angewandt, nicht neu erfunden.*

## Kriterien

```yaml
  - id: K-01
    aussage: "Die Zahl faellt, und zwar in BEIDEN Schreibweisen."
    befehl: >
      In EigenschaftenPanel.tsx: Vorkommen von `style={{` (grep -o) und von
      `style={bezeichner}` (Muster style=\{(?!\{)[A-Za-z_]).
    erwartet: "0 und 0"
    gegenbeweis: >
      Danach die SUMME ueber alle sechs zerlegten Dateien zaehlen: sie muss um genau
      129 gefallen sein (196 -> 67). **Faellt sie um mehr, ist Inhalt verschwunden;
      faellt sie um weniger, ist etwas in eine Nachbardatei gewandert.**

  - id: K-02
    aussage: "Kein roher Farbwert, keine rohe Groesse in den neuen Regeln."
    befehl: "grep -cE '#[0-9a-fA-F]{3,6}|rgb\\(' in den neu hinzugefuegten CSS-Zeilen"
    erwartet: >
      0 — jede Farbe kommt aus `var(--hp-…)`. *Das ist die Regel aus Scheibe 1-3 und der
      Grund, warum die Stilschicht ueberhaupt etwas wert ist.*

  - id: K-03
    aussage: "Die Verriegelung der Stilschicht bleibt gruen."
    befehl: "npm run test:hausplaner -- --filter=stilschicht"
    erwartet: >
      gruen, 171 Zusagen unveraendert. **Besonders `assert.doesNotMatch(quelle, /@media/)`** —
      die Zusage, die *„Responsive ist L7"* traegt. Wer sie bricht, hat den falschen Weg genommen.

  - id: K-04
    aussage: "Das Panel sieht gleich aus — gemessen, nicht betrachtet."
    nachweis: >
      Vor und nach dem Umbau: ein Bauteil auswaehlen und `getBoundingClientRect()` von
      Panel, Reiterleiste und erstem Feld notieren. **Die Werte muessen gleich sein.**
    gegenbeweis: >
      Aendere EINE Klasse absichtlich (etwa `padding`) und miss erneut. Bleiben die Werte
      gleich, misst die Probe nicht, was sie zu messen behauptet.

  - id: K-05
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    gegenbeweis: >
      Mindestens 8 Mutationen an dem, was der Umbau anfasst: eine Klasse am falschen
      Element · zwei Klassen vertauscht · eine Regel ohne Wirkung. **Wie viele kommen durch?**
      *In AUF-48 waren es ueber acht Scheiben 38 von 52. Die Zahl gehoert in den Bericht,
      auch wenn sie 0 ist.*

  - id: K-06
    aussage: "Die A11y-Entscheidungen des Panels ueberleben."
    befehl: "npm run test:hausplaner -- --filter=eigenschaftenPanel"
    erwartet: "gruen, Zahl der Zusagen UNVERAENDERT"
    hinweis: >
      **S4d hat gemessen, dass zwei davon durch nichts geschuetzt waren**, und sie geschlossen:
      *Schwere als Symbol UND Text* sowie *die Rueckfrage vor dem Entsperren*.
      `eigenschaftenPanel.test.ts` haelt sie jetzt — **diese Datei wird nicht angefasst.**

  - id: L-01
    nachweis: >
      npm run build:hausplaner, dann /admin/hausplaner/studio → Expertenmodus →
      Wand zeichnen → auswaehlen → Panel zeigt ihre Werte, Reiter wechseln,
      Sicht- und Sperrschalter reagieren.
  - id: L-01-anker
    nachweis: >
      VOR jeder anderen Zahl: HTTP 200 · querySelectorAll('canvas') mindestens 1 ·
      document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Danach

**P2 bis P5** je Datei: `GruppenzeileUndSchiene` (27) · `FussUndUeberlagerungen` (20) ·
`Kopfrahmen` (16) · `HausplanerApp` (4). *`Buehne.tsx` hat null — dort ist nichts zu tun.*
**Erst nach P1**, weil P1 die Mechanik an der schwersten Datei zeigt.


---

## NACHTRAG 22:54 — **K-03 steht auf einer Zusage, die selbst einen Befund traegt**

**PB-010 (Pruefer):** *„`stilschicht.test.ts` — Wirkungs-Zusage prueft gegen **3 tote
Bezeichner**."* Rang P3, Zustand *ANGENOMMEN, ABER ANDERS GESCHNITTEN*.

**Das ist genau die Datei, auf die K-03 dieses Blattes sich stuetzt.** *Eine Zusage, die gegen
Bezeichner prueft, die es nicht mehr gibt, ist an diesen Stellen gruen, ohne etwas zu pruefen.*

### Was daraus folgt — und was ausdruecklich NICHT

**K-03 bleibt gueltig.** Der Kern der Zusage — `assert.doesNotMatch(quelle, /@media/)` und
`/!important/` — traegt unabhaengig davon; er prueft **Abwesenheit in der Quelle**, nicht
einen Bezeichner. *Die drei toten Stellen betreffen die Wirkungs-Zusage, nicht diese beiden.*

**Aber der Bericht nennt sie.** Wer P1 baut, faellt ueber sie:

```yaml
  - id: K-03b
    aussage: "Die drei toten Bezeichner aus PB-010 sind benannt, nicht stillschweigend geheilt."
    nachweis: >
      Nenne im Bericht, WELCHE Bezeichner `stilschicht.test.ts` prueft, die es in der
      gebauten CSS nicht gibt. **Beheben ist NICHT Teil dieses Auftrags** — PB-010 liegt
      beim Evaluator und ist anders geschnitten worden.
    warnung: >
      *Meine eigene Zaehlung dazu war unbrauchbar: ich habe `hp-`-Vorkommen im Test gegen
      die CSS gehalten und 26 "tote" gefunden — darunter `hp-accent` (das ist die CSS-Variable
      `--hp-accent`, keine Klasse) und die Praefixe `hp-ef-` und `hp-gf-`.* **Der Befehl mass
      die Gestalt, nicht die Sache.** Der Pruefer nennt drei; seine Messung gilt, meine nicht.
      **Wer hier zaehlt, sagt dazu, WONACH er zaehlt.**
```
