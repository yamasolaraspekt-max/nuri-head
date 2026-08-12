# Baubericht W-39 — Studio-Rahmen, abgelesen

```yaml
auftrag: "W-39"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-39-studio-rahmen.md
art: "STUFE 6 · ABLESUNG — der Code existiert, es wird nichts vorgegeben"
in_arbeit_commit: "b4e7243e"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
fassung: "2 — nachgebessert nach dem NACHBESSERN des Evaluators (2ff41efd)"
```

> **Fassung 2. Sieben von acht Kriterien waren erfüllt; rot war W-39-5 — und der Fehler ist meiner,
> und er ist derselbe, den ich im selben Bau schon einmal gemacht und selbst gefangen hatte.**

```text
6-PRUEFUNG  „stilschicht = Farben nur aus Tokens, geteilter Waechter"   -> unvollstaendig
6-PRUEFUNG  Fangprobe „guided-Schalter entfernen -> KEIN TEST"          -> ZURUECKGEZOGEN
7-GRENZEN   K-05 unter „Zusagen ohne Waechter"                          -> ZURUECKGEZOGEN
7-GRENZEN   neue Luecke des Evaluators: das Flag AM AUFRUF              -> aufgenommen
```

**Die Ursache in einem Satz: ich habe eine Testdatei nach ihrer Überschrift eingeordnet, statt sie
zu öffnen.** *Fünf ihrer 58 Tests lesen die Studio-Quelle, vier davon tragen W-39s eigene K-Zusagen
im Namen — `T2/K-01`, `K-03`, `K-04`, `K-05`.* **Bei `imStudio` hatte ich denselben Griff eine
Stunde vorher selbst bemerkt und berichtigt; bei K-05 nicht.**

> **Vier Zahlen des Blattes habe ich beim Nachmessen anders gefunden, und keine davon habe ich
> geglättet.** *Drei sind Mengenfragen, eine war schlicht meine eigene Schlamperei. Alle vier stehen
> unten mit ihrer Auflösung.*

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-39-studio-rahmen/
  1-ZWECK.md  2-FUNKTION.md  3-FORMELN.md  4-BEDIENUNG.md
  5-CODE/LIESMICH.md  6-PRUEFUNG.md  7-GRENZEN.md
