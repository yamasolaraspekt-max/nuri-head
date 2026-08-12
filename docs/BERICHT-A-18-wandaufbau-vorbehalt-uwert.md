# Baubericht A-18 — `wandaufbau`: der U-Wert trägt seinen Vorbehalt

```yaml
auftrag: "A-18"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-18-wandaufbau-vorbehalt-uwert.md
basis_sha: ea9522bc
gebaut_auf: ee2dad24
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

**Ein Pflichtfeld, eine Konstante, eine Testzusage.** *Keine Rechenänderung, kein Glaser, kein
Brückenbau — und zwei Zahlen des Blattes, die beim Nachmessen etwas anderes bedeuteten als sie
sagten.*

---

## A-18-1 · `vorbehalt` ist PFLICHT — die Gegenprobe bricht

`UErgebnis` trägt `vorbehalt: string` ohne `?`. Der Beweis, dass es nicht weglassbar ist, ist kein
Blick auf den Code, sondern der Abbruch:

```text
# Feld aus der Rueckgabe entfernt, Typpruefung gefahren:
npm run tsc:hausplaner
  wandaufbau.ts(87,3): error TS2741: Property 'vorbehalt' is missing in type
  '{ gesamtdicke: number; rBa…'
  RUECKGABEWERT: 2

# Feld zurueckgebaut:
  RUECKGABEWERT: 0     error-Zeilen: 0
```

*Ein optionales Feld wäre die stille Ersetzung gewesen — es ließe genau den Fall zu, den A-18
verhindern soll.*

## A-18-2 · Benannte Konstante — und eine Zahl, die der Evaluator entscheiden soll

`UWERT_VORBEHALT` steht in `wandaufbau.ts:57-60`, nach dem Muster `N003_VORBEHALT`
(`sparrenBerechnung.ts:100` — **nachgemessen, die Zeilenangabe des Blattes stimmt**).

**Das Kriterium sagt „die Konstante kommt genau einmal vor". Gemessen kommt der Wortlaut ZWEIMAL
vor, und ich melde beide Stellen statt die passende auszuwählen:**

```text
resources/planner/hausplaner/geometry/wandaufbau.ts:58        die Konstante selbst
resources/planner/hausplaner/__tests__/wandaufbau.test.ts:58  ausgeschrieben in der Zusage
```

*Warum die zweite Stelle absichtlich dort steht:* **ein Test, der nur `r.vorbehalt === UWERT_VORBEHALT`
prüft, prüft gar nichts** — er vergleicht zwei Verweise auf dieselbe Zeichenkette und bleibt grün,
wenn jemand den Wortlaut umformuliert. Der ausgeschriebene Vergleich ist der einzige Wächter im
Repositorium gegen eine stille Umformulierung. **Will der Evaluator die Zahl wörtlich, streiche ich
die Zeile — dann fällt der Schutz weg. Das ist seine Entscheidung, nicht meine.**

## A-18-3 · Zeichengenau — maschinell verglichen, nicht mit dem Auge

```text
Blatt   258 Zeichen   (Abschnitt 3, Zeilen 91-93, Zeilenumbruch = ein Leerzeichen)
Code    258 Zeichen
ZEICHENGENAU GLEICH: JA
```

*Beide Seiten wurden aus der jeweiligen Datei gelesen, keine Seite abgetippt. Der Wortlaut behält
die ASCII-Schreibungen des Blattes* (`geprueft`, `dafuer`, `Diffusionswiderstaende`) *und den
Halbgeviertstrich. **Ich habe ihn NICHT still auf Umlaute umgestellt**, obwohl das Hausmuster
`N003_VORBEHALT` welche trägt (`prüffähige`) — „zeichengenau" schlägt Hausstil, und die Abweichung
gehört gemeldet statt behoben.*

> **Der erste Anlauf des Vergleichs war falsch, und der Fehler lag bei mir:** *mein Auslese-Skript
> trennte die Anweisung an `;` — der Wortlaut enthält aber selbst eines („gepruef**t;** dafuer").
> Es meldete **88 statt 258 Zeichen** und „NEIN". **Ein Werkzeug, das an den eigenen Daten
> zerbricht, meldet einen Codefehler, wo keiner ist.** Behoben (`;` nur mit Zeilenende dahinter),
> und der Vorfall steht hier, weil er beim Prüfen genauso passieren kann.*

## A-18-4 · Keine Rechenänderung

```text
git diff --numstat -- resources/
   24  0   geometry/wandaufbau.ts
   16  1   __tests__/wandaufbau.test.ts
