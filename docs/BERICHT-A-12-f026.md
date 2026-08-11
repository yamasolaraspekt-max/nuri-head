# BERICHT A-12 — F-026 ausgeführt: ein L-Grundriss gerechnet, das Ergebnis angesehen

```yaml
auftrag: A-12
rolle: generator (Messlauf, frische Instanz — Übernahme eines laufenden Auftrags)
blatt: docs/auftraege/aktiv/A-12-f026-ausfuehren.md
basis_sha: d1d716c8
mess_sha: 239a163e
mess_sha_start: c33d4c1a
datum: 2026-08-11
lieferung: BERICHT — kein Produktivcode, keine Änderung in resources/, scripts/, app/
scope_geaendert: [docs/BERICHT-A-12-f026.md, docs/STATUS.md]
scope_bewusst_nicht_geaendert: [FORMELSAMMLUNG.md, VORGEHEN.md]   # Begründung: Abschnitt 7
tests:
  statisch: nicht_anwendbar
  unit: "1692/1692"
  backend: nicht_anwendbar
  schema: pass
  build: nicht_anwendbar
  browser: nicht_anwendbar
ampel_vorschlag: "🟢 — mit einer Wortlaut-Korrektur am Formelblatt (Abschnitt 6)"
```

## 0. Übernahme, nicht Neustart

Die A-12-Instanz vom 10.08. hat `IN_ARBEIT` und die §7-Vorprüfung gesetzt und belegt (`4e935e84`,
Datensatz `docs/STATUS.md`), danach starb sie am Wochenlimit der Umgebung — 24,5 h ohne Commit,
kein Bericht. Der Plan-Prüfer hat die Lage gemessen und gemeldet (`2d45785f`, Befundblock in
`docs/STATUS.md`). **Ich setze `IN_ARBEIT` nicht erneut**, sondern übernehme den laufenden Auftrag
und vermerke die Übernahme im Datensatz.

**Doppel-Launch-Prüfung vor dem ersten Schritt** (Lehre P-02):

```text
$ git log --oneline --all | grep -i "A-12"
-> 4e935e84 (IN_ARBEIT), 1181dee7 (Schnitt), 876a64b2/2d45785f/a7375a11 (Fremdmeldungen)
   KEIN Bau-Commit einer zweiten A-12-Instanz
$ ls docs/BERICHT-A-12-f026.md
-> No such file or directory
```

**Was ich von der toten Instanz übernommen habe:** die untracked liegengebliebene Wegwerf-Probe
`resources/planner/hausplaner/__tests__/zzA12wegwerf.test.ts` (14.612 B, in keinem Commit). Ich habe
sie gelesen, gegen die Attrappen-Regel des Blatts geprüft (Abschnitt 3), gefahren und **restlos
entfernt** (Abschnitt 5). Sie war die Arbeit meines Vorgängers; ich verantworte sie ab hier selbst.

## 1. Messumgebung und Beweisform

- **Mess-SHA `239a163e`** (HEAD am Ende des Laufs). Start war `c33d4c1a`; dazwischen liefen drei
  fremde Commits (`23839610`, `c9325929`, `239a163e`), die **ausschließlich `docs/**` berühren:

```text
$ git diff --name-only c33d4c1a..239a163e
docs/BEFUND-MUSTPRESERVE-LUECKE.md
docs/STATUS.md
docs/auftraege/aktiv/W-05-raum-beschreiben.md

$ git diff --name-only 3e7e19d6..HEAD -- resources/ | wc -l
0        # Insel-Geometrie seit dem mess_sha des Vorgängers unbewegt
$ git merge-base --is-ancestor d1d716c8 HEAD ; echo $?
0        # Blatt-Basis ist Vorfahr

# content-diff der fünf gemessenen Insel-Dateien (A-05-Muster: Inhalt statt git-status,
# wegen der bekannten A-07-Index-Phantome)
$ for f in geometry/dachVerschneidung.ts geometry/dachGeometrie.ts geometry/dachUForm.ts \
           renderers/three-d/dachMesh.ts domain/scene.types.ts ; do
    git show HEAD:$pfad/$f | diff -q - $pfad/$f ; done
-> IDENTISCH: alle fünf
```

  **Die Messwerte hängen damit an keinem bewegten Code.**

- **Runner:** `npm run test:hausplaner` (`package.json:10`) bzw. derselbe Runner direkt
  (`./scripts/node-runtime.sh --experimental-strip-types --import ./resources/planner/hausplaner/test-register.mjs --test …`).
  Kein Serverstart, keine Bühne, **keine Datenbank** — §15 ist mangels Datenzugriff nicht berührt.
