# ⇒ GENERATOR-AUFTRAG AUF-33 — L2 + L3: die 13 Rechen-Engines bekommen eine Fläche

**Vom:** Planner · **25.07.2026** · **Entsperrt durch Yama:** *das Panel-Muster wird die Treppe.*

**Vorher gelesen:** HEAD `368f2d7` · `git log -5` · Tafelzeile AUF-33 (§3a) ·
`app/tools/faehigkeiten.ts:84` · `geometry/treppenBerechnung.ts:10-61` · `app/FachFlaeche.tsx:100` ·
`app/dashboard/fachFlaechen.ts` (525 Z.) · Fahrplan `fahrplan-frontend-layout-hausplaner.md` Z. 87–88.

**Warum dieser Posten der höchste Hebel im Layout ist** (Fahrplan Z. 87): eine Fläche, die
**dreizehnmal** wiederverwendet wird. Ein Muster, das hier schief steht, steht dreizehnmal schief.

---

## 1. Zwei Stufen, und die zweite beginnt erst nach Abnahme der ersten

| Stufe | Inhalt | Gate |
|---|---|---|
| **L2** | **Nur die Treppe.** `engine-treppe` bekommt die Fläche, die zum Muster wird | Abnahme durch den Evaluator |
| **L3** | Die übrigen **12** nach demselben Muster, gruppenweise | erst nach L2-Abnahme |

**L3 wird in diesem Auftrag nicht gebaut.** Wer L2 meldet und L3 gleich mitliefert, hat das Muster
nicht prüfen lassen, bevor er es zwölfmal kopiert hat. Das ist der teuerste Zeitpunkt für einen Fehler.

## 2. Gemessener Bestand — es ist mehr da, als die Tafelzeile vermuten lässt

`app/tools/faehigkeiten.ts` deklariert für **alle 13** Engines bereits vollständig:

```
{ id: 'engine-treppe', label: 'Treppen-Auslegung', gruppe: 'treppe', art: 'engine',
  zustand: 'in_entwicklung', funktion: 'Stufen/Steigung DIN 18065',
  eingang: 'TreppenEingabe', ausgang: 'TreppenErgebnis',
  engineModul: 'geometry/treppenBerechnung', engineExport: 'berechneTreppe' }
```

`grep -c engineModul:` = **13**. Stichprobe auf vier Module (`treppenBerechnung`, `holzMengen`,
`holzBauteile`, `schifterListe`): **alle vier existieren**. Die Rechenfunktionen sind da; was fehlt,
ist ausschließlich die Fläche davor.

`geometry/treppenBerechnung.ts` — die Vorlage für das Muster:

```
export interface TreppenEingabe   6 Felder, davon 1 Pflicht (geschosshoehe), 5 optional mit Default
export interface TreppenErgebnis  8 Zahlen + pruefungen: TreppenPruefung[] + bestanden: boolean
export interface TreppenPruefung  { id, schwere: 'info'|'warnung'|'fehler', meldung, bestanden }
export function berechneTreppe(e: TreppenEingabe): TreppenErgebnis      // rein, ohne Seiteneffekt
```

Das Muster ist damit vorgezeichnet: **Eingabefelder → reiner Aufruf → Ergebniszahlen + Prüfliste.**
`FachFlaeche.tsx` (AUF-25) liefert bereits Kopf, Zweck, Zurück und Leerzustand — **das wird
wiederverwendet, nicht neu gebaut.**

## 3. Was **nicht** gebaut wird

**(a) Keine Rechenlogik im Panel.** Kein Grenzwert, keine Formel, keine Rundung in der Fläche. Sie
ruft `berechneTreppe` auf und zeigt, was zurückkommt. **Jede Zahl, die im Panel entsteht statt in der
Engine, ist ein Defekt** — und zwar der, der zwölfmal mitkopiert würde. `GRENZEN` und
`DURCHGANG_MIN` bleiben, wo sie sind.

**(b) Kein dynamischer Import über die Zeichenkette.** `engineModul: 'geometry/treppenBerechnung'` ist
eine **Deklaration**, kein Ladepfad. Ein `import(variable)` überlebt das Vite-Bundling nicht
zuverlässig. Stattdessen eine **explizite Zuordnung** id → `{ felder, aufruf }` mit statischem
Import, damit der Bundler alles sieht. Die Deklaration bleibt die Wahrheit über *welche* Engine —
die Zuordnung ist die Wahrheit über *wie* sie aufgerufen wird.