```

**Die eine gelöschte Zeile, ausgeschrieben — sie ist keine Rechenzeile und keine Zusage:**

```diff
-import { berechneUWert, UEBERGANG } from '../geometry/wandaufbau';
+import { berechneUWert, UEBERGANG, UWERT_VORBEHALT } from '../geometry/wandaufbau';
```

> *Meine erste Prüfung meldete „1 Treffer in geänderten Rechenzeilen" — **die Trefferzeile war diese
> Import-Zeile, weil `UEBERGANG` darin vorkommt.** Ohne Nachlesen hätte ich eine Rechenänderung
> gemeldet, die es nicht gibt (H-6).* **Will der Evaluator 0 gelöschte Zeilen wörtlich, wird daraus
> eine zweite Import-Zeile — dann steht ein doppelter Import in der Datei. Auch das seine
> Entscheidung.**

`UEBERGANG` und `ZIEL_U` sind **zeichengleich**, über acht Zeilen inhaltlich verglichen (nicht nach
Zeilennummer — die hatte meine eigene Einfügung verschoben):

```text
UEBERGANG  aussenwand 0.13/0.04 · innenwand 0.13/0.13 · dach 0.10/0.04 · boden 0.17/0.00
ZIEL_U     aussenwand 0.24 · innenwand 0.75 · dach 0.20 · boden 0.30
```

## A-18-5 · Kein Glaser, kein μ, kein s_d, kein Klima

**Die zehn Begriffe, jeder einzeln gezählt UND jede Trefferzeile gelesen:**

| Begriff | Treffer | wo |
|---|---|---|
| `kondensat` · `sd-wert` · `mu-wert` | **0** | — |
| `taupunkt` | 1 | Zeile 40, Doku-Kommentar |
| `glaser` | 1 | Zeile 41, Doku-Kommentar |
| `diffusion` | 2 | Zeile 42 (Kommentar), 59 (Vorbehaltstext) |
| `4108` | 2 | Zeile 41 (Kommentar), 59 (Vorbehaltstext) |
| `tauwasser` · `schimmel` | je 1 | Zeile 58, Vorbehaltstext |
| `feuchte` | 2 | Zeile 58, 60, Vorbehaltstext |

**Alle Treffer liegen in Kommentar (38-48, 52-56) oder in der Zeichenkette (58-60). Kein einziger in
einem Rechenausdruck.** *Genau die Unterscheidung, die das Blatt verlangt: **die Engine SPRICHT über
die Größen, sie RECHNET sie nicht.***

## A-18-6 · Die Zusagen — und was „zehn" wirklich meinte

**Das Blatt sagt „die ZEHN vorhandenen Testzusagen". Ich zähle sechs. Beide Zahlen stimmen, weil sie
verschiedene Mengen zählen:**

```text
grep -cE '^test\('   wandaufbau.test.ts  ->   6   test()-Bloecke
grep -cE 'assert\.'  wandaufbau.test.ts  ->  10   assert-Aufrufe
```

*Die zehn Zusagen sind die **Assertions**, nicht die Testblöcke. **Das ist B6 in freier Wildbahn:
eine Summe, deren Menge nie benannt wurde** — hier ohne Schaden, weil beide Lesarten dieselbe Datei
meinen, aber es hat mich eine Messung gekostet.*

Alle zehn bleiben unverändert; hinzu kommt **ein** Block mit drei Assertions (Konstante,
ausgeschriebener Wortlaut, zweite Bauteilart):

```text
npm run test:hausplaner   ->   tests 1694   pass 1694   fail 0
                               (vorher 1693 — genau ein Block mehr)