- **Fremdquelle, aufs Byte wie im Blatt:**
  `/Users/yamanuri/Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d/dachdecker_pro_3d.tsx`
  — `wc -lc` → **2173 Zeilen, 132.374 Byte**. **Kein Byte davon ist ins Repo kopiert worden**; der
  Fremdcode wurde zur Laufzeit von seinem Desktop-Pfad gelesen, per Zeilenschnitt isoliert und mit
  `node:module.stripTypeScriptTypes` ausgeführt.
- **Die eingesetzten Maße** (alle Rohausgaben unten beziehen sich auf sie, Meter im Engine-Raum):

```text
L (length) = 12 · W (width) = 8 · WB (widthB) = 4 · LB (lengthB) = 4
Neigung 35° · Überstand oh = ohG = 0,5 · Traufhöhe h = 2,5 · Sparrenhöhe 20 cm
Grenzfall-Läufe zusätzlich mit WB = 8 (= W) und WB = 10 (> W)
```

---

## 2. A-12-1 — TEIL A: die Konturen (trivial ausführbar)

**Fundstelle:** `dachdecker_pro_3d.tsx:95–121`, Funktion `buildTopologyPolygon`; die beiden
l-shape-Zweige stehen in `:101–106` (`category === 'flat'`) und `:107–112` (der übrige, also
`pitched`). Geschnitten wurde exakt `:95–121` (die Probe gibt den Schnitt selbst aus).

**Rohausgabe:**

```text
A12| Schnitt buildTopologyPolygon = Quelle:95-121 · getDefaultEdgeTopologyConfigs = :123-132 · analyzeTopology = :134-171
A12| 1a KONTUR flat    (L=12,W=8,WB=4,LB=4): [{"x":-6,"y":-4},{"x":6,"y":-4},{"x":6,"y":0},{"x":-2,"y":0},{"x":-2,"y":4},{"x":-6,"y":4}]
A12| 1b KONTUR pitched (L=12,W=8,WB=4,LB=4): [{"x":-6,"y":-4},{"x":6,"y":-4},{"x":6,"y":0},{"x":2,"y":0},{"x":2,"y":8},{"x":-6,"y":8}]
A12| 1c flat:    punkte=6 geschlossen=true doppelte=0 flaeche=64  selbstschnitte=0 einspringend=[{"i":3,"p":{"x":-2,"y":0}}]
A12| 1c pitched: punkte=6 geschlossen=true doppelte=0 flaeche=112 selbstschnitte=0 einspringend=[{"i":3,"p":{"x":2,"y":0}}]
```

**Die drei verlangten Prüfungen — bestanden, für beide Varianten:**

| Prüfung | flat | pitched | wie gemessen |
|---|---|---|---|
| **geschlossen** | ja | ja | 6 Punkte, 0 doppelte Nachbarpunkte, Shoelace-Fläche ≠ 0 (implizit umlaufend, kein Wiederholungspunkt am Ende) |
| **nicht selbstschneidend** | ja (0) | ja (0) | alle nicht benachbarten Segmentpaare auf echte Kreuzung geprüft |
| **einspringende Ecke** | genau 1, bei (−2, 0) | genau 1, bei (2, 0) | Kreuzprodukt-Vorzeichen gegen den Umlaufsinn |

*Die drei Messinstrumente sind meine eigenen (Shoelace, Segmentschnitt, Kreuzprodukt) — nicht die
des Fremdcodes. Sonst hätte ich den Fremdcode mit sich selbst geprüft.*

**BEFUND A-12-1/a — die beiden Konturvarianten beschreiben nicht dasselbe Gebäude.**
Bei identischer Eingabe liefert `flat` **64 m²** und `pitched` **112 m²**. `flat` **schneidet** den
Anbau aus dem Rechteck L×W heraus (96 − 32 = 64), `pitched` **setzt ihn außen an** (y läuft bis
`W/2 + LB` = 8). Die Kantenliste des Blatts verlangte, beide zu nennen und keine zu wählen — sie
sind nicht zwei Schreibweisen derselben Form, sondern zwei verschiedene Gebäude.

**BEFUND A-12-1/b — Grenzfall WB ≥ W:**

