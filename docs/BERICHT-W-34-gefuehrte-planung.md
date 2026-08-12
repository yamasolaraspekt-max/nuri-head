# Baubericht W-34 — Geführte Planung, abgelesen

```yaml
auftrag: "W-34"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-34-gefuehrte-planung.md
art: "STUFE 6 · ABLESUNG — der Code existiert, es wird nichts vorgegeben"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
fassung: "2 — nachgebessert nach dem NACHBESSERN des Evaluators (e5716bc0)"
```

> **Fassung 2. Sieben von acht Kriterien waren erfüllt; rot war ausgerechnet W-34-1, das sich
> selbst als P1 TRAGEND markiert — und der Fehler war meiner.** *Berichtigt an drei Stellen, das
> Falsche jeweils **zurückgezogen und nicht gelöscht**:*

```text
2-FUNKTION.md   die widerlegte Kausalaussage     -> zurueckgezogen, 85-Kombinationen-Messung
6-PRUEFUNG.md   die WIRKUNGSLOSE Fangprobe       -> ersetzt durch eine GEFAHRENE (5 fail)
6-PRUEFUNG.md   K-1 der Kriterientabelle          -> trug dieselbe Aussage ein zweites Mal
```

> **Drei Zählfehler stecken in diesem Bau, alle drei von meiner eigenen Gegenprobe gefangen, und
> alle drei stehen in den Blättern statt gelöscht zu sein.** *Sie sind dieselbe Sorte: ein Muster,
> das die Schreibweise misst und nicht die Sache.*

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-34-gefuehrte-planung/
  1-ZWECK.md  2-FUNKTION.md  3-FORMELN.md  4-BEDIENUNG.md
  5-CODE/LIESMICH.md  6-PRUEFUNG.md  7-GRENZEN.md
REGISTER.md   Zeile 121:  LEER -> BESCHRIEBEN
```

**Keine Datei außerhalb der Werkbank berührt.** *`fahrschritte.ts` und `GuidedView.tsx` sind
gelesen, nicht geändert; `studioDaten.ts` blieb unberührt — es ist gerade abgenommen.*

## W-34-1 · `statusAus`, fünf Zweige UND die Reihenfolge

> **NACHGEBESSERT, Runde 2 — hier stand der einzige rote Punkt der Abnahme, und er ist meiner.**
> *Ich hatte behauptet: „Zweig 2 (`warn`) steht VOR den `every`-Prüfungen, **deshalb** schlägt
> `warn` neunmal `ok`."* **Diese Ursache gibt es nicht** — *ein `warn` bricht beide
> `every`-Bedingungen ohnehin, die Mengen sind disjunkt.*

**Die Zeilen gezeigt, nicht beschrieben** (`fahrschritte.ts:43-49`). **Selbst nachgemessen über alle
85 Kombinationen aus vier Statuswerten bei Länge 0 bis 3:**

```text
M1  warn-Zweig HINTER die every-Pruefungen   0 Abweichungen von 85   -> WIRKUNGSLOS
M2  LEER-Zweig HINTER die every-Pruefungen   1 Abweichung  von 85   -> TRAGEND
      []   original 'open'   ->   mutiert 'ok'
