# W-18 · Topologie prüfen — PRÜFUNG

## Die Zahl, die man hier NICHT nennen darf, ohne sie zu zerlegen

**Am Bau-Stand, zwei Muster:**

```text
IMPORT auf 'geometry/kontur' in __tests__/     EINE Datei    kontur.test.ts
WORT 'kontur' in __tests__/                    ZWOELF Dateien
```

> ***Elf der zwölf treffen die WERKZEUG-ID `'kontur'`*** (`toolRegistry.ts:230`) *und nicht das
> Prüfmodul* — `toolRegistry.test.ts`, `werkzeugVertrag.test.ts`, `buehne.test.ts`,
> `masseingabe.test.ts`, `markieren.test.ts` und weitere prüfen das **Zeichenwerkzeug**.
>
> **„Zwölf Wächter" wäre eine Zahl und kein Befund.** *Der Träger entscheidet, nicht das Wort.*

## Der eine Wächter: `__tests__/kontur.test.ts` — 173 Z., ELF Zusagen

### K-02 — die Geometrie selbst

```text
'Rechteck und L-Form schneiden sich nicht selbst'
'die Acht wird erkannt'
'eine Kante, die auf einer anderen LIEGT, zaehlt auch'
'drei Punkte auf einer Linie umschliessen keine Flaeche'
'unter der Mindestzahl wird abgelehnt, und zwar mit diesem Grund'
```

> ***„Die Acht wird erkannt" ist die tragende Zusage*** — *die liegende Acht ist die einfachste
> Kontur, die sich selbst schneidet, und sie fällt durch jede Prüfung, die nur benachbarte Kanten
> ansieht.*
>
> **Und „eine Kante, die auf einer anderen LIEGT, zählt auch" fängt den Grenzfall, den ein
> Schnittpunkt-Test übersieht:** *zwei kollineare, überlappende Kanten haben keinen einzelnen
> Schnittpunkt — sie haben unendlich viele.*

**Die letzte nennt „und zwar mit diesem Grund"** — *sie prüft nicht nur die Ablehnung, sondern
**welchen** `KonturGrund` sie trägt.* **Ein Urteil mit dem falschen Grund erzeugt den falschen Satz
für den Anwender.**

### Z-05 — die Einbettung, und sie ist strenger als die Geometrie

```text
'jeder Ablehnungsgrund hat einen Satz, der den Weg heraus nennt'
'die Kontur endet an DERSELBEN einen Stelle wie alles andere'
'die Hauptansicht raeumt die Kontur NICHT an einer eigenen Stelle auf'
'geschlossen wird mit DERSELBEN Fangtoleranz wie ueberall'
'Klick und Enter laufen durch DIESELBE Pruefung'
'die laufende Kontur ist auf der Buehne zu sehen'
```

> ***Vier dieser sechs sind EINDEUTIGKEITS-Zusagen*** — *sie halten fest, dass es **eine** Stelle
> gibt und nicht zwei: ein Abschluss, ein Aufräumweg, eine Fangtoleranz, ein Prüfpfad für Klick und
> Enter.*
>
> **Das ist die Zwei-Wahrheiten-Klasse, im Voraus verriegelt.** *„Klick und Enter laufen durch
> DIESELBE Prüfung" verhindert genau den Fehler, bei dem die eine Bedienart prüft und die andere
> nicht — und der fiele erst auf, wenn jemand Enter benutzt.*

**Und die erste ist eine Zusage über die SPRACHE:** *jeder Grund braucht einen Satz, der den Weg
heraus nennt.* **Sie prüft nicht, dass ein Text da ist, sondern dass er etwas leistet.**

## Was NICHT geprüft wird

- **`konturStatusText` hat keine eigene Zusage** für den Erfolgsfall — *der Satz „Kontur
  geschlossen — n Punkte" ist durch keinen Test gehalten, obwohl der Code seinen Zweck ausdrücklich
  begründet (`:165-166`).*
- **Kein Wächter über `geradenGeometrie`** in dieser Richtung — *er wäre auch sinnlos: die
  Konturprüfung benutzt es nicht (siehe `3-FORMELN`).*
- **Der offene Posten „Treppe ohne Zielgeschoss"** ist nicht geprüft, weil er nicht gebaut ist —
  siehe `7-GRENZEN`.
