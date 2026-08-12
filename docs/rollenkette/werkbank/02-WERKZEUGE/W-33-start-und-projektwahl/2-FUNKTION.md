# W-33 · Start und Projektwahl — FUNKTION

## Vier Komponenten in einer Datei, EIN Export

| Zeile | Komponente | eigener `hover`-Zustand? |
|---|---|---|
| `:52` | `Karte({ ico, titel, desc, onClick, grund })` | **ja** (`:53`) |
| `:104` | `ProjektKachel({ z, dominant })` | **ja** (`:105`) |
| `:165` | `HubKarte({ f, onKonfigurator })` | **ja** (`:166`) |
| `:193` | **`export function StartView({ onGuided, onKonfigurator, projekte = [] })`** | **nein** |

**DREI der vier halten einen eigenen `hover`-Zustand, der Export selbst keinen.** *Am Code gezählt,
jede Stelle geöffnet.*

> **Das ist eine Aussage über die Bauart und keine Feinheit:** *es gibt **keine gemeinsame Auswahl**
> und **keine Hochhebung** in den Rahmen.* **Jede Karte weiß nur von sich.** *Wer eine
> übergreifende Markierung bauen will — „diese eine ist ausgewählt" —, findet keinen Ort dafür und
> müsste ihn erst schaffen.*

## Die Datenquellen: drei von W-38, eine von außen

```ts
:4   import { T, FACH, PROJ, type FachHub } from './studioDaten';      W-38
:5   import type { ProjektEintrag } from './state/projekte';           von aussen
```

| Quelle | Was sie liefert | Wo sie wirkt |
|---|---|---|
| **`T`** | die Farbmarken | überall, wo ein Wert aus Zustand oder Messung kommt |
| **`FACH`** | die Fachplaner-Hubs | `:261` — `FACH.map(f => <HubKarte …>)` |
| **`PROJ`** | die Projekt-Karten | `:238`, `:240` — `PROJ[0].icon`, `PROJ[1].icon` |
| **`ProjektEintrag`** | **die echte Projektliste** | `:20` als `projekte?`, `:206`/`:221`/`:224` |

**`PROJ` wird an zwei von drei Stellen benutzt.** *`PROJ[2]` steht in den Daten und wird **nicht**
gerendert — stillgelegt, nicht gelöscht* (`:248-249`). **Siehe unten.**

## Befund (b) ist behoben — aber ANDERS, als es im Auftrag steht

**Der Auftrag beschreibt den Zustand VOR der Behebung:** *„die drei Projektkarten riefen alle
`onGuided(1)` — drei Versprechen, ein Ziel"*, und verlangt in W-33-2, das mit `projektKlick` als
heutigem Schutz zu benennen.

**Am Bau-Stand gemessen:** `grep -c "<Karte " StartView.tsx` → **2**.

```text
:238   Karte „Sanierungsplan"   KEIN onClick, dafuer `grund`:
       „Der Sanierungsablauf ist ein eigener Weg — er unterscheidet sich noch
        nicht vom Neubau-Ablauf."
:240   Karte „Hausplaner"       onClick={() => onGuided(1)}
:241   die dritte, „Weiterarbeiten", IST FORT (AUF-66)
```

> **Die Lösung ist nicht „drei Karten, drei Ziele", sondern zwei Karten und ein neuer Weg.**
> *`:242-245` sagt den Grund: „Sie ist überflüssig geworden: fortsetzen geht jetzt oben, mit einem
> Klick, am Projekt selbst. **Zwei Wege zu derselben Handlung sind kein Angebot, sondern eine Frage,
> die der Nutzer beantworten muss, bevor er arbeiten darf.**"*
>
> **Der Kommentar in `:234-237` trägt die alte Fassung noch** — *„drei Karten, drei Ziele"* — *und
> ist damit selbst überholt.* **Ich lasse ihn stehen: eine Ablesung ändert ihre Quelle nicht.**

