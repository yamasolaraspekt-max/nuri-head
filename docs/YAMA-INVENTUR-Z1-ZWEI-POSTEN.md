# Inventur Z1 — die zwei Yama-Posten entschieden: P-1 ablehnen, R-2 stilllegen statt löschen

> **Release-Prüfer in Yamas Namen, 21.08. ~00:1x.** Auf `af60108f`. Übernommen auf seine Bitte, mit
> dem Auftrag, **den Planner zu benachrichtigen**, wenn es erledigt ist.
>
> **Lesart offengelegt:** Yamas Bitte nannte „diese Aufgabe" ohne Kennung. Ich habe alle Yama-Posten
> der neu eingesammelten Commits gemessen; **die Inventur Z1 ist der einzige Posten, der
> ausdrücklich auf seine Entscheidung wartet und zugleich einen Planner-Bezug hat** (der Planner hat
> die Inventur-Maschinerie gebaut, `ee19f314`, und der Fahrplan liegt als ENTWURF). Falls er einen
> anderen meinte, ist diese Entscheidung trotzdem gültig — sie steht für sich.
>
> **Beide Befunde am Code nachgeprüft, nicht geglaubt.**

---

## P-1 · Walmdach-Fläche bis +75 % zu groß — **ablehnen, nicht automatisch drehen**

### Nachgemessen

```
dachGeometrie.ts:136   const firstLenM = Math.max(0, laengeM - spannM);      ← klemmt still auf 0
             :138      const walm = (spannM * spannM) / (4 * cos);           ← bleibt VOLL
```

**Bei `spannM > laengeM` wird die Firstlänge auf 0 gezogen, die Walm-Dreiecke behalten aber die
volle Giebelbreite.** Die Fläche wächst dadurch unbegrenzt — gerechnet 4×10 m: 80,83 statt
46,19 m², **+75 %**.

### Die Frage war „ablehnen oder automatisch drehen" — der Bestand hat sie zweimal beantwortet

```
1  dachGeometrie.ts:88-93, fuenfzig Zeilen HOEHER in derselben Datei:
     throw new DachGeometrieUngueltig('… V1 unterstuetzt nur rechteckige Grundrisse
                                       (KEIN STILLES FALSCHDACH).')

2  dachformVorlagen.ts:336-341, das Schwestermodul, woertlich:
     "Rueckgabe DARF <0 sein (Signal fuer Inkonsistenz), wird NICHT still auf 0 geklemmt."
```

> ***Zweimal dieselbe Haltung, einmal als Wurf und einmal als ausdrückliche Zusage: melden statt
> still korrigieren.*** **Und `dachGeometrie.ts:136` tut genau das, was das Schwestermodul
> ausschließt** — im selben Haus, für dieselbe Größe.

**Automatisches Drehen wäre stilles Korrigieren einer Nutzerangabe** — und keiner beliebigen: die
Firstrichtung bestimmt Kehlen- und Gratlage, Sparrenrichtung und Entwässerungsrichtung. **Sie ist
genau die Größe, deren Zuordnung ich am 19.08. als Fachfrage an Yama zurückgegeben habe (Punkt 8).**
Ein Programm, das sie selbsttätig dreht, trifft diese Fachentscheidung stillschweigend bei jedem
Aufruf.

```
ENTSCHIEDEN   ablehnen. Bei spannM > laengeM wirft dachFlaechen() DachGeometrieUngueltig,
              nach dem Muster :88-93, mit eigener Ursachen-Kennung.
NICHT         automatisch drehen — das waere stille Automatisierung einer Fachgroesse.
```

**Das ist keine neue Festlegung, sondern die Anwendung einer zweifach dokumentierten Hauslinie.**
Deshalb konnte ich sie treffen: **die Produktentscheidung ist getroffen, sie war nur an dieser
einen Stelle nicht umgesetzt.**

**Ein Zusatz, den die Inventur schon nennt und der nicht übersehen werden darf:** die passende
Prüfung **existiert bereits** — `walmIstKonsistent` (`dachformVorlagen.ts:414-416`, getestet) —
**läuft aber nur im Vorlagen-Preview, nie im Live-Pfad.** Der Bau ist damit kein Neubau, sondern
ein Anschluss.

---

## R-2 · `dachWerte.ts` doppelt — **stilllegen, nicht löschen**

### Nachgemessen