```text
A12| 1f GRENZFALL WB=8 flat:    [...,{"x":-2,"y":4},{"x":-2,"y":4},...] doppelte=1 selbstschnitte=0 einspringend=0
A12| 1g GRENZFALL WB=8 pitched: [{"x":-6,"y":-4},{"x":6,"y":-4},{"x":6,"y":0},{"x":-2,"y":0},{"x":-2,"y":8},{"x":-6,"y":8}] doppelte=0 selbstschnitte=0 einspringend=1
A12| 1f GRENZFALL WB=10 flat:   [...] doppelte=0 selbstschnitte=0 einspringend=1
A12| 1g GRENZFALL WB=10 pitched:[...,{"x":-4,"y":0},{"x":-4,"y":8},...] doppelte=0 selbstschnitte=0 einspringend=1
```

Bei `WB = W` **entartet die flat-Variante**: zwei Punkte fallen aufeinander (`doppelte=1`), die
einspringende Ecke verschwindet — aus dem L wird ein Rechteck mit einem Nullsegment, **ohne
Meldung**. Die pitched-Variante entartet nicht, dreht aber bei `WB > W` die Schenkelrichtung
(x = −4 statt +2), ohne dass ein Grenzwert geprüft würde. *Nirgends im Bereich `:95–121` steht eine
Gültigkeitsprüfung; `Math.max(0.1, …)` in `:96–99` fängt nur Nullwerte.*

**Ergebnis A-12-1: „F-026 kann eine L-Kontur" ist BELEGT** — sechs Punkte, geschlossen,
schnittfrei, mit genau einer einspringenden Ecke, in beiden Varianten.

---

## 3. A-12-2 — TEIL B: die Flächen. **Weg 1 gewählt, Weg 1 gelaufen**

### 3.1 Wegwahl und Begründung

**Gewählt: Weg 1** (three.js aus dem Repo, Klasse minimal gestellt, echte Funktion laufen lassen) —
**zusätzlich** wurde die Weg-2-Kontrolle als Gegenprobe mitgerechnet.

Begründung, an der Sache und nicht an der Bequemlichkeit:

1. Das Blatt sagt selbst: *„Weg 1 belegt ‚der Code läuft', Weg 2 belegt nur ‚die Rechnung stimmt'"*.
   Die Ampel-Sperre lautet wörtlich **„noch nicht ausgeführt"** (`FORMELSAMMLUNG.md:302`) — sie fragt
   nach dem Lauf, nicht nach der Arithmetik. Weg 2 hätte die Sperre formal nicht aufgehoben.
2. Die Voraussetzung trug: `three` liegt im Repo (`package.json:54`, `^0.163.0`), und der Ausschnitt
   braucht davon nur `Vector2/Vector3/Euler/Mesh/CylinderGeometry`.
3. Der Preis war messbar klein: der Klassenzustand des Ausschnitts umfasst **sechs** Namen —

```text
$ awk 'NR>=774 && NR<=928' dachdecker_pro_3d.tsx | grep -oE "this\.[a-zA-Z]+" | sort | uniq -c | sort -rn
  10 this.mats        10 this.gRafters     7 this.createBeam
   5 this.buildRoofFace   3 this.drawBeamBetweenPoints   2 this.gVisualTiles
$ awk 'NR>=774 && NR<=928' dachdecker_pro_3d.tsx | grep -o "THREE\." | wc -l
67
```

**Attrappen-Regel eingehalten:** alle sechs Attrappen **zeichnen nur auf**. `createBeam` legt
`{name, typ, posY}` ab und gibt das Objekt zurück, `buildRoofFace` legt `{id, origin, uMax, vMax,
poly}` ab, `drawBeamBetweenPoints` legt Start/Ende ab, `gVisualTiles.add` zählt. **Keine Attrappe
rechnet** — jede Zahl unten stammt aus `:774–928` selbst, nicht aus meinem Gerüst.

### 3.2 Rohausgabe des Laufs