```

**Die tragende Reihenfolge ist `checks.length === 0` VOR den `every`-Prüfungen, weil
`[].every(...)` WAHR ist.** *Fachlich ist das genau die Lüge, die dieses Werkzeug abschafft: ein
Schritt ohne Prüfpunkt meldete „Vollständig" — und alle sechs Schritte ohne Modellgrundlage haben
`checks: []`.*

```text
Zweig 5 (prog) hat KEINE Bedingung   ->  prog wird nie geprueft, sondern uebrig gelassen
```

> **Was ich daraus mitnehme:** *ich hatte eine Reihenfolge gesehen und ihr eine Wirkung
> **zugeschrieben**, statt sie zu messen.* **Die Aussage klang tragend und war es nicht — die
> tragende Stelle stand direkt daneben, und der Dateikopf `:40-41` hatte sie sogar benannt.**

## W-34-2 · Elf Schritte, am Code gezählt — und zwei Fehlzählungen unterwegs

```text
Eintraege des return-Arrays in ableitenSchritte (:124-196), Kommentarzeilen ausgenommen:  11
```

> **Mein erstes Muster zählte 16** — *es traf `schritt(` und `ohneGrundlage(` auch **innerhalb** der
> Ternärausdrücke.* **Mein zweites zählte 13** — *es ging über die Einrückung, und zwei
> Kommentarzeilen (`:127`, `:161`) stehen auf derselben Ebene wie die Einträge.* **Erst „genau vier
> Leerzeichen UND keine Kommentarzeile" trifft die Sache.**

**Die Zahl kommt NICHT aus `fahrschritte.test.ts`,** *und die beiden dortigen Zusagen habe ich
trotzdem geöffnet:* `:37` (`assert.equal(schritte.length, 11)`) *und* `:71`
(`assert.equal(STEPS_STILLGELEGT.length, 11)`). **Ein dritter Treffer auf „11" war die UUID in
`:20`** — *H-6, geöffnet und verworfen.*

## W-34-3 · Die sechs Lücken, vollständig — und ein Kommentar, der nicht trägt

**Alle sechs stehen in `7-GRENZEN.md` mit dem Satz aus dem Code**, *und die Verhältniszahl steht
daneben:* `Object.keys(SCHRITTE_OHNE_GRUNDLAGE).length = 6` **von 11.**

**Der geforderte wörtliche Kommentar ist zitiert** (`:52-54`) — *und weil er wörtlich zitiert werden
muss, muss auch gesagt werden, dass ein Satz darin nicht trägt:*

```text
Der Kommentar sagt:  "SCHRITTE_OHNE_GRUNDLAGE.length ist die Laenge der Rueckgabe-Liste
                      an den Planner"
Gemessen:  :56  export const SCHRITTE_OHNE_GRUNDLAGE: Readonly<Record<string, string>>
           -> ein Record, kein Array. .length darauf ist undefined.
           -> und die Rueckgabe-Liste hat 11 Eintraege, nicht 6.
```

> **Zwei Fehler in einem Satz, Wirkung null:** *der Ausdruck steht nur im Kommentar. Außerhalb der
> Datei wird `SCHRITTE_OHNE_GRUNDLAGE` allein im Test benutzt, und dort korrekt mit
> `Object.keys(...)` (`:91`, `:98`).* **Der Gedanke stimmt — „die Lücke ist zählbar" — nur sein
> Beleg nicht.** *Ein Befund am Kommentar, kein Befund am Verhalten. **Die Berichtigung gehört
> nicht zu einer Ablesung** und ist nicht gemacht.*

## W-34-4 · `bebauteGeschosse`, mit der Begründung aus dem Code

**Gezählt wird, was das Geschoss TRÄGT** — `nodes`, `roofs` oder `ceilings` (`:84-88`). *Die
Begründung ist wörtlich zitiert (`:77-83`): ein frisch angelegtes Projekt **hat** bereits ein
Geschoss, weil die Anwendung es anlegt — „1 Geschoss angelegt ✓" wäre grün, ohne dass jemand etwas
getan hat.*

**Beide Zahlen erscheinen nebeneinander** (`:136`, `:140`): *„3 von 4 Geschossen bebaut".*

## W-34-5 · Der Anschluss an W-38, mit Fundstelle

```text
GuidedView.tsx:4    import { T, STATUS_LABEL, type SchrittStatus, type Fahrschritt }
GuidedView.tsx:15   schritte: readonly Fahrschritt[];
GuidedView.tsx:18   badgeFarbe: Record<SchrittStatus, { bg; fg }>
GuidedView.tsx:22   checkFarbe: Record<SchrittStatus, { bg; fg; sym }>
GuidedView.tsx:71   …>{STATUS_LABEL[s.status]}</span>
fahrschritte.ts:27  import type { Fahrschritt, Pruefpunkt, SchrittStatus }
```

**Vier Namen benutzt, keiner neu definiert.** *`Record<SchrittStatus, …>` erzwingt, dass alle vier
Stufen aus W-38 eine Darstellung haben.* **Richtung einseitig und geprüft: W-38 importiert nichts,
kein Kreis möglich.**

## W-34-6 · Die fünf Wächter, je mit ihrer Zusage

| Datei | Tests | Was sie hält |
|---|---|---|
| `fahrschritte.test.ts` | 12 | Leerdokument liefert nichts Grünes · Reinheit · elf Schritte · die sechs Lücken |
| `gefuehrteEhrlich.test.ts` | 8 | kein Statuswort behauptet eine Freigabe · leere Aufgabenkarte |
| `breiten.test.ts` | 5 | keine feste zweite Spalte — *das war die tote Fläche bei 375 px* |
| `dialogFokus.test.ts` | 11 | Fokusfalle an beiden Rändern · Enter UND Leertaste (WCAG 2.1.1) |
| `stilschicht.test.ts` | 58 | jede Variable aus `studioDaten.ts`, kein Farbwert in der CSS-Quelle |

> **Der schärfste Test des Werkzeugs ist der zweite K5 in `fahrschritte.test.ts`:** *er prüft nicht
> den neuen Code, sondern hält die **alte Lüge** als Vergleichsgrundlage fest — die stillgelegten
> Demo-Daten behaupteten grün.* **Ein Test, den es ohne W-38s stillgelegte Konstante nicht gäbe.**

**`stilschicht` und `dialogFokus` sind GETEILTE Wächter**, *sie decken die ganze Insel ab.* **Das
steht so in `6-PRUEFUNG.md`, weil „was bewacht welcher" eine ehrliche Antwort verlangt.**

## W-34-7 · Die Scope-Grenze steht in 2-FUNKTION

*`studioDaten.ts` gehört W-38 · `EngineFlaeche.tsx` und `enginePanels.ts` gehören W-37.* **Benutzen
ist nicht besitzen.**

## W-34-8 · Sieben Blätter, Gegenprobe grün

```text
Blatt                W-34      Vorlage   gleich?   Dublette unter 25 Werkzeugen?
1-ZWECK.md           229e6b9c  e921aa08  nein      keine
2-FUNKTION.md        93359bf5  20e1ac73  nein      keine
3-FORMELN.md         a971bdf2  a7d05b09  nein      keine
4-BEDIENUNG.md       c75d59e1  9845bcf1  nein      keine
5-CODE/LIESMICH.md   a5808e59  619cf07e  nein      keine
6-PRUEFUNG.md        b4c60d15  719012f0  nein      keine
7-GRENZEN.md         afd6cd40  a5b225f8  nein      keine
```

## Die Fangprobe, die nichts fing — und ihr Ersatz, gefahren

**Meine erste Mutationsprobe war „den `warn`-Zweig hinter die `every`-Prüfungen schieben".** *Der
Evaluator hat sie gefahren: **1698 pass, 0 fail**.* **Von fünf Fangproben war ausgerechnet die
wirkungslos, die zum tragenden Kriterium gehörte.**

**Ersetzt und diesmal selbst gefahren, mit Anker und Rücksetzung:**

```text
Anker md5 vor der Probe   ace90cf96cb559da4849d4cf458cac44
Grundlinie                1698 Tests · 1698 pass · 0 fail
Mutation: checks.length === 0 hinter die every-Pruefungen
                          1698 Tests · 1693 pass · 5 fail

  ✖ K5  ein LEERES Dokument liefert keinen gruenen Schritt und keinen gruenen Pruefpunkt
  ✖ K6  ein Schritt ohne Modellgrundlage sagt WAS fehlt und traegt keine erfundenen Pruefpunkte
  ✖     statusAus: alle erfuellt ⇒ ok · alle offen ⇒ open · gemischt ⇒ prog · ein warn ⇒ warn
  ✖ K4  die SCHLUESSEL sind unveraendert
  ✖ K4  kein Schritt hat seinen Status gewechselt

md5 nach dem Ruecksetzen  ace90cf96cb559da4849d4cf458cac44   IDENTISCH
git status resources/     0
```

**Fünf Wächter über zwei Testdateien.** *Die vier übrigen Fangproben bleiben **abgelesen** und sind
in `6-PRUEFUNG.md` je Zeile als solche gekennzeichnet — die Spalte „Gefahren?" macht den Unterschied
sichtbar, statt ihn in einen Satz darunter zu schieben.*

## Der dritte Zählfehler — in `3-FORMELN`

**Ich schrieb „sechzehn Zählungen" und maß dann nach:**

```text
grep -c 'zaehle(nodes'    14   <- enthaelt die DEFINITIONSZEILE function zaehle(nodes: …)
grep -c '= zaehle(nodes'  13   <- die echten Aufrufe
direkte .length            4   <- geschosse :76 · daecher :93 · decken :94 · bebauteGeschosse :84
                          ---
                          17
```

> **Eine Funktion zählt sich sonst selbst mit.** *Dieselbe Klasse wie die 16 und die 13 oben, und
> derselbe Grund, warum die Gegenprobe kein Zierrat ist.*

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien geändert** — nur gelesen |
| `docs/STATUS.md` | nur W-34s eigener Zustand |
| Rückweg | reine Neuanlage plus **eine** geänderte Registerzeile; `git revert` genügt |