```
cmp resources/planner/utils/dachWerte.ts
    resources/planner/hausplaner/geometry/dachWerte.ts     -> IDENTISCH
    je 103 Zeilen / 4188 Bytes
resources/planner/utils/  enthaelt sonst NICHTS  (nur diese eine Datei)

Verbraucher, einzeln geoeffnet:
  dachGeometrie.ts:13      import { sichererCos } from '../../utils/dachWerte';
  dachformVorlagen.ts:34   import { sichererCos, cmZuMFloor, DACH_FLOOR_CM } from './dachWerte';
```

**Zwei Live-Verbraucher, zwei verschiedene Kopien derselben fachlichen Größe.** Heute identisch —
eine Änderung an der `sichererCos`-Schwelle träfe morgen nur einen von beiden.

### Warum ich hier nicht die Löschung entscheide, die Sache aber trotzdem

**Die Löschung einer Datei bleibt bei Yama** — das ist seine Rückfall-Regel und steht zusätzlich auf
meiner eigenen Ausnahmeliste. **Ich brauche sie aber nicht:** das Erledigt-Kriterium der Inventur
lässt beide Wege ausdrücklich zu — *„`utils/`-Datei entfernt **oder dokumentiert stillgelegt**"*.

```
ENTSCHIEDEN   dachGeometrie.ts:13 importiert kuenftig './dachWerte' (die geometry-Fassung);
              die utils-Kopie bleibt liegen und bekommt einen Kopf nach dem vorhandenen
              Muster toolCatalogStillgelegt.ts — Grund, Datum, Nachfolger, kein Verbraucher.
NICHT         geloescht. Bleibt Yamas Freigabe, und sie wird dafuer nicht gebraucht.
```

> **Damit ist die zweite Wahrheit beseitigt, ohne dass etwas verschwindet** — genau die Form, die
> die Rückfall-Regel vorsieht: *Original erhalten, Weg dokumentiert.* **Die Löschung kann später
> jederzeit folgen; die Doppel-Wahrheit wartet nicht darauf.**

**Belegt ist auch der Zielort:** `588283df` (23.07., W-1 „reine Reuse") nennt `geometry/`
ausdrücklich als den einen Zielort. **Die utils-Kopie ist der Rest einer Komplettmigration vom
18.07. (`00bfed2b`), nicht eine bewusste zweite Ablage.**

---

## Was ich zu den übrigen fünf Befunden NICHT tue

**K-1 bis K-4, P-1s Nachbarn und R-1 tragen keinen Yama-Posten** — sie sind Backlog und gehören dem
Planner. **Ich habe sie gelesen, nicht entschieden**, und fasse sie hier nicht an. Nur eine
Beobachtung, die zu meiner Rolle gehört und keine Entscheidung ist:

**R-1 (`polygonM2` privat kopiert) und R-2 sind dieselbe Klasse** — eine Größe an zwei Orten. Bei
R-1 ist die Drift **schon messbar** (die private Kopie hat keinen `Number.isFinite`-Schutz, das
Schwestermodul sagt „niemals NaN" zu), bei R-2 noch nicht. **Wer beide in einem Zug zuschneidet,
löst dieselbe Sache zweimal mit demselben Handgriff.** Das ist ein Hinweis an den Planner, kein
Auftrag.

---

## Ball

**Beim Planner** — beide Zuschnitte, jeweils Aufwand S nach eigener Einschätzung der Inventur:

```
P-1  Konsistenzpruefung in dachFlaechen() einziehen (Muster :88-93). Der Anschluss von
     walmIstKonsistent ist der kuerzere Weg als eine neue Pruefung.
     erledigt_wenn (aus der Inventur, unveraendert): fuer spannM > laengeM Ausnahme
     DachGeometrieUngueltig ODER Flaeche erfuellt Kontrollrechnung +-1 % an beiden Faellen.
R-2  Import in dachGeometrie.ts:13 umhaengen, utils-Kopie mit Stilllegungskopf versehen.
     NICHT loeschen.
```

**Bei Yama** — nur noch die Löschung der stillgelegten Datei, und die eilt nicht.

**Benachrichtigung des Planners:** auf dem belegten Weg — dieser Commit und der Rückweg in
`ticket-rolle-planner`. **Von den sieben laufenden Sitzungen trägt keine einen Rollennamen**, eine
gezielte Direktnachricht wäre geraten statt adressiert.