```text
A12| Schnitt buildCompoundPitchedFaces = Quelle:774-928 · three.js aus dem Repo
A12| 2a FLAECHEN (4): [
 {"id":"main_N","origin":[6.5,2.331,-4.5],"uMax":13,"vMax":5.493,"poly":[[0,0],[13,0],[13,5.493],[0,5.493]]},
 {"id":"main_S","origin":[-6.5,2.331,4.5],"uMax":13,"vMax":5.493,"poly":[[0,5.493],[0,0],[8,0],[10.5,3.052],[13,0],[13,5.493]]},
 {"id":"ext_W","origin":[1.5,2.331,2],"uMax":6.5,"vMax":3.052,"poly":[[0,3.052],[2.5,0],[6.5,0],[6.5,3.052]]},
 {"id":"ext_E","origin":[6.5,2.331,8.5],"uMax":6.5,"vMax":3.052,"poly":[[0,0],[4,0],[6.5,3.052],[0,3.052]]}]
A12| 2b BALKEN createBeam (7): Fußpfette Nord · Fußpfette Süd Links · Fußpfette Süd Rechts ·
     Fußpfette Anbau West · Fußpfette Anbau Ost (alle posY=2.57) ·
     Firstpfette Main (posY=5.412) · Firstpfette Anbau (posY=4.011)   [typ jeweils 'pfette']
A12| 2c LINIEN drawBeamBetweenPoints (2):
     {"name":"Kehlsparren Links","typ":"kehlsparren","von":[1.5,2.29,4.5],"bis":[4,4.04,2]}
     {"name":"Gratsparren Rechts","typ":"gratsparren","von":[6.5,2.29,4.5],"bis":[4,4.04,2]}
A12| 2d Firstkappen (gVisualTiles): 2
```

**Das ist ein L-Dach mit benannten Flächen:** vier Flächen (`main_N`, `main_S`, `ext_W`, `ext_E`),
zwei Firstlinien (Haupt- und Anbaufirst, `:872` / `:883`), **eine Kehle und ein Grat**, beide als
Bauteil benannt (`:861` / `:868`). Die Notch-Fläche `main_S` trägt sechs Punkte mit der Spitze bei
(10.5, 3.052) — das ist der Anbaudurchdringungspunkt, nicht ein Rechteck.

### 3.3 Weg-2-Kontrolle (die Trigonometrie aus `:775–788` nachgerechnet)

```text
A12| 2e KONTROLLE Weg 2: uMaxMain=13==13 · slopeLen=5.493==5.493 · yEaveEdge=2.331==2.331
     yRidge(gerechnet)=5.482 == Firstpfette-y+0.07=5.482
```

Nachgerechnet mit den Konstanten aus dem Code (`:783` `kerve = 20/100·0,25`, `:784` `hPivot =
h + 0,14 + (0,1 − kerve)·cos α`, `:785` `yEaveEdge = hPivot − oh·tan α`, `:780` `slopeLen =
(W/2+oh)/cos α`, `:787` `uMaxMain = L + 2·ohG`, `:871` `yRidge = hPivot + (slopeLen − oh/cos α)·sin α`).
**Beide Wege stimmen überein.** Weg 1 und Weg 2 sind damit gefahren, nicht nur Weg 1.

### 3.4 Grenzfall WB = W = 8 bei den Flächen

```text
A12| 2f GRENZFALL WB=8 (=W): faces=4 vPeak=5.493 vMaxMain=5.493
     main_S-poly=[[0,5.493],[0,0],[4,0],[8.5,5.493],[13,0],[13,5.493]]
     (Spitze AUF dem First — Fremdcode baut, kein Wurf, keine Meldung)
```

Der Anbaufirst liegt exakt auf dem Hauptfirst (`vPeak = vMaxMain`); die Notch-Spitze berührt die
Firstkante. Der Fremdcode **baut trotzdem vier Flächen, wirft nicht und meldet nichts**. Zum
Verhalten der Insel an derselben Stelle siehe Abschnitt 4.

**Ergebnis A-12-2: Weg 1 ist NICHT gescheitert.** F-026s Flächenteil ist isolierbar und läuft — das
Blatt hatte den Ausgang „nicht isolierbar" ausdrücklich als zulässiges Ergebnis vorgesehen; er ist
nicht eingetreten.

---

## 4. A-12-3 — der Vergleich: dasselbe L durch die Insel

### 4.1 Nebeneinander

