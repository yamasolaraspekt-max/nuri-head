# AUF-48-S4d — das Eigenschaften-Panel aus dem JSX

**Spur A** · **Heimat: ticket** · **Basis: `cdc320c0`** · *Geschnitten 30.07. 21:20*

## Warum diese Scheibe die wichtigste der fuenf ist

```text
Inline-Stellen in HausplanerApp.tsx gesamt:   118
davon in DIESEM Block:                         67
```

**Mehr als die Haelfte.** AUF-38 Scheibe 7 ist seit Wochen als „120 Inline-Stellen im JSX-Block"
geplant. **Gemessen liegt der Schwerpunkt hier** — wer S7 vorbereitet, bereitet in Wahrheit
diese Scheibe vor. *Das war vor dem Schnitt von S4 in fuenf Teile nicht bekannt; es ist der
Nebenertrag der Messung.*

## Umfang

**Aus `HausplanerApp.tsx` nach `resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx`:**

| Naht | Anker |
|---|---|
| Anfang | `{/* Rechtes Eigenschaften-Panel — AUF-83-T5 / K-01/K-02/K-05/K-06 …` |
| Ende | die Zeile **vor** `{/* Statusleiste */}` |

Gemessen gegen `cdc320c0`: **413 Zeilen**. Enthalten: die Reiter aus `PANEL_TABS` · der
Inhaltsbereich · die Mehrfachauswahl-Uebersicht (AUF-35a) · Sicht und Sperre je Knoten
(Dashboard v1 §5) · die L/T/U-Anbaumasse (Verdrahtung #1) · die Pruefungen mit Schwere als
Symbol **und** Text.

## Kriterien

```yaml
  - id: K-01
    aussage: "Keine Inline-Stelle verschwindet oder entsteht."
    befehl: >
      grep -c 'style={{' auf HausplanerApp.tsx + Kopfrahmen.tsx + EigenschaftenPanel.tsx
    erwartet: "die SUMME ist 133 — unveraendert seit S4a"
    hinweis: >
      Die Summe geht ueber ALLE drei Dateien, nicht nur die zwei neuen. Seit S4a wohnen
      15 Stellen im Kopfrahmen; wer sie weglaesst, misst 118 und nennt es vollstaendig.

  - id: K-02
    aussage: "Kein Zustand ist mitgewandert."
    befehl: "grep -cE 'usePlannerUiStore|useState|localStorage' EigenschaftenPanel.tsx"
    erwartet: "0 — das Panel nimmt Auswahl und Knoten entgegen und meldet Befehle zurueck"

  - id: K-03
    aussage: "Die geerbten Zusagen lesen BEIDE Dateien, nicht nur die Hauptfunktion."
    gegenbeweis: >
      Suche jede Zusage, die eine ABSENZ in `HausplanerApp.tsx` behauptet, und lass sie
      ueber beide Dateien laufen (`for (const [name, quelle] of [...])`).
      *Sonst wird sie gruen, weil der Inhalt eine Datei weiter gezogen ist.*
      In S4a hast du das fuer 24 Zusagen selbst gemacht — dieselbe Pflicht hier.

  - id: K-04
    aussage: "Die Mutationsprobe kommt VOR den Tests — und ihr Ergebnis ist der Befund."
    gegenbeweis: >
      Mindestens 12 Mutationen an unterschiedlichen Bindungen, bevor eine Zeile Test
      geschrieben wird: Sicht- und Sperr-Schalter vertauschen · eine Anbaumass-Eingabe an
      den falschen Knoten haengen · die Schwere-Symbole entkoppeln · einen Reiter aus
      PANEL_TABS entfernen. **Wie viele kommen durch?**
      In S4a waren es 9 von 15 — die 42 geerbten Zusagen pruefen Vorhandensein und
      Reihenfolge, kaum die Bindungen. *Rechne hier mit demselben Bild, aber miss es,
      statt es anzunehmen. Die Zahl gehoert in den Bericht, auch wenn sie 0 ist.*

  - id: K-05
    befehl: "npm run test:hausplaner"
    erwartet: "ueber 1504 (Stand nach S4a), kein roter Fall"

  - id: L-01
    aussage: "Die Buehne rendert nach dem Umbau noch."
    nachweis: >
      npm run build:hausplaner, dann /admin/hausplaner/studio → Expertenmodus →
      Taste W → zwei Klicks auf LEERER Flaeche → Wand mit Masszahl → Rueckgaengig.
      Zusaetzlich fuer DIESE Scheibe: ein Bauteil auswaehlen und pruefen, dass das Panel
      seine Werte zeigt, die Reiter wechseln und Sicht/Sperre schalten.
  - id: L-01-anker
    aussage: "Die Messung fand auf der richtigen Seite statt."
    nachweis: >
      VOR jeder anderen Zahl: HTTP 200 · querySelectorAll('canvas') mindestens 1 ·
      document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Reihenfolge

**S4b → S4c → S4d → S4e.** *S4b wurde um 21:20 korrigiert — sein alter Anfangs-Anker ist mit
S4a umgezogen und existiert nicht mehr.*


---

## NACHTRAG 22:00 — die 14 ungeprueften Abhaengigkeitslisten (Evaluator-Befund, geschlossen)

**Sein Befund, an S2 gemessen:** der AST-Vergleich, der die `useMemo`-Huellen prueft, sieht nur
diesen einen Hook.

```text
Hooks mit Abhaengigkeitsliste in HausplanerApp.tsx (per AST gezaehlt):
   useMemo       15      <- geprueft
   useCallback    7      <- UNGEPRUEFT
   useEffect      7      <- UNGEPRUEFT
   ----------------------
   UNGEPRUEFT:   14  von 29
```

**Fast die Haelfte faellt aus dem Sicherheitsnetz** — und bei einer Zerlegung ist das nicht
theoretisch. *Er nennt einen echten Fehlerpfad, den er versehentlich selbst erzeugt hat:*
`setSchienen(ladeSchienen(activeWorkspace))` **ohne** `activeWorkspace` in der Liste laedt beim
Bereichswechsel nicht neu — **und kein Test der Suite wird davon rot.**

**Sein Satz dazu: *„Der Aufwand, das zu schliessen, ist ein Wort."*** Aus
`expression.endsWith("useMemo")` wird eine Pruefung auf drei Namen, der Rest des Befehls bleibt.

```yaml
  - id: K-AB
    aussage: "Alle drei Hooks mit Abhaengigkeitsliste sind verriegelt, nicht nur useMemo."
    befehl: >
      Der AST-Vergleich aus dem S2-Blatt, mit einer Aenderung:
        alt   n.initializer.expression.getText(f).endsWith("useMemo")
        neu   ['useMemo','useCallback','useEffect'].some((h) =>
                n.initializer.expression.getText(f).endsWith(h))
      Vergleicht wie bisher Name gegen zweites Argument, ueber BEIDE Dateien.
    erwartet: "basis == kandidat und diff == [] fuer alle drei Hook-Arten"
    gegenbeweis: >
      Entferne EINEN Eintrag aus einer `useEffect`-Liste, die in die neue Datei gewandert ist.
      Bleibt die Pruefung gruen, sieht sie den Hook nicht — und das ist der Befund.
      *Herkunft: Evaluator am S2-Pruefstand. Er hat den Fehlerpfad versehentlich selbst erzeugt
      und ihn gemeldet, statt ihn stillschweigend zu beheben.*


---

## NEU VERMESSEN 22:05 — gegen `1406d2c6`, und diesmal ueber die VERSCHACHTELUNG

*Der Generator hat mir dreimal in Folge denselben Ankerfehler nachgewiesen. Sein Satz aus dem
S4c-Bericht: **„Die Anker werden nach Lesereihenfolge gewaehlt, JSX-Grenzen entstehen aber aus
Verschachtelung."** Fuer dieses Blatt habe ich deshalb nicht den Kommentar gesucht, sondern den
ausgeglichenen Block.*

### Alle Geschwister auf Einrueckung 8 zwischen dem Panel-Kommentar und der Statusleiste

```text
1126  {/* Rechtes Eigenschaften-Panel — AUF-83-T5 … */}     Kommentar
1130  {/* AUF-26/B3: overflowWrap + boxSizing … */}          Kommentar
1132  <div                                                   <- OEFFNET
1141  >                                                      (mehrzeilige Props enden hier)
1536  </div>                                                 <- SCHLIESST, gleiche Ebene
```

**Der Panel-Block ist EIN einziges ausgeglichenes `div` mit zwei Kommentaren davor.**
*Zwischen 1132 und 1536 liegt kein Geschwister auf Ebene 8 — nichts oeffnet dort, was spaeter
schliesst. Das ist der Bereich, den S4c mir gefehlt hat.*

| Naht | Anker |
|---|---|
| Anfang | `{/* Rechtes Eigenschaften-Panel — AUF-83-T5 …` (Zeile 1126) |
| Ende | das `</div>` auf **Einrueckung 8**, das zu dem `<div>` bei 1132 gehoert (Zeile 1536) |

**Gemessen gegen `1406d2c6`: 411 Zeilen, 67 Inline-Stellen.**
*Die 67 sind unveraendert — die Scheibe ist von S4a/S4b/S4c nicht beruehrt worden.*

**Die Zeile 1537 (`</div>` auf Einrueckung 6) gehoert NICHT dazu** — sie schliesst die Reihe,
in der Buehne und Panel nebeneinander stehen.
