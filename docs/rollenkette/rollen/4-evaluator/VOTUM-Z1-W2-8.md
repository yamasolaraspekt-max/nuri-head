# VOTUM Z1-W2-8 — Werkzeugleiste in Baureihenfolge

**ABGENOMMEN (BROWSER) — fünf von fünf gültigen Kriterien. (b) ist gestrichen, (c) mit Befund.**

| Feld | Wert |
|---|---|
| Blattstand | `8460f98f` — der **berichtigte**; meine frühere Angabe `f5cfc933` war falsch und ist berichtigt |
| Bau | `3a4aafa1` · Ausgang `1c28d529` |
| Mein Stand | `67d3ff15` |
| gelesen_bis | 2026-08-22T21:59:22+02:00 |
| Bühne | Port 8105, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 34 |

## Z1-W2-8-a · Die Reihenfolge stimmt — in Leiste UND Menü — ERFÜLLT

Die Absage-Regel verlangt **beide** Ansichten („wer nur eine prüft, hat die halbe Zusage").
Beide gemessen, im Browser, am selbst gebauten Bündel:

**Leiste** (`belege/Z1-W2-8-leiste-gruen.png`) — die sieben in Soll-Folge, `Markieren` als
Auswahl-Werkzeug davor:

```
Markieren V · Wand W · Fenster F · Tür T · Treppe R · Decke K · Kontur U · Dach D
```

**Menü** „Architektur ▾" (`belege/Z1-W2-8-menue-gruen.png`) — dieselben sieben **zuerst**:

```
Wand · Fenster · Tür · Treppe · Decke · Kontur · Dach          (7, in Entwicklung)
Boden · Dachfenster · Fassade/Ansicht · Gaube · Raum ·
Schnitt · Stütze · Unterzug/Träger · Öffnung                    (9, gesperrt, dahinter)
```

**Selbst nachgezählt: neun** dahinter, wie das Blatt verlangt — unverändert in ihrer Folge.

Der Erzeugungsort trägt es ebenfalls: `toolPresentation.ts`, `zone: 'fix'`, aufsteigend
1 auswahl · 2 wand · 3 fenster · 4 tuer · 5 treppe · 6 decke · 7 kontur · 8 dach. Bewegt wurden
genau drei Werte (dach 5→8, treppe 7→5, kontur 8→7); `werkzeugThemen.ts` ist von **alphabetisch**
auf Baureihenfolge umgestellt.

## Z1-W2-8-b · GESTRICHEN

Im Blattstand `8460f98f` ist die Überschrift durchgestrichen; die DoR hat (b) entfernt, weil die
Bodenplatte `GP-0` braucht. Kein Gegenstand, keine Prüfung. **`bodenplatte` als Eintrag: 0** —
und das ist hier das Richtige, nicht der Mangel.

## Z1-W2-8-c · Der Tooltip ist ehrlich — ERFÜLLT, soweit im Bestand erfüllbar. MIT BEFUND.

**Was erfüllt ist:** Der Decken-Eintrag bleibt **ein** Eintrag, und sein Text nennt beides.
`toolRegistry.ts:152/155`, wörtlich:

```
meaning:        'Zwischendecke oder Abschlussdecke erzeugen — massiv oder mehrschichtig.'
tooltip.body:   'Zwischendecke oder Abschlussdecke erzeugen — massiv oder mehrschichtig.'
tooltip.title:  'Decke'          (vorher: 'Decke / Bodenplatte')
```

Beide Felder wurden nachgezogen — der Generator begründet das ausdrücklich damit, dass sie sonst
auseinanderliefen. Das ist richtig: zwei Orte für denselben Satz sind die Doppelung, gegen die
dieses Haus baut.

**DER BEFUND, und er reicht über dieses Blatt hinaus:** Der Messbefehl verlangt *„beide Tooltips
im Browser **sichtbar**"*. **Das ist im Bestand nicht herstellbar** — es gibt keinen Renderer:

```
'tooltip' in .tsx, Vorstand 1c28d529 :  0
'tooltip' in .tsx, Bau     3a4aafa1 :  0
'tooltip' in .ts   (Typ + Registry) : 70      <- der Griff traegt
WerkzeugGruppenMenue.tsx: title= nur fuer gruppe.label, Status und Anheft-Hinweis
HausplanerApp.tsx zoneTools('fix'):  kein Hilfetext
```

Im Browser gegengeprüft: `document.querySelectorAll('[title]')` findet 134 Elemente — **keines**
davon trägt einen Werkzeugtext; Hover über „Decke" zeigt nichts.

**Das ist kein Baumangel.** Die Zahl 0 steht vorher wie nachher; der Generator hat den Text
gesetzt, den das Kriterium verlangt, und niemand hat je einen Tooltip gerendert. Es ist eine
Lücke zwischen **Blatt-Messbefehl und Bestand** — und sie betrifft **alle** Tooltip-Texte der
Registry, nicht nur den der Decke. Ich melde das gesondert; für dieses Blatt werte ich (c) als
erfüllt, soweit es erfüllbar ist, und sage ausdrücklich, was **nicht** belegt ist.

*Ich habe vier Anläufe gebraucht, bis ich das erkannt habe — jeder Fehlgriff sah aus wie ein
Werkzeugfehler von mir. Erst die Messung im Code hat gezeigt, dass nicht mein Hover falsch war,
sondern dass es nichts zu hovern gibt.*

## Z1-W2-8-d · Rot-Probe, alt gegen neu — ERFÜLLT

Bündel aus dem Vorstand `1c28d529` gebaut, derselbe Bedienweg, dieselbe Ansicht:

```
ALT  Markieren V · Wand W · Fenster F · Tür T · Dach D · Decke K · Treppe R · Kontur U
NEU  Markieren V · Wand W · Fenster F · Tür T · Treppe R · Decke K · Kontur U · Dach D
```

Der Erzeugungsort bestätigt es: im Alt-Stand `dach` auf **ordnung 5**, vor `decke` (6) und
`treppe` (7). Bildbelege `belege/Z1-W2-8-leiste-alt.png` gegen `belege/Z1-W2-8-leiste-gruen.png`.

**Der Ortsbeleg hält:** im Rot-Lauf steht dieselbe Leiste mit denselben acht Einträgen — nur in
der alten Folge. Es fehlt nichts, es steht anders. Genau das soll (d) zeigen.

## Z1-W2-8-e · Lieferung grün und vollständig — ERFÜLLT

```
npm run test:hausplaner      1785 / 1785 · 0 fail
npm run test:hausplaner:dom    36 /   36 · 0 fail
npm run tsc:hausplaner       Rueckgabe 0
```

## Z1-W2-8-f · Diff auf den drei Dateien und ihren Tests — ERFÜLLT

```
app/tools/toolPresentation.ts · app/tools/toolRegistry.ts · app/tools/werkzeugThemen.ts
__tests__/toolPresentation.test.ts · __tests__/toolRegistry.test.ts · __tests__/leisteAusZonen.test.ts
public/hausplaner/hausplaner.js
ausserhalb Insel + Buendel : 0
geometry/ domain/ commands/: 0        <- Fachlogik leer
```

**Beide SHA genannt** (`1c28d529..3a4aafa1`) — die Absage-Regel verlangt es ausdrücklich, weil ein
`git diff` ohne Referenz nach dem Commit immer leer ist.

Der Dirigent hatte (f) am 19:44 um `toolPresentation.ts` und `werkzeugThemen.ts` erweitert; der
Planner hat nachgezogen. **Genau deshalb ist der Blattstand entscheidend:** mit meinem alten Stand
`fd2575ce` hätte ich (f) als verletzt gemeldet — gegen einen Bau, der die Anordnung befolgt.

**Ball:** Integrator (Transport). Der Tooltip-Befund geht gesondert an Planner und Dirigent.
