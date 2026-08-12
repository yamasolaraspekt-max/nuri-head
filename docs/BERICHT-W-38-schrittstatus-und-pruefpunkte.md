# Baubericht W-38 — Schritt-Status und Prüfpunkte, abgelesen

```yaml
auftrag: "W-38"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-38-schrittstatus-und-pruefpunkte.md
art: "STUFE 6 · ABLESUNG — der Code existiert, es wird nichts vorgegeben"
basis_sha: 44de4c8b
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Zwei Fehltreffer stehen in der Erhebung des Auftragsblattes, und einer davon ist meiner.**
> *Beide sind unten benannt und gemessen. Keiner blockiert etwas — die Ablesung ist vollständig.*

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-38-schrittstatus-und-pruefpunkte/
  1-ZWECK.md   2-FUNKTION.md   3-FORMELN.md   4-BEDIENUNG.md
  5-CODE/LIESMICH.md   6-PRUEFUNG.md   7-GRENZEN.md
REGISTER.md   Zeile 125:  LEER -> BESCHRIEBEN
```

**Keine Datei außerhalb der Werkbank berührt.** *`studioDaten.ts` ist gelesen, nicht geändert —
eine Ablesung schreibt nicht in ihre Quelle.*

## W-38-1 · Vier Stufen, und der Nachweis dass es vier sind

```ts
studioDaten.ts:163
export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';
```

**Nicht „vier" behauptet, sondern an drei unabhängigen Stellen belegt:** *die Typzeile selbst · das
`Record<SchrittStatus, string>` in `:255`, das Vollständigkeit erzwingt · und
`__tests__/gefuehrteEhrlich.test.ts:42`, das die Schlüsselmenge gegen
`['ok','open','prog','warn']` prüft.* **Steht in `2-FUNKTION.md`.**

## W-38-2 · Vier Datenformen mit ihren Feldern

| Form | Fundstelle | Optionale Felder |
|---|---|---|
| `Pruefpunkt` | `:164` | — |
| `Aufgabe` | `:165` | **`warn?`** · **`detail?`** |
| `Empfehlung` | `:166` | **`cfg?`** |
| `Fahrschritt` | `:167-174` | — *(`empfehlung` ist nicht optional, sondern verpflichtend nullbar)* |

**Für jedes optionale Feld steht in `2-FUNKTION.md`, was das Weglassen AUSSAGT** — nicht dass es
weggelassen werden darf. *Belegt: `cfg: true` kommt genau **einmal** vor (`:206`), und **sechs** der
elf stillgelegten Schritte tragen ausdrücklich `empfehlung: null` (`:190, :216, :221, :231, :236,
:241`).*

## W-38-3 · STATUS_LABEL vollständig

```ts
studioDaten.ts:255-257
ok: 'Vollständig'   prog: 'In Bearbeitung'   warn: 'Prüfung erforderlich'   open: 'Offen'
```

**Alle vier Zuordnungen in `4-BEDIENUNG.md`**, mit der Begründung aus `:245-254`, warum `ok` nicht
mehr das frühere Freigabewort trägt: *es behauptete einen Vorgang, den es nicht gegeben hat.*

## W-38-4 · Die Nutzer je Typ — und vier verworfene Fehltreffer

**Gemessen, dann JEDE Trefferzeile gelesen.** *Das Lesen war der eigentliche Ertrag:*

```text
SchrittStatus   GuidedView.tsx:4,:18,:22 · fahrschritte.ts:27,:43 · gefuehrteEhrlich:23,:47
Pruefpunkt      fahrschritte.ts:27,:35,:37,:43,:113                      NUR diese eine Datei
Fahrschritt     GuidedView.tsx:4,:15 · fahrschritte.ts:27,:74,:114,:115,:118
STATUS_LABEL    GuidedView.tsx:4,:71 · gefuehrteEhrlich:23,:38,:42,:43,:44,:45
Aufgabe         KEIN Nutzer ausserhalb studioDaten.ts
Empfehlung      KEIN Nutzer ausserhalb studioDaten.ts
```

**Verworfen, weil Fließtext und nicht Typ:**

```text
GuidedView.tsx:119             {/* Seitenpanel: Aufgabe + Empfehlung + … */}   Kommentar
sparrenBerechnung.ts:30        // L/300 (Empfehlung Endzustand, …)             Kommentar
enginePanelTgaHeizung.test.ts:65  '… mit verletztem Pruefpunkt bleibt …'       Testname
enginePanelRest.test.ts:73        'ein verletzter Pruefpunkt bleibt …'         Testname
```

> **Der erste Fehltreffer steht im Auftragsblatt.** *Dessen Abschnitt 2 nennt
> `app/dashboard/enginePanels.ts` als `Pruefpunkt`-Nutzer.* **Gemessen: diese Datei nennt
> `studioDaten` an KEINER Stelle und importiert nichts daraus.** *Ihr einziger Treffer ist
> `enginePanels.ts:235` — ein deutscher Anzeigetext:*
> `grundlage: 'Auslegung nach Verlegeabstand … Pruefpunkte zu Leistung …'`
>
> **H-6, wörtlich: ein Wort ist kein Beleg; erst die Stelle ist einer.** *Die beiden dort ebenfalls
> genannten Testdateien sind derselbe Fall. Das Blatt sagt zu Abschnitt 2 selbst „meine Erhebung,
> **nachzumessen**" — genau das ist passiert, und W-38-4 hat es erzwungen, weil es die Trefferzeile
> verlangt und nicht das Wort „wird verwendet".*

