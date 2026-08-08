# BERICHT A-05 — Messung: was fehlt zwischen L-Kontur und l-shape-Dach

```yaml
auftrag: A-05
rolle: generator (Messlauf)
blatt: docs/auftraege/aktiv/A-05-messung-l-kontur-l-dach.md
basis_sha_blatt: 42c0320f   # historisch, laut Plan-Prüfer-Votum: Messung am aktuellen Stand
mess_sha: 4da0e84c
datum: 2026-08-08
lieferung: BERICHT — kein Produktivcode, keine Änderung in resources/, app/, tests/
```

## Messumgebung und Beweisform

- **Mess-SHA `4da0e84c`** (HEAD beim Start der Messungen). Während des Laufs bewegte sich der
  HEAD durch parallele Doku-Arbeit weiter (`f3faf111`, berührt nur
  `docs/auftraege/aktiv/A-04-buehnen-waechter.md`). **Alle acht gemessenen Quelldateien sind per
  content-diff byte-identisch zu `4da0e84c`** (Befehl und Ausgabe unten); die Messwerte hängen
  damit an keinem bewegten Code.
- **Runner:** `npm run test:hausplaner` (package.json:10) bzw. derselbe Runner direkt
  (`./scripts/node-runtime.sh --experimental-strip-types --import ./resources/planner/hausplaner/test-register.mjs --test …`).
  Kein Serverstart, keine Bühne, keine Datenbank (Rest-2-Entscheidung des Blatts eingehalten).
- **Vorhandene Zusagen gefahren:** volle Suite **1689/1689 pass** (`npm run test:hausplaner`,
  Rohzähler: `tests 1689 · pass 1689 · fail 0`).
- **Wegwerf-Probe:** `resources/planner/hausplaner/__tests__/zzA05wegwerf.test.ts`, 10 Zusagen,
  **10/10 pass**, Rohausgaben unten je Frage zitiert. **Vor diesem Bericht restlos entfernt, kein
  Commit trägt sie.** Sauberkeits-Beleg (die `git status`-Phantome unter `resources/` sind die
  bekannte A-07-Index-Klasse — deshalb content-diff statt Status-Lesen):

```text
for f in dachMesh.ts dachVerschneidung.ts dachGeometrie.ts roofShape.ts scene.types.ts \
         HausplanerApp.tsx EigenschaftenPanel.tsx nichtDarstellbar.ts; do
  git show 4da0e84c:$pfad/$f | diff -q - $pfad/$f; done
-> IDENTISCH zu 4da0e84c: alle acht Dateien
ls __tests__ | grep -i "zz\|wegwerf\|a05" -> kein Treffer (Probe geloescht)
```

