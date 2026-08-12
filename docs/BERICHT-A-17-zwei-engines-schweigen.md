# Baubericht A-17 — zwei Engines schweigen, und der Vorbehalt tritt an die Stelle der Plakette

```yaml
auftrag: "A-17"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-17-zwei-engines-schweigen.md
basis_sha: 3678d1de
gebaut_auf: 05c6536b
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

**Dreimal „ein Flag setzen und einen Satz schreiben", wie das Blatt es angekündigt hat — das Bauteil
kommt vollständig aus A-14.** *Neu ist nichts erfunden worden. Die Arbeit steckte in der Abnahme:
die Browsermessung hat mich vier Anläufe gekostet, und **drei davon sind an meinem eigenen Werkzeug
gescheitert, nicht am Code**.*

---

## A-17-1 / A-17-2 · Beide Engines tragen `keinGesamturteil` — und nur sie

```text
enginePanels.ts:176   engine-sparren    (A-14, unveraendert)
enginePanels.ts:241   engine-fbh        NEU
enginePanels.ts:340   engine-abwasser   NEU
```

**Gegenprobe im Test, erhoben statt behauptet:**

```text
stumm            : engine-sparren, engine-fbh, engine-abwasser        (3)
mit Gesamturteil : engine-treppe, engine-heizkoerper,
                   engine-fensterprodukt, engine-kueche, engine-pv    (5)
```

**Die schärfere Gegenprobe — die, an der auch A-14 geprüft wurde:** *ein Panel mit
`bestanden=false`, das seine Plakette **behält**.* Im Test gemessen: **`engine-treppe`** liefert
`bestanden=false` und behält sie. **Das Flag schaltet zwei Engines stumm, nicht negative Urteile
allgemein.**

## A-17-3 · Der Vorbehalt trägt, statt zu fehlen

Beide Engines bekommen ein **Pflichtfeld** `vorbehalt` (kein `?`), gespeist aus einer benannten
Konstante nach dem Muster `N003_VORBEHALT`:

```text
abwassergefaelle.ts:51  ABWASSER_VORBEHALT      :71  vorbehalt: ABWASSER_VORBEHALT
fbhAuslegung.ts:56      FBH_VORBEHALT           :96  vorbehalt: FBH_VORBEHALT
enginePanels.ts:264     { schluessel: 'vorbehalt', label: 'Vorbehalt' }   (fbh)
enginePanels.ts:354     { schluessel: 'vorbehalt', label: 'Vorbehalt' }   (abwasser)
```

**Der Wortlaut kommt aus dem Dateikopf der jeweiligen Engine, nicht aus meiner Feder** — genau wie
das Blatt es verlangt, und im Test **zeichengenau** gegen den ausgeschriebenen Satz geprüft.

*Die `grundlage`-Zeile trägt die Reichweitengrenze jetzt sichtbar; sie ist auf dem Bildschirmfoto
unter dem Titel zu lesen.*

## A-17-4 · Keine Rechenänderung

```text
git diff --numstat -- resources/
   23  0   geometry/fbhAuslegung.ts
   23  1   geometry/abwassergefaelle.ts
   17  2   app/dashboard/enginePanels.ts
```

**Alle drei gelöschten Zeilen ausgeschrieben, damit niemand raten muss:**

```diff
-    grundlage: 'Auslegung nach Verlegeabstand und maximaler Heizkreislaenge; Pruefpunkte …'
-    grundlage: 'Mindestgefaelle je Nennweite; Hoehenverlust = Gefaelle x Laenge',
-    bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden) };
```

*Die zwei `grundlage`-Zeilen sind **von A-17-3 gefordert** (die Reichweitengrenze musste hinein).
Die dritte ist keine Rechenänderung: der Ausdruck ist zeichengleich, nur das abschließende `};`
wurde zu `,` plus der neuen `vorbehalt`-Zeile.* Gegengemessen:

```text
Vergleichsoperatoren + Grenzwerte je Datei   abwassergefaelle  vorher 9  nachher 9
                                             fbhAuslegung      vorher 5  nachher 5