**Dass `Aufgabe` und `Empfehlung` keinen äußeren Nutzer haben, ist kein Mangel:** *sie werden
ausschließlich über `Fahrschritt` benutzt — `fahrschritte.ts:114` greift sie mit
`Fahrschritt['aufgaben']` und `Fahrschritt['empfehlung']` ab, also indiziert, ohne ihre Namen zu
nennen.* **Bestandteile, keine eigenständigen Schnittstellen.**

## W-38-5 · Die zwei Attrappen, gekennzeichnet und bewacht

```text
ZULETZT_STILLGELEGT  :157   startEhrlich.test.ts:24, :37, :43, :44
STEPS_STILLGELEGT    :186   gefuehrteEhrlich.test.ts:100 · fahrschritte.test.ts:71, :174
```

**Die drei vom Auftrag genannten Wächterzeilen stehen wörtlich so da.** *Zusätzlich gefunden:
`ZULETZT_STILLGELEGT` hat **eigene** Wächter in `startEhrlich.test.ts` — eine Datei, die im
Auftragsblatt nicht vorkommt.* **Ergänzung, kein Widerspruch: der Auftrag verlangt „die drei
Wächtertests" und meint die von `STEPS_STILLGELEGT`.** *Beide stehen jetzt in `6-PRUEFUNG.md` und
`7-GRENZEN.md`, damit niemand `ZULETZT_STILLGELEGT` für ungeschützt hält und sie aufräumt.*

**Und `7-GRENZEN.md` sagt es ausdrücklich:**

```text
grep -c '^export function' studioDaten.ts   ->  0
grep -c '^import'          studioDaten.ts   ->  0
```

## W-38-6 · Die Scope-Grenze steht in 2-FUNKTION

*Dort liest sie, wer weiterbaut:* **`fahrschritte.ts` und `GuidedView.tsx` gehören W-34,
`enginePanels.ts` gehört W-37.** *Sie benutzen W-38s Typen. Benutzen ist nicht besitzen.*

## W-38-7 · Der W-40-Befund als offener Anschluss

```text
W-38   ok · prog · warn · open              studioDaten.ts:163, im Code
W-40   confirmed · outdated · blocked       REGISTER.md Stufe 6, KEIN Code
```

**Kein Wort kommt in beiden vor.** *`7-GRENZEN.md` hält die Frage offen, ob W-40 ein zweites
Statussystem einführt, nennt beide Seiten — sie beschreiben Verschiedenes; ein Schritt kann `ok`
sein und sein Ergebnis `outdated` — und **entscheidet nicht**.* **Die Entscheidung gehört zum
Schnitt von W-40.**

## W-38-8 · Sieben Blätter, und die Gegenprobe

```text
Blatt                 W-38      Vorlage   Gleich?
1-ZWECK.md            a0d3a3e7  e921aa08  nein
2-FUNKTION.md         9229ce93  20e1ac73  nein
3-FORMELN.md          f29a0fea  a7d05b09  nein
4-BEDIENUNG.md        f65efbcf  9845bcf1  nein
5-CODE/LIESMICH.md    787553b9  619cf07e  nein
6-PRUEFUNG.md         93842544  719012f0  nein
7-GRENZEN.md          857c178e  a5b225f8  nein

Gegen ALLE 26 Werkzeugordner geprueft: kein W-38-Blatt gleicht einem fremden. 0 Dubletten.
```

**Nebenbefund aus derselben Messung:** *neun Werkzeuge tragen ihre Blätter noch unverändert als
Vorlage —* `W-03 W-06 W-10 W-12 W-14 W-16 W-17 W-18 W-19`. **Alle neun stehen im Register auf
`LEER`; das ist konsistent und kein Mangel.** *Ich nenne sie nur, damit die Zahl „10 gleiche Hashes"
niemanden erschreckt.*

## Der zweite Fehltreffer ist meiner

**Ich wollte belegen, dass die Datei nicht rechnet, und griff zu einem Rechenausdruck-Muster:**

```text
grep -cE '[0-9]+ *[*/+-] *[0-9]+' studioDaten.ts   ->  6
```

**Sechs Treffer, und KEINER ist eine Rechnung:** *vier SVG-Pfade (`M3 21h18M6 21V11l6-4 6 4v10` —
`6-4` sind zwei Koordinaten, kein Minus) und zwei Anzeigetexte (`Rev. 42 · Schritt 2/11`).*

> **Das ist H-9 an mir selbst: ein Muster, das eine Schreibweise voraussetzt, misst die Schreibweise
> und nicht die Sache.** *Der tragende Beleg ist die **0** bei `export function`.* **Ich habe die
> Fehlmessung nicht gelöscht, sondern in `3-FORMELN.md` stehen lassen** — *sie zeigt, wie leicht
> genau dieses Blatt eine Formel erfunden hätte, die es nicht gibt.*

**Und noch eine, beim Messen des Messens:** *zwei meiner Gegenproben schrieben `"$w1-ZWECK.md"`.
Die Shell liest darin die Variable `w1`, nicht `$w` gefolgt von `1`* — **beide Läufe prüften eine
Datei, die es nicht gab, und meldeten fröhlich „keine Dublette".** *Aufgefallen, weil derselbe
Bestand zwei Sätze vorher „1 Dublette" gesagt hatte.* **Zwei Messungen, die sich widersprechen, sind
ein Prüfanlass und keine Auswahl.**

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien geändert** — nur gelesen |
| `docs/STATUS.md` | nur W-38s eigener Zustand |
| Rückweg | reine Neuanlage plus **eine** geänderte Registerzeile; `git revert` genügt |