- **Vermerk Nebenläufigkeit:** Der IN_ARBEIT-Übergang wurde vor der ersten Messung in
  `docs/STATUS.md` gesetzt (§3). Die Zeilen wurden von der parallel arbeitenden
  Plan-Prüfer-Instanz in `c2feffd4` (unbenannter Beifang, von ihr selbst in `4da0e84c` richtig-
  gestellt und benannt) mitgesichert — ein eigener Statuscommit war danach nicht mehr möglich
  („nothing to commit"). Inhaltlich ist der Übergang vollständig in Git.

---

## A-05-1 (Kernfrage): Welche Eingaben braucht `verschneidungsFlaechen` über `roofType` hinaus?

`dachMesh.ts:143` baut `e` mit `anbauZuEingabe(roof)` (dachMesh.ts:76–92); `dachMesh.ts:153`
ergänzt `form`. Das ergibt die zehn Felder von `VerschneidungEingabe`
(dachVerschneidung.ts:22–29):

| Feld | gebaut in (Renderer) | Herkunft im Modell | liefert der Anlege-Pfad heute? |
|---|---|---|---|
| `form: 'l'\|'t'\|'u'` | dachMesh.ts:153 (`roof.roofType === 't-shape' ? 't' : 'l'`) | `roof.roofType` (scene.types.ts:322) | **nein** — fest `'sattel'` (HausplanerApp.tsx:968) |
| `length` [m] | dachMesh.ts:82 (`a.length / 1000`) | `roof.anbau.length` [mm] (scene.types.ts:289) | **nein** — das Anlege-Objekt hat kein `anbau` (HausplanerApp.tsx:964–972) |
| `width` [m] | dachMesh.ts:83 | `roof.anbau.width` (scene.types.ts:290) | **nein** (wie oben) |
| `lengthB` [m] | dachMesh.ts:84 | `roof.anbau.lengthB` (scene.types.ts:291) | **nein**; das Panel bietet das Feld nur bei `u-shape` an (EigenschaftenPanel.tsx:286–293) |
| `widthB` [m] | dachMesh.ts:85 | `roof.anbau.widthB` (scene.types.ts:292) | **nein** (wie `lengthB`) |
| `overhang` [m] | dachMesh.ts:86 (`roof.ueberstandMm / 1000`) | `roof.ueberstandMm` (scene.types.ts:326) | **ja** — 500 (HausplanerApp.tsx:969) |
| `overhangGable` [m] | dachMesh.ts:87 (derselbe Wert) | `roof.ueberstandMm` | **ja** (derselbe Wert) |
| `pitchGrad` | dachMesh.ts:88 | `roof.neigungGrad` (scene.types.ts:324) | **ja** — 35 (HausplanerApp.tsx:968) |
| `height` [m] | dachMesh.ts:89 (`roof.traufhoeheMm / 1000`) | `roof.traufhoeheMm` (scene.types.ts:327) | **ja** — `level.elevation + level.defaultWallHeight` (HausplanerApp.tsx:969) |
| `rafterHeight` [cm] | dachMesh.ts:90 | Konstante `SPARREN_HOEHE_CM = 20` (dachMesh.ts:54) — nicht im Modell | entfällt (Konstante) |

**Torbedingungen davor:** `anbauZuEingabe` liefert `null`, wenn nicht **alle vier** Maße
(`length`, `width`, `lengthB`, `widthB`) `> 0` sind (dachMesh.ts:78) → `verschneidungsFlaechen`
gibt dann leer zurück (dachMesh.ts:143–144, ohne Wurf). Danach prüft `lTBauGueltig`
(dachMesh.ts:154; dachVerschneidung.ts:158–167) die Maße auf Baubarkeit (u. a. `widthB < width`).

**Was fehlt über `roofType` hinaus also konkret: `roof.anbau` mit allen vier Maßen.**

**Kann der Anlege-Pfad das aus einer gezeichneten Kontur liefern?** Heute **nein — es existiert
kein Code, der `anbau` aus einem Polygon ableitet.** Gemessen: Schreiber von `anbau` sind
ausschließlich das Eigenschaften-Panel (Nutzereingabe in mm-Felder, EigenschaftenPanel.tsx:271–275)
und Fixtures/Tests (`studioFixtures.ts`); `dachformVorlagen.ts` (Vorlagen-Patches mit Maßen) hat
außerhalb von Tests **keinen Konsumenten** außer einem Typ-Import (`grep -rn dachformVorlagen`:
nur `dachMesh.ts:13 import type`). Sachlich steckt die Maß-Information in einer achsparallelen
L-Kontur (Bbox + Kerbe), aber die Eingabe-Semantik verlangt eine **Zerlegung** in Hauptbau
(`length/width`) und Anbau (`lengthB/widthB`) (dachVerschneidung.ts:24–25), und eine L-Kontur legt
diese Zerlegung nicht eindeutig fest (zwei Lesarten, dazu die Orientierung gegen
`firstAzimutGrad`). **Ob und wie diese Zuordnung getroffen wird, ist eine Entscheidung — sie ist
nicht Gegenstand dieses Berichts.**

---

## A-05-2: Sind `lTBauGueltig` / `uBauGueltig` Erkenner oder Validierer?

**Signaturen:**

```ts
export function lTBauGueltig(e: VerschneidungEingabe): boolean   // dachVerschneidung.ts:158
export function uBauGueltig(e: UFormEingabe): boolean            // dachUForm.ts (Import dachMesh.ts:15)
```

Beide nehmen **Maße, kein Polygon** — sie können die Form einer Kontur strukturell gar nicht
sehen. **Aufrufe (Wegwerf-Probe, Rohausgabe):**

```text
A-05-2a lTBauGueltig({form:'l', length:12, width:8, lengthB:4, widthB:4,
                      overhang:0.5, overhangGable:0.5, pitchGrad:35,
                      height:2.5, rafterHeight:20})            = true
A-05-2b dieselben Masse — real gezeichnet war ein RECHTECK      = true   (er sieht keine Kontur)
A-05-2c dieselben Masse, widthB:9 (Anbau >= Hauptdach)          = false
```

**Ergebnis: VALIDIERER** — sie sagen „dieses L ist baubar" (endlich, > 0, Neigung echt,
`W_b < W`; dachVerschneidung.ts:159–166), niemals „diese Kontur IST ein L". Die Gegenprobe 2b
belegt es: dieselben Maße liefern `true`, egal was gezeichnet wurde.