```

## A-17-5 · Die einzelnen Prüfzeilen bleiben sichtbar

**Am Bildschirm gemessen, nicht abgeleitet:** die Abwasser-Fläche zeigt nach dem Rechnen weiterhin
`PRÜFUNGEN (2)` mit beiden Zeilen (`✓ erfüllt Gefälle 1 % ≥ Mindestgefälle 1 % (DN100)` und
`✓ erfüllt Gefälle 1 % ≤ empfohlenes Maximum 5 %`). **Was fällt, ist das Summen-Urteil, nicht die
Meldung.**

## A-17-6 · Der Zusatzbefund — benannt, NICHT geändert

*Der Wortlaut `Alle Prüfungen bestanden` (`EngineFlaeche.tsx:146`) ist in drei Engines irreführend,
weil das Flag nur Prüfungen der Schwere `fehler` zählt.* **Nicht geändert, und die Begründung des
Blattes trägt: der Satz steht an EINER Stelle und wirkt auf ALLE Panels — eine Änderung dort berührt
Engines, die dieser Auftrag nicht gemessen hat. Das wäre Beifang in der Sache.**

## A-17-7 · Browserabnahme — beide Panels am Bildschirm

**Das ausgelieferte Bündel, aus der Netzwerkantwort gehasht (nicht von der Platte):**

```text
md5 Datei                 62d7be7eac45f91b2d90147f740a01fa
md5 ueber HTTP (curl)     62d7be7eac45f91b2d90147f740a01fa
md5 im Browserlauf        62d7be7eac45f91b2d90147f740a01fa   (1 449 331 Bytes)
Elter-Buendel (A-14)      a5ea00566991cf15a5ce2a83c15e08f1
```

*Das am Bildschirm gemessene Artefakt ist Byte für Byte dasselbe wie das im Kandidaten.* Und es
**trägt** die Änderung: `Kein Entwaesserungsnachweis` 1× · `normative Auslegung sind` 1× ·
`keinGesamturteil` 4× (vorher 2×). *Im Elter-Bündel: die beiden Sätze je 0×.*

**Was auf dem Bildschirm stand — vier Flächen, je nach dem Rechnen gemessen:**

| Fläche | Plakette grün | Plakette **rot** | Label „Vorbehalt" |
|---|---|---|---|
| **engine-abwasser** | **0** | **0** | **1** |
| **engine-fbh** | **0** | **0** | **1** |
| `engine-kueche` (Gegenprobe) | **1** | 0 | 0 |
| `engine-heizkoerper` (Gegenprobe) | 0 | **1** | 0 |

> **Die letzte Zeile ist die schärfere Gegenprobe, und sie ist am Bildschirm belegt:**
> *`engine-heizkoerper` zeigt die **rote** Plakette „Eine Prüfung ist nicht bestanden" — dieselbe
> Engine, an der der Release-Prüfer A-14 geprüft hat.* **Das Flag unterdrückt zwei Engines, nicht
> negative Urteile allgemein.**

*Bühne: `scripts/browser-buehne.sh --port 8099`, Datenbank am Kindprozess geprüft — `ticket_testing`.
Chrome headful (System-Chrome; Puppeteers eigenes ist hier nicht installiert). Für die Anmeldung
wurde **ein** Nutzer in `ticket_testing` angelegt und nach der Messung wieder entfernt
(`[TEST-HARNESS] A-17 Browserabnahme`, danach `users` wieder 0). **Niemals gegen `ticket`.***

## Was mich vier Anläufe gekostet hat — und dreimal war es mein Werkzeug

*Das gehört in den Bericht, weil es beim Prüfen genauso passieren wird:*

```text
1  fester Selektor auf Karten          fuenf Klicks, fuenfmal false — die Insel hat eigene
                                       Klassen (hp-start-karte, hp-fn-label)
2  element.click() aus der Seite       trifft, loest aber React nicht aus -> echte Maus auf
                                       die Bildschirmkoordinaten
3  "Fussbodenheizung" gesucht          der Chip heisst "Fußbodenheizung" mit ß  -> H-6
4  nur die GRUENE Plakette gezaehlt    heizkoerper meldete 0 und sah "unterdrueckt" aus —
                                       es war die ROTE (EngineFlaeche.tsx:146)
```

> **Punkt 4 ist der gefährliche.** *Meine Messung hätte „auch heizkoerper ist stumm" gemeldet — das
> Gegenteil der Wahrheit, und ausgerechnet an der Gegenprobe, die den Auftrag trägt.* **Ein Zähler,
> der nur eine von zwei möglichen Antworten kennt, misst nicht.**

*Und ein Wegbefund, der niemandem sonst auffällt: die **Fachplaner-Chips der Startseite** öffnen
nicht die Engine-Fläche, sondern die L4-Vorschau — dort steht wörtlich „die Rechnung ist noch nicht
angeschlossen". **Die zu messende Fläche hängt am Fachplaner-Reiter der linken Schiene im
Expertenmodus** (`FussUndUeberlagerungen.tsx:209`). Wer am falschen Ort misst, misst eine Fläche,
die gar nichts rechnet.*

## `must_preserve` und Rückweg

| | Ergebnis |
|---|---|
| **geändert** | alle `resources/**` außer den drei Bau-Dateien byte-identisch |
| **hinzugefügt** | **1** — `__tests__/zweiEnginesSchweigen.test.ts` (der neue Test) |
| **entfernt** | **0** |
| **`app/**`** | **0 Dateien** |
| **Insel-Suite** | **1698 pass, 0 fail** (vorher 1694 — vier Zusagen mehr) |
| **Typprüfung** | `npm run tsc:hausplaner` → 0 Fehler |
| **Rückweg** | `git apply --check -R` → **Exit 0**, Arbeitsbaum unangetastet |

**Rückfallpunkt am Bautag, mit Befehl:** `fork/auto/hausplaner-integration` und
`backup-private/auto/hausplaner-integration` stehen auf `5579a6c0`; die B5/B6- und A-18-Commits
liegen darin. **Dieser Bau ist zum Zeitpunkt der Messung noch nicht außerhalb der Maschine.**

## Berührte Dateien

```text
resources/planner/hausplaner/geometry/abwassergefaelle.ts   +23 / -1
resources/planner/hausplaner/geometry/fbhAuslegung.ts       +23 / -0
resources/planner/hausplaner/app/dashboard/enginePanels.ts  +17 / -2
resources/planner/hausplaner/__tests__/zweiEnginesSchweigen.test.ts   NEU
public/hausplaner/hausplaner.js                             neu gebaut, md5 62d7be7e
docs/BERICHT-A-17-zwei-engines-schweigen.md                 dieser Bericht
docs/STATUS.md                                              Zustand an beiden Orten
```
