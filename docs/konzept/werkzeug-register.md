# Werkzeug-Register — Registerzeile ↔ Modul ↔ Registry-Kennung ↔ Reifegrad ↔ Paket ↔ Zielmenü

```yaml
art: "REGISTER (Konzept-Tabelle) — keine Entscheidung, kein Bau"
rolle: planner
auftrag: "SPEZ-planner-anschlusswelle-1 gen 17, Posten 1 (Inhalt aus gen 16)"
mess_sha: 6e37d2b5
gemessen_am: "2026-08-22, Worktree ticket-rolle-planner, Zweig rolle/planner"
zweck: "Die eine Tabelle, die Werkzeugregister, Modulbestand, Registry und Anschlusspakete
        zusammenfuehrt — damit die Anschlusswelle Kennungen hat statt Vermutungen."
kein_bau: "Kein Produktcode, keine toolRegistry-Aenderung."
```

## Die vier Grundmengen, je mit Befehl und Stand `6e37d2b5`

```
Registerzeilen im Werkzeugregister (Reifegrad BESCHRIEBEN)                        37
  docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md, Spalte 3 isoliert
  dazu: ENTWORFEN 2 · GEGENSTANDSLOS 2
Module im Hausplaner (.ts/.tsx, ohne __tests__/__domtests__, ohne .d.ts)         160
  resources/planner/hausplaner
  davon erreichbar (BFS ab main.tsx, Laufzeit-Kanten)                            133
  davon NICHT erreichbar                                                          27
  davon echte Kandidaten (ohne die 3 reinen Typmodule)                            24
Kennungen in der toolRegistry                                                     13
  auswahl · wand · fenster · tuer · dach · decke · treppe · bemassen
  flaeche-messen · kontur · loeschen · duplizieren · trimmen
Anzeigeort der Leiste                    app/rahmen/GruppenzeileUndSchiene.tsx
```

> **Die Zahlen 37 und 13 sind die des Auftrags, unabhängig nachgemessen und bestätigt.**
> *Beim ersten Griff gab mein Zähler **49** statt 37 — er zählte die Worterklärungen im Kopf des
> Registers mit. Erst die Zählung über die isolierte Reifegrad-Spalte trifft.*

---

## Wie die Zuordnung Modul → Registerzeile entstanden ist

**Gemessen, nicht geraten:** für jedes Modul wurde gesucht, welche Werkzeugblätter unter
`docs/rollenkette/werkbank/02-WERKZEUGE/` es namentlich nennen.

```
grep -rl '<modulname>' docs/rollenkette/werkbank/02-WERKZEUGE --include='*.md'
  | grep -oE 'W-[0-9]+' | sort -u
```

**Grenze dieses Verfahrens, ausdrücklich:** es misst **Erwähnung in einem Blatt**, nicht
Verwendung im Code. Ein Modul, das in sechs Blättern vorkommt, gehört nicht zu sechs Werkzeugen —
es wird von sechs Blättern *erwähnt*. **Wo die Zuordnung mehrdeutig ist, steht das hier und wird
nicht auf einen Wert verkürzt.** *Ort ist nicht Wirkung — dieselbe Regel, an der heute zwei
VERWERFEN-Empfehlungen gescheitert sind.*

---

## Register — die 24 Anschlusskandidaten

Reifegrad-Stufen dieser Tabelle: **CODE** (Fachlogik da, geprüft) · **PRODUKTWEG** (erreichbar im
Produktivpfad) · **BROWSER** (bedienbar und browserabgenommen). Alle 24 stehen heute auf **CODE**.

### Paket 3 — Prüfungen und Warnungen · Entscheidung: **ANSCHLIESSEN, zuerst**

| Modul | Z. | Registerzeile | Registry-Kennung | Reifegrad | Zielmenü |
|---|---|---|---|---|---|
| `geometry/integrationAbgleich.ts` | 135 | **W-40** Gültigkeitsstatus | — | CODE | **offen** |
| `geometry/aufbautenStatus.ts` | 77 | **W-22** Gaube | — | CODE | **offen** |
| `geometry/grundriss.ts` | 154 | **mehrdeutig**: W-05 · W-08 · W-10 · W-11 · W-17 · W-26 | — | CODE | **offen** |