```

## A-18-7 · Die Fähigkeitsliste — geprüft, nicht angenommen

```text
resources/planner/hausplaner/app/tools/faehigkeiten.ts:81
  { id: 'engine-uwert', …, eingang: 'Schicht[]', ausgang: 'UErgebnis',
    engineModul: 'geometry/wandaufbau', engineExport: 'berechneUWert' }
```

**Der Eintrag bleibt ohne Änderung richtig.** *`ausgang` nennt den **Typnamen**, nicht seine Felder —
ein Pflichtfeld mehr in `UErgebnis` ändert daran nichts.* **Nichts angefasst.** *Zur Pfadangabe: das
Blatt schreibt `faehigkeiten.ts:81`, die Datei liegt unter `app/tools/`; die Zeilennummer stimmt.*

## A-18-8 · Der zweite Befund — benannt, nicht behandelt

```text
wandaufbau.ts:2   „Konfigurator Wandaufbau §11, autark; SPEIST HEIZLAST & DACH"
gemessen          Aufrufe aus app/:  0 Dateien   (grep -rl 'wandaufbau|berechneUWert' app/)
                  einziger Aufrufer: faehigkeiten.ts:81, zustand 'in_entwicklung'
```

**Eine Zusage im Dateikopf, die kein Aufruf einlöst — kein Brückenbau in diesem Auftrag.** *Als
eigener Posten geführt, damit er nicht als erledigt gilt.*

---

## `must_preserve`, in drei Richtungen einzeln

| Richtung | Ergebnis |
|---|---|
| **geändert** | alle `resources/**` außer den zwei genannten Dateien byte-identisch (HEAD-Blob gegen `hash-object`) |
| **hinzugefügt** | **0** unter `resources/` und `app/` |
| **entfernt** | **0** unter `resources/` und `app/` |
| **`app/**`** | `git diff --name-only HEAD -- app` → **0 Dateien** |

## Rückweg und Rückfallpunkt — am Bautag gemessen

```text
git apply --check -R <bau-patch>   ->  Exit 0, Arbeitsbaum unangetastet

fork/auto/hausplaner-integration            5579a6c0
backup-private/auto/hausplaner-integration  5579a6c0
origin/auto/hausplaner-integration          aa6584ae
lokal HEAD                                  ee2dad24
```

**Die Kopie außerhalb der Maschine ist aktuell und enthält meine Commits** — `5579a6c0` ist ein
`Merge commit 'ee2dad24'`, und `157576c2` (B5) liegt ebenfalls darin (`merge-base --is-ancestor`
beide Male JA). *Das Blatt vermutete „15 Commits hinter fork"; heute sind es **28**, alle in der
Gegenrichtung — lokal hat **0**, was fork nicht hat.*

> **Die ehrliche Grenze:** *der hier berichtete Bau ist zum Zeitpunkt dieser Messung **noch nicht**
> außerhalb der Maschine — er ist erst committet. Gesichert ist der Stand **bis** `ee2dad24`.*

## Was NICHT gebaut wurde

**Kein Glaser-Rechenweg, kein μ/s_d im Datentyp, keine Klimafelder, keine Brücke zur Heizlast, keine
Plakette gestrichen** *(es gibt keine — diese Engine hat kein Panel, anders als bei A-14 und A-17).*
Die Achse-2-Zeile aus A-15 löst sich damit auf: *was die Engine wirklich behauptet, wirkt auf die
Heizlast → **Fehlauslegung**, der Bauschaden-Weg ist ausgeschlossen, sobald sie ihre Grenze selbst
sagt.* **A-15 selbst habe ich nicht angefasst** — es steht auf ABGENOMMEN beim Release-Prüfer.

## Berührte Dateien

```text
resources/planner/hausplaner/geometry/wandaufbau.ts        +24 / -0
resources/planner/hausplaner/__tests__/wandaufbau.test.ts  +16 / -1  (die -1 ist der Import)
docs/BERICHT-A-18-wandaufbau-vorbehalt-uwert.md            dieser Bericht
docs/STATUS.md                                             Zustand an beiden Orten
```