**Gibt es im Bestand etwas, das die Form einer Kontur erkennt? Nein.** Gemessen
(`grep -rn "erkenn"` über die Insel): erkannt werden nur **Räume** (`erkenneRaeume`,
`geometry/roomDetection`, konsumiert in szene.ts:34/357, ableitungen.ts:33) — keine Dachform.
Das Nächstliegende ist `pruefeRechteckigeKontur` (dachGeometrie.ts:64–96): auch das ist ein
**Validierer** (Kontur ≈ Bounding-Box, sonst Wurf `kontur_nicht_rechteckig`), kein Erkenner —
er beantwortet „ist es ein Rechteck?", nicht „was ist es?".

---

## A-05-3: Was passiert heute beim Laden eines Dokuments mit `roofType: 'l-shape'`?

**Vorhandene Zusagen gefahren:** Suite 1689/1689. Keine vorhandene Zusage deckt den Lade-Fall
„`l-shape` ohne `anbau`" oder eine echte L-Kontur: `verdrahtungAnbau.test.ts:44–48` prüft
`l-shape` **mit** allen vier Maßen (auf Rechteck-Polygon), `:50–54` den Ohne-Maße-Fall nur für
`u-shape`; `verschneidungRender.test.ts:62` rendert `l-shape` mit Maßen. Deshalb Wegwerf-Probe
(RoofNode mit echter L-Kontur `[(0,0),(12000,0),(12000,4000),(4000,4000),(4000,8000),(0,8000)]`,
`roofType:'l-shape'`, `traufhoeheMm:2500`, **ohne** `anbau`) — **eigene Reproduktion der
Vormessung `9e97d274`, nicht übernommen:**

```text
A-05-3a sceneDocumentSchema.safeParse(dokument).success = true      (laedt ohne 422;
        roofType-Enum validation.ts:247, anbau optional :255)
A-05-3b dachMeshWelt  = {"dreiecke":[],"firstHoeheMm":2500}         (reproduziert 9e97d274)
A-05-3b dachflaechen.length = 0                                     (reproduziert 9e97d274)
A-05-3c nichtDarstellbareDaecher([dach]) = []                       (KEINE Meldung)
```

**Antwort:** Das Dokument lädt gültig, und das Dach ist ein **stilles leeres Dach**: kein Wurf,
keine Dreiecke, keine Fläche. Mechanik: `dachRoh` zweigt für Verschneidungsformen ab
(dachMesh.ts:215–217), `anbauZuEingabe` liefert ohne `anbau` `null` (dachMesh.ts:78) →
`verschneidungsFlaechen` gibt leer zurück (dachMesh.ts:143–144). Der A-01-4-Melder
`nichtDarstellbareDaecher` meldet **nur Würfe** (`catch DachGeometrieUngueltig`,
nichtDarstellbar.ts:42–48) — der Leer-ohne-Wurf-Pfad läuft an ihm vorbei (Probe 3c). Die
Beobachtung endet nach der Rest-2-Entscheidung an der Test-Ebene (kein Serverstart, keine Bühne);
für die Sichtkette darüber hinaus wäre eine Bühne nötig — **nicht gestartet, siehe „offene
Punkte"** unten.