REGISTER.md   Zeile 126:  LEER -> BESCHRIEBEN
```

**`HausplanerStudio.tsx` ist gelesen, nicht geändert.** *Keine Datei außerhalb der Werkbank
berührt.*

## W-39-1 · Die additive Bauart

**Zitiert aus `:2-5`, und die Flag-Fundstelle gezeigt:**

```tsx
:140   <div className="hp-experte-buehne"><HausplanerApp imStudio /></div>
```

**Der einzige Eingriff in die bestehende App.** *Sie wird eingebettet, nicht ersetzt; `imStudio`
blendet allein ihre Markenzeile aus, weil der Rahmen schon eine Kopfzeile trägt.* **Wer W-39 als
„neue Oberfläche" beschreibt, verfehlt genau das.**

## W-39-2 · Die drei Modi — Zeilen statt Suchmuster

```text
:131   {modus === 'start'  && <StartView …>}
:132   {modus === 'guided' && <GuidedView …>}
:133   {imExperte && ( … )}          <- :85  const imExperte = modus === 'expert';
```

**Ein Suchmuster auf `'expert'` findet den dritten Zweig nicht** — *das Blatt verbietet es als
Nachweis, und ich habe die Zeilen gezeigt.* **Zusätzlich gemessen, warum die Variable existiert:
`imExperte` hat einen zweiten Nutzer in `:130` (`overflow: imExperte ? 'hidden' : 'auto'`).**

*Die zweite Fehlspur derselben Art ist ebenfalls geprüft:* **`grep -E '^\s+return \('` findet zwei
Treffer — `:89` gehört zu `modeBtn`, das Studio hat genau EINEN `return` in `:97`.**

## W-39-3 · Zustände und Stores — am Code gezählt

```text
React.useState   5    :23 modus · :24 schritt · :25 toast · :26 konfig · :29 fachOffen
React.useRef     1    :60 toastTimer
useHausplanerStore    4 Felder   :30 :31 :55 :56
usePlannerUiStore     1 Feld     :39
```

> **Das Blatt nennt „SECHS eigene Zustände (:23-29) … `+1`". Gemessen sind es FÜNF.** *Das `+1` ist
> `toastTimer`, ein `useRef` in `:60` — außerhalb des genannten Bereichs und kein Zustand: ein Ref
> löst kein Neuzeichnen aus, das ist sein Zweck.* **W-39-3 verlangt „gezählt am Code, nicht
> geschätzt" — also fünf Zustände und ein Ref, getrennt genannt.**

## W-39-4 · `modeBtn` und die Entwurfsentscheidung

**Drei Aufrufe, einer je Modus** (`:110`, `:111`, `:112`). *Nur der Expertenschalter trägt einen
Titel — und das ist der Erklärtext, der früher als Leiste über der Bühne stand.*

**Die Begründung wörtlich aus `:135-139`:** *er „beantwortete eine Frage, die man **genau einmal**
hat".*

**K-05 nachgemessen statt geglaubt:** *alle drei Schalter stehen in einem
`<div className="hp-modusschalter">` (`:109`), und die Kopfzeile hängt nicht vom Modus ab.* **Es
gibt keinen Modus, in dem der Rückweg fehlt.**

## W-39-5 · Acht Wächter, je mit Zusage

| Datei | Tests | trifft W-39 | Zusage (Auszug) |
|---|---|---|---|
| `stilschicht` | 58 | **beides** — 53 geteilt, **5 unmittelbar** | kein Farbwert in einer CSS-Regel · **und `T2/K-01`, `K-03`, `K-04`, `K-05` lesen die Studio-Quelle** |
| `projektKlick` | 15 | unmittelbar | ein Klick, nicht zwei |
| `dialogFokus` | 11 | geteilt | die Falle schlägt an beiden Rändern um |
| `speicherAnzeige` | 10 | unmittelbar | „Gespeichert" steht NIE auf einer Fläche, die nicht speichern kann |
| `fachFlaechen` | 9 | unmittelbar | 19 Flächen — gemessen, nicht abgeschrieben |
| `arbeitszeileSuche` | 7 | unmittelbar | genau EIN Ort öffnet die Palette |
| `fussleistenEhrlich` | 7 | unmittelbar | keine der Fußleisten verspricht noch etwas |
| `breiten` | 5 | geteilt | die Kopfzeile bricht um, statt zu schieben |

**Yamas Maßstab, wörtlich aus `fussleistenEhrlich.test.ts:9` und `:14-15`:**

> *„**Der Maßstab ist derselbe: sagen, was da ist, statt zu versprechen, was kommt.**"*
> *„Die Studio-Navigation **zählt** aus `PROJ` und `FACH`. **Eine gezählte Zahl kann nicht veralten;
> eine abgetippte schon.**"*

> **Der zweite Satz ist die Umkehrung dessen, was mich heute dreimal eingeholt hat** — *A-21-3 wuchs
> von 13 auf 15, A-22-1 fiel von 17 auf 14, A-22-2b brauchte drei Fassungen.* **Der Code macht es
> hier richtig vor.** *Dazu die Einordnung, die dazugehört: die Studio-Navigation ist inzwischen
> ausgebaut (`:122-128`) — der Maßstab gilt weiter, sein damaliger Anwendungsfall nicht mehr.*

## Vier Zahlen, die ich anders gefunden habe

```text
Blatt sagt        gemessen     Aufloesung
13 Importe        14           Zeile 7 ist React und damit extern; die 13 sind die
                               Insel-Module. Zwei Mengen, kein Mangel.
6 Zustaende       5            das +1 ist ein useRef in :60, kein Zustand.
W-34/W-38                      im REGISTER stehen die WERKZEUGE auf BESCHRIEBEN;
BETRIEBSBESTAETIGT             BETRIEBSBESTAETIGT ist der AUFTRAGS-Zustand.
                               Zwei verschiedene Dinge — ich hatte sie verwechselt.