| | **F-026 (Fremdcode `:774–928`)** | **Insel (`geometry/` + `renderers/three-d/`)** |
|---|---|---|
| Zahl der Dachflächen | **4** (`main_N`, `main_S`, `ext_W`, `ext_E`) | **4** (`verschneidungsFlaechen`, dachVerschneidung.ts:185) |
| Flächen benannt | IDs, Klartext erst im UI (`:1141`) | **Klartext direkt**: Hauptdach Nord/Süd, Anbau West/Ost |
| Polygone | siehe 3.2 | **punktweise identisch** — `maxAbweichung = 0` |
| Firstlinien | 2 (Firstpfette Main `:872`, Anbau `:883`) + 2 Firstkappen | keine Linienobjekte; nur `firstHoeheMm = 5482` |
| Kehle / Grat | 2 **Sparren**: Kehlsparren links, Gratsparren rechts | 2 **Linien**: `art:'kehle'`/`art:'grat'`, je 3,945 m, 26,341° |
| Kantentypen benannt | `analyzeTopology`: 6× TRAUFE, joins 5 Grat / 1 Kehle / 0 Ortgang | keine Kantentypisierung |
| Grundfläche/Mesh | Shoelace über die 4 Polygone: **167,234 m²** | `dachMeshWelt`: 10 Dreiecke, **167,246 m²**, first 5482 mm |
| Kennwertpfad (`dachGeometrie.dachFlaechen`) | — | **wirft**: „Traufkontur ist nicht rechteckig — V1 unterstützt nur rechteckige Grundrisse" |
| Aufbauträger (`dachflaechen`) | — | **0** |
| Grenzfall WB = W | baut 4 Flächen, keine Meldung | `lTBauGueltig = false` → **0 Flächen**, Linien `pruefpflichtig = [true,true]` |

Rohausgaben:

```text
A12| 3a INSEL verschneidungsFlaechen: 4 Flächen [{"id":"main_N","name":"Hauptdach Nord","uMax":13,"vMax":5.493,...},
     {"id":"main_S","name":"Hauptdach Süd",...},{"id":"ext_W","name":"Anbau West",...},{"id":"ext_E","name":"Anbau Ost",...}]
A12| 3b VERGLEICH Flächenpolygone Fremdcode vs. Insel (4 ids, punktweise): maxAbweichung=0
A12| 3c INSEL verschneidungslinien: [{"art":"kehle","seite":"links","laenge3D":3.945,"neigungGrad":26.341,"pruefpflichtig":false},
     {"art":"grat","seite":"rechts","laenge3D":3.945,"neigungGrad":26.341,"pruefpflichtig":false}]
A12| 3d FREMD Sparrenlinien: [{"name":"Kehlsparren Links","typ":"kehlsparren"},{"name":"Gratsparren Rechts","typ":"gratsparren"}]
A12| 3e INSEL dachMeshWelt(l-shape + L-Kontur + anbau): dreiecke=10 firstHoeheMm=5482 flaechensummeM2=167.246
A12| 3f FREMD Flächensumme (Shoelace über die 4 poly): 167.234 m2
A12| 3g INSEL dachflaechen (Träger für Aufbauten): 0
A12| 3h INSEL dachGeometrie.dachFlaechen WIRFT: Traufkontur ist nicht rechteckig — V1 unterstützt nur rechteckige Grundrisse (kein stilles Falschdach).
A12| 3i GRENZE WB=W=8: Insel lTBauGueltig=false · Insel verschneidungsFlaechen=0 · Fremdcode baut 4 Flächen ohne Wurf
A12| 3j GRENZE WB=W=8: Insel verschneidungslinien pruefpflichtig=[true,true]
```

Die Insel-Eingabe war ein echter `RoofNode` mit L-Kontur **und** `anbau` (wie A-05 gemessen hat):
Polygon (0,0) → (12000,0) → (12000,4000) → (4000,4000) → (4000,8000) → (0,8000) in mm,
`roofType:'l-shape'`, 35°, `ueberstandMm:500`, `traufhoeheMm:2500`,
`anbau:{length:12000,width:8000,lengthB:4000,widthB:4000}`.

### 4.2 Warum die Abweichung 0 ist — und was das für die Wegentscheidung bedeutet

**Die Insel enthält bereits einen Port genau dieser Fremdfunktion.**
`resources/planner/hausplaner/geometry/dachVerschneidung.ts` sagt es in seinem eigenen Kopf:

- `:3` — *„Spiegelt EXAKT die Engine-Konstanten aus `buildCompoundPitchedFaces` (SSOT)"*
- `:138–142` — *„W-3b Teil 3: L/T-Verschneidungs-FLÄCHEN (Port-Abschluss). Byte-treue Spiegelung
  der EINGEBAUTEN Flächen aus `DachplanerProPage.buildCompoundPitchedFaces`"*

```text
$ git log --diff-filter=A --oneline -- resources/planner/hausplaner/geometry/dachVerschneidung.ts
588283df  2026-07-23  "W-1: Dach-Werte + Verschneidung (dachWerte/dachVerschneidung/dachUForm)
                       portiert (reine Reuse) + Tests (+31)"
$ # Renderer-Anschluss
f0d02f45  2026-07-24  "W-3b Teil 3: L/T-Verschneidungsflaechen — Port-Abschluss (l/t rendern real statt leer)"
```