**Keines der drei hat eine Registry-Kennung** — sie sind keine Werkzeuge im Sinne der Leiste,
sondern **Prüfungen, die zu einem Werkzeug gehören**. *Das ist für Posten 4 die entscheidende
Feststellung: „ein Werkzeug = ein Blatt" braucht hier eine andere Anbindung als einen Leisteneintrag.*

### Paket 1 — Massenermittlung · **ANSCHLIESSEN, als zweites**

| Modul | Z. | Registerzeile | Registry-Kennung | Reifegrad | Zielmenü |
|---|---|---|---|---|---|
| `geometry/wandFlaeche.ts` | 253 | W-02 · W-04 · **W-08** Dachfläche messen | `flaeche-messen` (W-08) | CODE | **offen** |
| `geometry/auswechslung.ts` | 195 | W-21 · W-22 · W-25 · W-28 · W-29 | — | CODE | **offen** |
| `geometry/wandaufbau.ts` | 96 | W-02 · W-10 · W-22 | — | CODE | **offen** |
| `geometry/holzBauteile.ts` | 99 | W-21 · W-25 · **W-43** Abbund | — | CODE | **offen** |
| `geometry/holzMengen.ts` | 81 | **W-20** Stückliste und Mengen · W-21 | — | CODE | **offen** |

### Paket 2 — Dach · **AUFGETEILT** (Dirigent 14:12:32, nach Zimmerer-Fachlinse)

**7 Module · 1106 Zeilen.** *Nicht mehr ein Paket mit einer Entscheidung, sondern drei Klassen:*

| Modul | Z. | Registerzeile | Kennung | Entscheidung |
|---|---|---|---|---|
| `projection/dachProjektion.ts` | 43 | **W-07** | — | **ANSCHLIESSEN** (echte Verdrahtung, eigenes Spur-W-Blatt) |
| `geometry/dachOeffnung.ts` | 96 | W-07 · W-21 · W-26 · **W-29** Durchdringungen | — | **ANSCHLIESSEN** (echte Verdrahtung, eigenes Spur-W-Blatt) |
| `geometry/sparrenTrennung.ts` | 67 | **W-21** Sparren und Lattung · W-25 · W-29 | — | **zurückgestellt** — braucht Modell-/Renderer-Fähigkeit |
| `geometry/dachTopologie.ts` | 183 | W-27 | — | **zurückgestellt** — braucht Modell-/Renderer-Fähigkeit |
| `geometry/schifterListe.ts` | 152 | W-21 · W-25 · W-27 · **W-43** | — | **zurückgestellt** — braucht Modell-/Renderer-Fähigkeit |
| `geometry/dachAusschnitt.ts` | 531 | **W-07** Dach aus Kontur · W-08 · W-26 · W-29 · W-30 | `dach`/`kontur` (W-07) | **klären** — fremdes Haus, Meter gegen mm |
| `geometry/dachVorlage.ts` | 34 | **W-07** · W-30 | — | **klären** — zweite Wahrheit neben `dachformVorlagen.ts` |

**Reihenfolge:** die zwei Anschlüsse kommen **nach Paket 3 und Paket 1**. Die zwei Klärungen sind
**eigene Klärungsposten, kein Anschluss**. Die vier Zimmerer-Befunde **B-1…B-4** werden in den
Register-Zeilen der Dachmodule **zitiert, nicht nachgebaut** (P-02 Punkt 4), sobald sie vorliegen.

> **Meine Kopfzahl war falsch und die Korrektur ist belegt:** die Anschluss-Vorlage nannte
> **1010 Zeilen**. Selbst nachgemessen, Modul für Modul: **1106**.
> *Die Differenz ist exakt `dachOeffnung.ts` (96)* — es stand in meiner Tabelle, fiel aber aus der
> Summe. **Eine Summe ohne Gegenprobe**, obwohl ich die Summenprobe an diesem Vormittag zweimal
> selbst als Rettung erlebt habe. Der Dirigent nennt 1106; die Zahl ist von mir nachgerechnet,
> nicht übernommen.

### Paket 4 — Einzelstücke · Entscheidung: **PARKEN**

| Modul | Z. | Registerzeile | Registry-Kennung | Reifegrad | Zielmenü |
|---|---|---|---|---|---|
| `geometry/treppenTypen.ts` | 153 | **W-09** Treppe | `treppe` | CODE | geparkt |
| `geometry/treppeSvg.ts` | 142 | **W-09** Treppe | `treppe` | CODE | geparkt |
| `projection/raumProjektion.ts` | 125 | **keine** | — | CODE | geparkt |
| `geometry/heizkreisVerteiler.ts` | 58 | **keine** | — | CODE | geparkt |