---

## A-05-4: Die Lückenliste — „Nutzer zeichnet L-Kontur" → „l-shape-Dach steht"

Vorweg die zulässige Kurzantwort geprüft: **„nur die Formzuweisung" genügt NICHT** — Beleg
Punkt 2 (Probe A-05-4a: das Anlege-Tor wirft trotz `roofType:'l-shape'`) und Punkt 3 (ohne
`anbau` bleibt das Mesh leer, Probe A-05-3b).

1. **Die Formzuweisung fehlt.** Der Anlege-Pfad setzt `roofType` **immer** fest auf `'sattel'`
   (HausplanerApp.tsx:968), egal welche Kontur gezeichnet wurde; die gezeichnete Kontur landet
   nur in `polygon` (HausplanerApp.tsx:967). Nichts im Bestand setzt je `'l-shape'` aus einer
   Kontur.
2. **Das Anlege-Tor kennt den Verschneidungs-Pfad nicht.** Das A-01-Tor fragt
   `dachGeometrie.dachFlaechen` (HausplanerApp.tsx:986), und diese Funktion prüft für **jede**
   Form zuerst `pruefeRechteckigeKontur` (dachGeometrie.ts:107) — anders als der Renderer, der
   für l/t/u vorher abzweigt (dachMesh.ts:215–217). Gemessen (Probe A-05-4a):
   `dachFlaechen(l-shape + L-Kontur)` → Wurf `DachGeometrieUngueltig` („Traufkontur ist nicht
   rechteckig …"). Für l/t/u mit rechteckiger Kontur liefert sie `[]` (dachGeometrie.ts:148–151,
   „Flächenprojektion … folgt in Stufe 2").
3. **`roof.anbau` fehlt und wird nirgends aus der Kontur abgeleitet.** Der Verschneidungs-Pfad
   verlangt alle vier Maße (dachMesh.ts:78); der Anlege-Pfad setzt kein `anbau`
   (HausplanerApp.tsx:964–972); Schreiber sind nur Panel-Eingabefelder und Fixtures (A-05-1,
   grep-Beleg). Mit allen vier Maßen rendert `l-shape` — auch mit L-Kontur (Probe A-05-4b:
   10 Dreiecke, First 5482 mm).
4. **Ein Form-Erkenner existiert nicht** (A-05-2): `lTBauGueltig`/`uBauGueltig` validieren Maße,
   `pruefeRechteckigeKontur` validiert Rechteckigkeit; nichts klassifiziert eine Kontur als L/T/U.
5. **Auch im Zielzustand formt die Kontur das Dach nicht.** `verschneidungsFlaechen` liest vom
   Polygon ausschließlich die **Bbox-Mitte als Platzierungs-Anker** (dachMesh.ts:179, `polygonBbox`
   :62–69); die gesamte Geometrie kommt aus `anbau` + RoofNode-Skalaren. Gemessen (Probe
   A-05-4c): gleiche `anbau`-Maße auf L-Kontur vs. Rechteck-Polygon → identische Dreieckszahl
   (10/10), identische Firsthöhe (5482/5482), identischer erster Eckpunkt
   `{"x":4000,"y":10500,"z":5481.78…}` (die Bbox-Mitten fallen hier zusammen). Kontur und
   gerendertes Dach sind nur über den Anker gekoppelt — Deckungsgleichheit ist heute
   Nutzer-Verantwortung, kein Code-Vertrag.
6. **Der Lade-Pfad hat keinen Melder für den Leer-Fall.** `l-shape` ohne `anbau` lädt gültig und
   bleibt still leer; `nichtDarstellbareDaecher` fängt nur Würfe (A-05-3, Proben 3a–3c). Der
   Hinweis auf fehlende Maße existiert nur im Eigenschaften-Panel bei selektiertem Dach
   (EigenschaftenPanel.tsx:296–302).
7. **Messbefund am Rand (Panel ↔ Mesh-Torbedingung):** Für L/T bietet das Panel nur
   `length`/`width` an (`lengthB`/`widthB`-Felder nur bei `istU`, EigenschaftenPanel.tsx:286–295)
   und sein Fehlt-Hinweis verlangt für L/T nur diese zwei (EigenschaftenPanel.tsx:276, 300);
   das Mesh-Tor verlangt alle vier (dachMesh.ts:78). Gemessen (Probe A-05-4d): `l-shape` mit
   `anbau {length, width}` → `dreiecke []`. Die Panel-Zusage „L/T-Dach braucht Außenmaß Länge und
   Breite > 0 — sonst rendert es nicht" trifft damit nicht die tatsächliche Torbedingung.
   *Einordnung und ggf. Auftrag: Sache des Planners.*
8. **Falls die Ableitung Kontur → Maße je gebaut wird: die Zerlegung ist unterbestimmt** (A-05-1,
   letzter Absatz): Hauptbau/Anbau-Zuordnung und Orientierung gegen `firstAzimutGrad` sind aus
   der Kontur allein nicht eindeutig — die Eingabe-Semantik (dachVerschneidung.ts:24–25) verlangt
   die Zuordnung aber. Festgehalten als Messfeststellung, nicht als Entwurf.

**Rohausgabe der Probe (vollständig, 10/10):**

```text
A-05-2a lTBauGueltig(L-Masse) = true
A-05-2b lTBauGueltig(gleiche Masse, Kontur egal) = true
A-05-2c lTBauGueltig(widthB>=width) = false
A-05-3a safeParse.success = true
A-05-3b dachMeshWelt = {"dreiecke":[],"firstHoeheMm":2500}
A-05-3b dachflaechen.length = 0
A-05-3c nichtDarstellbareDaecher = []
A-05-4a Wurf = Traufkontur ist nicht rechteckig — V1 unterstützt nur rechteckige Grundrisse (kein stilles Falschdach).
A-05-4b dreiecke.length = 10 firstHoeheMm = 5482
A-05-4c dreiecke L-Kontur = 10 · Rechteck = 10
A-05-4c firstHoeheMm L = 5482 · Rechteck = 5482
A-05-4c erster Eckpunkt L = {"x":4000,"y":10500,"z":5481.7877550532885} · Rechteck = {"x":4000,"y":10500,"z":5481.7877550532885}
A-05-4d dreiecke = [] firstHoeheMm = 2500
tests 10 · pass 10 · fail 0
```

## Offene Punkte

- **Sichtkette (Bühne):** Was der Nutzer beim Laden eines `l-shape`-Dokuments tatsächlich im
  Canvas sieht (bzw. nicht sieht), wurde nach der Rest-2-Entscheidung **nicht** auf einer Bühne
  geprüft. Die Test-Ebene belegt die leere Geometrie und den fehlenden Melder; falls der Planner
  die Sicht-Ebene zusätzlich belegt haben will, ist das eine Rückfrage an ihn — hier wurde
  keine Bühne gestartet.

## Was dieser Bericht NICHT sagt

- **Kein Urteil über A-01.** Ob A-01s Nicht-Ziel bleibt, entscheidet der Planner mit diesem
  Bericht — Punkt 2 der Lückenliste ist eine Fundstelle, keine Bewertung von A-01.
- **Keine Empfehlung, was gebaut werden soll.** Die Lückenliste misst, sie plant nicht; auch
  Punkt 7 und 8 sind Messfeststellungen ohne Bau-Vorschlag.
- **Keine Aussage über Walm/Krüppelwalm.**