9 ohne Werkzeug   8            13 minus 5 erfasste = 8. Meine eigene Zahl war falsch,
                               die Liste darunter trug bereits acht Zeilen.
```

## Ein Befund, der meine eigene Behauptung widerlegt hat

**Ich hatte in `6-PRUEFUNG.md` geschrieben, das Flag `imStudio` sei durch keinen Test gesichert.**
*Gemessen ist das falsch:*

```text
grep -rl 'imStudio' __tests__/     ->  2 Dateien
  kopfrahmen.test.ts:138   test('K-03 (Bindung): die Marke steht NUR ausserhalb des Studios')
  buehnenBreite.test.ts:76 prueft das Ternaer  height: imStudio ? '100%' : '100vh'
```

**Beide nennen `HausplanerStudio` nicht und gehörten deshalb nicht zu den acht** — *sie sitzen am
**empfangenden** Ende, in `Kopfrahmen.tsx`.* **Und `kopfrahmen.test.ts` ist aus einer durchgekommenen
Mutation entstanden**, wörtlich:

> *„Die Mutation `{!imStudio && (` → `{imStudio && (` kam **durch**. Im Studio gibt es kein Objekt;
> eine Marke ‚Hausplaner · Solar Aspekt' über einer Testfläche wäre genau die Anzeige, die AUF-40
> entfernt hat — nur andersherum."*

> **Die additive Bauart ist damit belegt gesichert, nur von der anderen Seite der Grenze.** *Hätte
> ich nur die acht Dateien betrachtet, die `HausplanerStudio` nennen, wäre die Lücke im Blatt
> geblieben — und sie wäre falsch gewesen.*

**Der Evaluator hat die Naht danach genauer gelegt als ich** (`2ff41efd`): *eine zweite Mutation —
**das Flag am Aufruf in `:140` entfernen** — bleibt **grün**, weil `kopfrahmen.test.ts` den
Kopfrahmen-Quelltext **statisch** misst und damit nur die empfangende Seite.* **Die empfangende
Seite ist bewacht, die sendende nicht.** *Beide meiner Aussagen waren zu grob; die richtige liegt
genau auf der Grenze. Aufgenommen in `7-GRENZEN.md`.*

## W-39-7 · Die Anschlusslücke der Stufe

**Acht der dreizehn Module haben kein eigenes Werkzeug** — *`HausplanerApp`, `FachFlaeche`,
`fachFlaechen`, `speicherAnzeige`, `dialogFokus`, `studioUi`, `uiState`, `hausplanerStore`.*

**Zwei davon stechen hervor, weil sie Regeln tragen und keine Darstellung:** *`speicherAnzeige`
entscheidet, wann „Gespeichert" gesagt werden darf (10 eigene Tests); `fachFlaechen` führt 19
Flächen mit Deckungsprüfung in beide Richtungen.* **Beide sind Ehrlichkeitskonstruktionen wie W-38
und W-34 — die nächsten sinnvollen Ablesungen. Die Entscheidung gehört dem Planner.**

## W-39-8 · Sieben Blätter, Gegenprobe grün

```text
Blatt                W-39      Vorlage   gleich?   Dublette unter 26 Werkzeugen?
1-ZWECK.md           0aaba971  e921aa08  nein      keine
2-FUNKTION.md        cbd7a806  20e1ac73  nein      keine
3-FORMELN.md         767fcf14  a7d05b09  nein      keine
4-BEDIENUNG.md       5eac2303  9845bcf1  nein      keine
5-CODE/LIESMICH.md   0d14a41f  619cf07e  nein      keine
6-PRUEFUNG.md        1f97c716  719012f0  nein      keine
7-GRENZEN.md         d6b160f6  a5b225f8  nein      keine
```

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien geändert** — nur gelesen |
| `docs/STATUS.md` | nur W-39s eigener Zustand |
| Rückweg | reine Neuanlage plus **eine** geänderte Registerzeile; `git revert` genügt |