Angeschlossen ist er produktiv: `dachMesh.ts:17` importiert `verschneidungsFlaechen`/`lTBauGueltig`,
`dachMesh.ts:215–217` leitet `istVerschneidungsForm` daran vorbei an der Rechteckprüfung,
`dachMesh.ts:152–155` setzt `form` und ruft den Port; von dort geht es in `renderers/three-d/szene.ts`
(`:35`, `:520`, `:572`). 23 vorhandene Tests decken ihn ab (`dachVerschneidung.test.ts` 11,
`dachVerschneidungFlaechen.test.ts` 6, `verschneidungRender.test.ts` 6).

**Das heißt für den Planner:** die Frage „F-026 bauen?" ist für den **Flächenteil** keine Bau-, sondern
eine Anschlussfrage — er ist gebaut, produktiv verdrahtet und mit Abweichung 0 zum Original belegt.
Offen sind die drei Lücken, die die Messung zeigt: der **Kennwertpfad** (`dachGeometrie.dachFlaechen`
wirft bei L, `dachflaechen` = 0), die **Linien** (`verschneidungslinien` ist gebaut, aber produktiv
nirgends benutzt — nur in Tests) und der **Anlegepfad** (A-05: `roof.anbau` wird nie gesetzt).
*Ich stelle das nebeneinander; die Wegentscheidung gehört dem Planner (Nicht-Ziel des Blatts).*

---

## 5. A-12-5 — must_preserve, Wegwerf-Probe, Sauberkeit

**Die Probe ist entfernt, bevor dieser Bericht geschrieben wurde:**

```text
$ ls resources/planner/hausplaner/__tests__/ | grep -i "zz\|wegwerf\|a12"
kein Treffer (Probe geloescht)
$ git log --all --oneline -- '*zzA12wegwerf*' | wc -l
0        # kein Commit im ganzen Repo trägt sie
```

*Die Probe wurde vor dem Löschen außerhalb des Repos gesichert
(`…/scratchpad/zzA12wegwerf.test.ts.sicherung`) — Dauerregel „Original erhalten"; im Repo ist sie
restlos weg und war nie in einem Commit.*

**Suite, beide Stände gemessen** (`npm run test:hausplaner`):

```text
mit Probe:   ℹ tests 1695 · pass 1695 · fail 0     <- belegt zugleich den Plan-Prüfer-Befund
ohne Probe:  ℹ tests 1692 · pass 1692 · fail 0     <- Sollzähler wiederhergestellt
```

**must_preserve in ALLEN DREI RICHTUNGEN** — Auflage aus `239a163e`, die ich als frische Instanz
übernehme (die alte Einweg-Messung hätte die Probe nicht gesehen und wäre grün gewesen):

```text
$ git diff --name-only HEAD -- resources/ scripts/ | wc -l                      # geändert
0
$ git ls-files --others --exclude-standard -- resources/ scripts/ | wc -l       # hinzugefügt
0
$ git diff --name-only --diff-filter=D HEAD -- resources/ scripts/ | wc -l      # entfernt
0
```

*Nicht angefasst, weil fremd: die Streudatei `1692` im Wurzelverzeichnis und `zz-unlink-probe`
(beide von anderen Vorgängen, in `239a163e` bzw. bei A-08 gemeldet).*

---

## 6. A-12-4 — Ampel-VORSCHLAG (der Generator schlägt vor, er setzt nicht)

### **Vorschlag: 🟢 — mit einer Wortlaut-Korrektur am Formelblatt**