> **Bemerkenswert und für die Parken-Entscheidung nachträglich stützend:** `treppenTypen` und
> `treppeSvg` haben als **einzige** des Paketes eine Registry-Kennung (`treppe`, W-09) — der
> Leisteneintrag existiert, die Fachlogik hängt daran nicht. *Ein halbes Werkzeug, wie in der
> Vorlage beschrieben.* `raumProjektion` und `heizkreisVerteiler` haben **gar keine**
> Registerzeile: sie gehören zu keinem beschriebenen Werkzeug.

### Werkzeug-Infrastruktur — kein Anschlussgegenstand

| Modul | Z. | Befund |
|---|---|---|
| `app/tools/werkzeugLandkarte.ts` | 271 | erwähnt in W-03 · W-07 · W-10 · W-18 · W-29 — **Verwaltung, kein Werkzeug** (offene Frage aus der Vorlage) |
| `app/tools/trefferSuche.ts` | 75 | W-13 · W-14 — Auswahl-Hilfslogik |
| `app/tools/auswahlDarstellung.ts` | 71 | W-13 · W-14 — Auswahl-Hilfslogik |

---

## Posten 2 — die zwei Klärungsposten

### `app/tools/toolCatalogStillgelegt.ts` — **BEHALTEN als Wächter und Muster**

| | |
|---|---|
| **Exporte** | **1** — `STILLGELEGT_INDESIGN_KATALOG` (`:20`) |
| **Verbraucher** | **3** — davon **1 echter Import** |
| **echter Import** | `__tests__/toolKatalog.test.ts:10`, prüft `length === 54` und baut ein Set der alten IDs |
| **nur Kommentar** | `app/studioDaten.ts`, `app/tools/toolCatalog.ts` — zitieren es als Muster für „stillgelegt statt gelöscht" |

**Ergebnis: behalten.** Verwerfen bräche einen laufenden Test und beseitigte den Wächter, dass
**54 alte Werkzeug-IDs nicht zurückkehren**.