**(c) Kein Schreiben ins Modell.** L2 rechnet und zeigt. Ob und wie ein Ergebnis in die
`SceneDocument` wandert, ist eine eigene Frage mit Undo-, Schema- und Persistenz-Folgen — sie gehört
**nicht** in den Posten, der das Muster festlegt. Zurückgeben, nicht mitbauen.

**(d) Kein zweiter Zustandsbegriff.** `zustand: 'in_entwicklung'` schaltet erst dann auf
`verfuegbar`, wenn die Engine wirklich angeschlossen ist — für die Treppe also mit diesem Commit, für
die anderen zwölf **nicht**. Zwölf Kacheln, die „verfügbar" behaupten und es nicht sind, wären genau
die falschen Versprechen aus AUF-28.

## 4. Schnitt

1. **Zuordnung** `engine-treppe` → Feldliste aus `TreppenEingabe` (Label, Einheit mm, Pflicht/Default,
   Wertebereich) + statischer Aufruf `berechneTreppe`.
2. **Panel** in `FachFlaeche` einsetzen: Eingabefelder → Knopf → Ergebnisblock.
3. **Ergebnisdarstellung:** die acht Zahlen mit Einheit und Klartext-Label (`schrittmass` heißt
   „Schrittmaß 2·Steigung + Auftritt", nicht `schrittmass`), darunter die **Prüfliste** mit den drei
   Schweregraden. `fehler` muss sich von `warnung` **nicht nur durch Farbe** unterscheiden
   (UI-Bauordnung / WCAG 1.4.1) — Zeichen oder Wort dazu.
4. **`bestanden: false`** ist ein normaler, gültiger Zustand, kein Fehlerbildschirm: die Zahlen bleiben
   sichtbar, die verletzte Prüfung wird benannt. Ein Architekt will sehen, *wie knapp* er daneben liegt.
5. **Zustand der Treppen-Kachel** auf `verfuegbar`.

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Testzahl vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `renderers/`, `scene.types` — **und `geometry/`**: die Engine
   wird gelesen, nicht geändert. Null Zeilen Diff in `geometry/treppenBerechnung.ts`.
3. **Keine Rechnung im Panel:** `grep` belegt, dass in der neuen Fläche weder `GRENZEN`-Werte noch
   `Math.round`/`/ 10`-Rundungen noch DIN-Grenzmaße vorkommen.
4. **Wertgleichheit gegen die Engine:** ein Test ruft `berechneTreppe` direkt und vergleicht mit dem,
   was die Fläche anzeigt — für mindestens drei Eingaben, davon **eine mit `bestanden: false`**.
5. **Alle drei Schweregrade** kommen in einem Test vor, und der Unterschied `fehler`/`warnung` ist
   nicht allein farblich (Assertion auf Zeichen oder Text).
6. **Kein dynamischer Import:** `grep` belegt, dass kein `import(` mit Variable vorkommt.
7. **Die anderen zwölf bleiben `in_entwicklung`** — testverriegelt, dass genau **eine** Engine auf
   `verfuegbar` steht.
8. **Mutations-Gegenbeweis:** die Feldzuordnung verfälschen (z. B. `geschosshoehe` auf
   `laufbreite` legen) ⇒ mindestens ein Test rot. Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen.** Der **Bundle-Rebuild ist ein eigener, zweiter Commit**
   unmittelbar danach, ausschließlich mit dem Artefakt (§8 Punkt 2b in
   `docs/agents/06-laufzeiten-und-takt.md`).
10. **Klassifikation: `sichtbar`.** Rebuild-Beleg (`grep -c` auf eine neue Zeichenkette) im Bericht,
    Sichtprobe gehört in die Abnahme.

## 6. Was zurückgegeben wird

- **Persistenz des Ergebnisses** (Punkt 3c) — eigener Posten, nicht hier.
- Jede Engine, deren `eingang` sich nicht aus dem vorhandenen Modell füllen lässt: **benennen**, nicht
  mit Platzhaltern füllen. Das ist die Naht, die L3 später real begrenzt — sie soll früh sichtbar sein.