**Begründung am Kriterium des Blatts** („🟢 wenn ein L-Dach mit benannten Flächen herauskommt"):
Es kam ein L-Dach mit **vier benannten Flächen**, **zwei Firstlinien**, **einer benannten Kehle** und
**einem benannten Grat** heraus (Abschnitt 3.2), gerechnet, nicht gelesen. Die Konturseite trägt
ebenfalls (Abschnitt 2). Damit ist weder 🔴 („kommt nicht heraus") noch „bleibt 🟡" („nur Teil A
trägt") zutreffend.

**Die Korrektur, die zur grünen Ampel gehört — der Sperrgrund verschiebt sich, er verschwindet nicht:**

> **BEFUND A-12-2/a: Die Flächenrechnung benutzt die Kantentopologie nicht.**
> Das Formelblatt beschreibt F-026 als sechsschrittiges Kantentopologie-Verfahren
> (`FORMELSAMMLUNG.md:352–364`: Kontur → Kantentypen → Neigung je Kante → Eckklassifikation →
> „Flächen aus den typisierten Kanten aufbauen"). **Gemessen wird das nicht durchlaufen:**
>
> ```text
> $ awk 'NR>=774 && NR<=928' dachdecker_pro_3d.tsx \
>     | grep -cE "buildTopologyPolygon|getDefaultEdgeTopologyConfigs|analyzeTopology|edgeConfig|TopologyPoint"
> 0
> $ grep -n "analyzeTopology\|buildTopologyPolygon\|getDefaultEdgeTopologyConfigs" dachdecker_pro_3d.tsx
> 95: / 123: / 134:   (Definitionen)
> 1496: · 1558: · 1560: · 1706:   (ALLE Aufrufe stehen in der React-Komponente)
> ```
>
> Die Topologie-Kette ist **UI-Anzeige**; das Dach entsteht in `:1137 → buildCompoundPitched (:965)
> → buildCompoundPitchedFaces (:774)` allein aus den Zahlen `L, W, LB, WB, pitch, overhang`.
> **Was läuft, ist fest verdrahtete Parametergeometrie für L und T — kein Kantentopologie-Verfahren.**
>
> **Die beiden Teile widersprechen sich sogar für dieselbe Eingabe.** Die Kontur legt den
> L-Schenkel nach links (x ∈ [−6, 2], Rohausgabe 1b); die gebaute Geometrie legt ihn nach rechts:
> `:807` `cx = L/2 − W_b/2 = 4`, damit Anbau x ∈ [2, 6] (belegt durch `ext_W.origin.x = 1,5 =
> cx − W_b/2 − oh` und `ext_E.origin.x = 6,5 = cx + W_b/2 + oh`, Rohausgabe 2a).
>
> **Und die Kantentypen für l-shape gibt es gar nicht.** `getDefaultEdgeTopologyConfigs` kennt nur
> `pult`, `walm`, `sattel` und `flat` (`:127–130`); `l-shape` fällt in den Sammelrückgabewert
> `:131` „alles TRAUFE". Daraus folgt die Klassifikation `grate=5, kehlen=1, ortgaenge=0`
> (Rohausgabe 1d) — während das **tatsächlich gebaute** Dach 1 Kehle, 1 Grat und zwei Giebel hat.
> Die Zeile *„Grat/Kehle/Ortgang benannt: fertig benannt"* (`FORMELSAMMLUNG.md:377`) trifft für
> l-shape nicht zu.

**Was ich damit vorschlage:** 🟢 für das, was die Ampel wörtlich sperrt (*„bis ein L-Grundriss
gerechnet und das Ergebnis gesehen wurde"* — das ist erfüllt), **und** eine Berichtigung der
Verfahrensbeschreibung in `FORMELSAMMLUNG.md:352–364` und der Vergleichszeile `:377`, weil ein
künftiger Auftrag sonst ein Verfahren zitiert, das der Code nicht ausführt. *Formulierung und
Eintragung gehören dem Planner; der Evaluator bestätigt zuerst.*

---

## 7. Die zwei Scope-Dateien — bewusst NICHT geändert

`FORMELSAMMLUNG.md` und `VORGEHEN.md` stehen im Scope des Blatts, ich habe **an beiden nichts
geändert**. Begründung:

- **Die Ampel setzt der Planner.** A-12-4 sagt wörtlich: *„Der Generator SCHLÄGT VOR, der Evaluator
  bestätigt, der Planner trägt ein."* Ein Eintrag von mir wäre eine Selbstabnahme.
- **„Schritt 3 als erledigt" ist eine Statusaussage.** Erledigt ist A-12 nach §9/§16 erst mit dem
  Evaluator-Votum, und der Statusträger ist `docs/STATUS.md` — nicht `VORGEHEN.md`. Ich würde eine
  zweite Statuswahrheit anlegen und sie vor der Abnahme auf grün stellen.
- Beide Dateien sind unverändert gegenüber HEAD (Abschnitt 1, content-diff → IDENTISCH), stehen
  also für Planner und Evaluator sauber bereit.

---

## 8. Offene Fragen und Nebenbefunde (nicht behoben — außerhalb meines Scopes)

1. **Die Zeilenangabe im Insel-Port trifft nicht.** `dachVerschneidung.ts:139` nennt als Quelle
   „`DachplanerProPage.buildCompoundPitchedFaces` (Z.1170 ff.)". Gemessen steht die Funktion in
   `dachdecker_pro_3d.tsx:774`, in `dachdecker_pro.tsx:1727` und in `profi_holzbau_solar_cad.tsx:639`
   — **bei 1170 in keiner der drei**; `:1170` von `dachdecker_pro_3d.tsx` ist eine Satellitenschüssel.
   Die *Rechnung* des Ports ist trotzdem korrekt (Abweichung 0). `resources/**` ist Nicht-Ziel,
   deshalb nur gemeldet.
2. **Drei Fremdfassungen derselben Funktion** liegen auf dem Schreibtisch; `dachdecker_pro.tsx:1727`
   führt zusätzlich einen Typ `'I'`. Welche Fassung Leitquelle ist, entscheidet nicht der Generator.
3. **three.js-Version des Fremdcodes nicht feststellbar** (Kantenliste verlangt „melden, nicht
   anpassen"): der Ordner enthält **kein** `package.json`, die Datei importiert nur
   `import * as THREE from 'three'` (`:2`). Repo-`three` `^0.163.0` (`package.json:54`) hat
   ausgereicht; angepasst wurde nichts.
4. **Fehlende Grenzwertprüfung im Fremdcode** bei `WB ≥ W` (Abschnitt 2 und 3.4) — die Insel hat
   sie mit `lTBauGueltig` (`dachVerschneidung.ts:158`), die Quelle nicht.
5. **Keine Aussage zu F-050/F-051** — Nicht-Ziel, unberührt.

---

## 9. E1 — Commit-Messung (Aussagen über den Bau werden am COMMIT gemessen)

*Pflicht vor `CODE_FERTIG`, von Yamas Vertretung am 10.08. angenommen. Die Klasse, die sie verhindert:
„grün gemeldet, was nicht im Commit steht" (`5c06f5ca`) — eine Gegenprobe, die den Arbeitsbaum misst,
ist blind für genau den Unterschied, auf den es ankommt.*

**Berührte Dateien: zwei.** `docs/BERICHT-A-12-f026.md` (dieser Bericht) und `docs/STATUS.md`
(Zustandswechsel, eigener Commit). Kein Produktiv-, Test- oder Regelpfad berührt.

```text
$ git show HEAD:docs/BERICHT-A-12-f026.md | diff - docs/BERICHT-A-12-f026.md
(keine Ausgabe, exit 0)   -> Baum == Commit 92310844

$ git show HEAD:docs/STATUS.md | diff - docs/STATUS.md
(wird unmittelbar nach dem Statuscommit gefahren; Ergebnis geht mit den SHAs an den
 Release-Prüfer und ist an jedem Stand nachfahrbar)
```

**Zusätzlich gegen den Mess-SHA gemessen, nicht nur gegen HEAD** — HEAD ist während des Laufs
viermal gewandert (fremde Planner-/Plan-Prüfer-Commits `23839610`, `c9325929`, `239a163e`,
`601aff5c`, `95fe1b88`):

```text
$ git diff --name-only 239a163e..92310844 -- resources/ scripts/ | wc -l
0        # kein fremder Commit hat gemessenen Code bewegt; alle Zahlen oben bleiben gültig
```

**Prüf-SHA für den Evaluator: `92310844`** (Bericht) zuzüglich des Statuscommits, der ihn nennt.

---

## 10. §11-Kurzstand

```yaml
auftrag: A-12
basis: d1d716c8
mess_sha: 239a163e
commit: 92310844
scope: [docs/BERICHT-A-12-f026.md, docs/STATUS.md]
tests:
  statisch: nicht_anwendbar
  unit: "1692/1692"
  backend: nicht_anwendbar
  schema: pass
  build: nicht_anwendbar
  browser: nicht_anwendbar
abweichungen:
  - "FORMELSAMMLUNG.md und VORGEHEN.md im Scope, bewusst NICHT geändert (Abschnitt 7) — die Ampel
     trägt der Planner ein, 'Schritt 3 erledigt' wäre eine zweite Statuswahrheit vor der Abnahme."
  - "Wegwerf-Probe vom Vorgänger übernommen statt neu geschrieben; gelesen, gegen die Attrappen-Regel
     geprüft, gefahren, restlos entfernt."
offene_akzeptanz:
  - "A-12-4: die Ampel selbst ist nicht gesetzt — das ist so gewollt (Evaluator bestätigt, Planner trägt ein)."
```