> **Meine ursprüngliche Zahl war „0 Exporte" und sie war falsch.** Mein Muster verlangte bei
> `const` eine **Funktionsform**; der Export ist ein **Array**. Die Spalte hieß
> „Funktionen/**Werte**", gezählt wurden nur Funktionen. **Der Befehl maß enger als sein Label.**

### `geometry/werkzeugRegistry.ts` — **ABGRENZUNG BELEGT, kein Doppel**

**Die Codebasis zieht die Grenze selbst** — `app/tools/toolTypes.ts:8`, im Wortlaut:

> *„Abgrenzung: `geometry/werkzeugRegistry.ts` beschreibt **BAUTEILE** (Parametrik/Fähigkeiten).
> Diese Datei beschreibt **WERKZEUGE** (Bedienung/Aktivierung). Ein Werkzeug KANN ein Bauteil
> referenzieren (`bauteilKind`), **dupliziert es aber nicht**."*

| | `app/tools/toolRegistry.ts` | `geometry/werkzeugRegistry.ts` |
|---|---|---|
| Zeilen / Exporte | 395 / 11 | 68 / 7 |
| Gegenstand | Werkzeuge (Bedienung) | Bauteile (Parametrik) |
| Kennbegriffe | 13 Werkzeug-IDs | `WerkzeugKategorie` (`bau`\|`bauelement`\|`haustechnik`\|`pv`), `Parametrik` |

**Ergebnis: kein Doppel — die Begriffe sind Bauteilbegriffe, keine Bedienbegriffe.**

> **Mein ursprünglicher Befund war „zwei Registries für dieselbe Frage".** Ich habe von der
> **Namensähnlichkeit** geschlossen, ohne die Abgrenzung zu lesen. *Der Name war mein Kriterium,
> nicht die Sache.* **Kein Gegenbefund** — die Abgrenzung trägt.

#### Entscheidung: **STILLLEGEN** (Dirigent 14:11:33, nach Fachlinse Software-Architekt)

**Eine Fachmessung geht über meinen Befund hinaus und ändert die Antwort:** `werkzeugRegistry.ts`
ist **nicht der Zwilling von `toolRegistry`** (die Datenmodelle sind disjunkt — das deckt sich mit
meiner Messung), sondern **der unbenutzte vierte Entwurf der Bauteil-Schicht** neben
`scene.types`, `geometry/*` und `enginePanels.ts:89` (`EngineErgebnis`) / `faehigkeiten.ts`.
**0 Produktivverbraucher.**

**Register-Zeile: `stillzulegen (Kleinblatt nach Paket 3)`** — nach dem Muster
`toolCatalogStillgelegt`: **nicht verwerfen, nicht anschließen, NICHT mit `toolRegistry`
zusammenführen.** Der Grund gehört in den Dateikopf. *Kein Blatt jetzt.*

> **Meine Frage war die falsche.** Ich habe gefragt: *„welche der zwei Registries gilt?"* — die
> richtige Frage lautet **„Parametrik oder `EngineErgebnis`?"**, und **`EngineErgebnis` lebt**.
> *Ich hatte die Abgrenzung Werkzeug/Bauteil gemessen und damit die Doppelung widerlegt; dass
> daneben eine dritte und vierte Bauteil-Sicht steht, lag außerhalb meines Blickfelds.* Damit ist
> auch mein „was offen bleibt" beantwortet: `werkzeugRegistry` bekommt **keinen** Produktivpfad.

---

## Die Zielmenü-Spalte — warum sie fast überall „offen" sagt

**Der Auftrag verlangt: „jetzt messen, wo messbar; sonst 'offen'".** Messbar wäre sie über
`GruppenzeileUndSchiene.tsx` — dort entsteht die Leiste. **Gemessen ist bisher nur, dass die
13 Registry-Kennungen dort hängen; welcher Menüpunkt zu welchem der 24 Module gehört, ist es
nicht.** *Von den 24 tragen genau drei überhaupt eine Registry-Kennung
(`wandFlaeche`→`flaeche-messen`, `dachAusschnitt`→`dach`/`kontur`, `treppenTypen`/`treppeSvg`→`treppe`).*

**Die übrigen 21 haben keinen Leisteneintrag** — für sie ist „Zielmenü" nicht *unbekannt*, sondern
**noch nicht entschieden**. Das gehört in die Anschlussblätter (Posten 4, Kriterium N4 Bedienweg),
nicht in dieses Register.

> **Das ist die benannte Lücke aus der Anschluss-Vorlage, jetzt genauer:** sie ist kleiner als
> gedacht (drei Module *haben* eine Kennung) und zugleich anders gelagert — bei 21 Modulen ist der
> Menüpunkt keine Messung, sondern eine **Gestaltungsentscheidung** des jeweiligen Anschlussblatts.

## Folge für Posten 4 (Anschlusswelle Paket 3)

**Die drei Module von Paket 3 haben keine Registry-Kennung und sind keine Leistenwerkzeuge.**
`ein Werkzeug = ein Blatt` gilt weiter, aber der **Bedienweg (N4)** kann bei ihnen nicht
„Leisteneintrag anklicken" heißen.

**Der Dirigent hat daraufhin Kriterium (a) zweigleisig präzisiert (14:15:26)** — je Blatt genau
eine Zeile:

```
Leistenwerkzeug   (a) Werkzeug in der Leiste sichtbar (toolRegistry-Kennung <K>)
Pruefung/Warnung  (a) Meldung erscheint am Objekt bzw. im Statusbereich, ausgeloest durch die
                      Bearbeitung selbst — Ort je Blatt benannt (Komponente/Panel, Pfad) und im
                      Browser belegt; N4 nennt das TRAGENDE Werkzeug statt einer Leistenkennung
```

**(b) und (c) bleiben unverändert:** Bearbeiten erzeugt die Meldung · ohne das Modul erscheint sie
nicht (Rot-Probe).

**Für `grundriss.ts` (sechs Blätter, mehrdeutig) ist die Zuordnung ausdrücklich meine
Gestaltungsentscheidung im Blatt — benennen, nicht offenlassen.** *Damit ist die Mehrdeutigkeit
kein Dauerzustand: das Register hält sie fest, das Blatt entscheidet sie.*

| Modul | tragendes Werkzeug für N4 |
|---|---|
| `integrationAbgleich.ts` | **W-40** Gültigkeitsstatus |
| `aufbautenStatus.ts` | **W-22** Gaube |
| `grundriss.ts` | **im Blatt zu entscheiden** — aus W-05 · W-08 · W-10 · W-11 · W-17 · W-26 |