**Und die zielllose Karte ist die zweite Hälfte derselben Haltung** (`:47-50`): *„eine Karte **ohne**
Ziel wird nicht klickbar gemacht, damit sie beschäftigt aussieht. Sie trägt `in Entwicklung` … und
**den Grund**, warum sie noch nirgendwohin führt. **Ohne `onClick` ist sie keine Schaltfläche**: keine
Rolle, kein Tastaturfokus, kein Zeiger."*

## Der Kern: `ProjektKachel` ist ein VERWEIS, keine Schaltfläche

```ts
:137-139   if (!z.adresse) return <div style={{…, cursor:'default'}}>{rumpf}</div>;
:142-143   <a href={z.adresse} …>
:147       onKeyDown: NUR die Leertaste — Enter loest der Verweis selbst aus
```

**Die Begründung steht wörtlich im Code (`:89-95`) und ist eine ausdrückliche Abweichung vom
Auftragswortlaut „Schaltfläche":**

> *„Das Ziel ist eine **Adresse**, kein Vorgang. Ein Verweis bringt von sich aus mit, was eine
> nachgebaute Schaltfläche erst nachbilden müsste — Fokus, Enter, Anzeige des Ziels in der
> Statuszeile, mittlere Maustaste, ‚in neuem Tab öffnen', Lesezeichen. Eine `role="button"`, die
> `location` setzt, nimmt all das weg und gibt nichts dafür."*

**Und die eine Lücke wird ausdrücklich geschlossen und nicht überspielt:** *die Leertaste löst bei
einem `<a>` nicht aus, also wird genau sie ergänzt* — **bewusst nicht mit `istAusloeser`, weil das
Enter mitprüft und Enter dann zweimal ankäme** (`:144-146`).

## Die Adresse kommt vom SERVER, nicht aus der Insel

```ts
state/projekte.ts, zu `adresse`:
„Sie kommt fertig vom Server (route(…) im Controller) und wird hier nur gelesen.
 Die Insel setzt keinen Pfad zusammen: ein zusammengebauter Pfad waere eine zweite
 Wahrheit ueber das Routing und braeche beim ersten Praefix — der Server weiss, wo
 er liegt, die Insel nicht."
```

**Das ist dieselbe Regel wie `TYP_MAP` in W-35 und die Übergangstabelle in W-40:** *eine Sache, ein
Ort.* **Hier ist der Ort der Server.**

## Die Grenzen zu W-39, W-38 und W-35

```text
W-33 IST      app/StartView.tsx: die vier Komponenten, der Leerzustand, die
              dominant-Hervorhebung, die drei Datenquellen, und der ehrliche
              Umgang mit einer fehlenden Projektliste.

W-33 IST NICHT
              der RAHMEN, der es rendert            -> W-39, Modus 'start'
                (BETRIEBSBESTAETIGT; W-39 importiert StartView namentlich)
              T, FACH und PROJ                      -> W-38 (BETRIEBSBESTAETIGT)
              state/projekte und der Weg, auf dem die Liste ankommt
                -> die NAHT; sie ist gebaut, siehe 7-GRENZEN
              die Konfigurator-Flaeche hinter onKonfigurator  -> W-35
```

> **Die Grenze zu W-39 ist gespiegelt und nicht neu gezogen** — *dort steht, dass der Rahmen
> `StartView` im Modus `start` rendert; hier steht dieselbe Grenze von der anderen Seite.*

## Zwei Ausgänge nach draußen

```ts
:11   onGuided: (schritt?: number) => void        benutzt in :240 mit onGuided(1)
:13   onKonfigurator: (name: string, fenster?: boolean) => void
                                                   durchgereicht an HubKarte (:261),
                                                   aufgerufen in :182 und :183
```

**`StartView` entscheidet nichts über das, was danach geschieht** — *es meldet nach oben.* **Wohin
`onGuided` und `onKonfigurator` führen, steht in W-39.**
