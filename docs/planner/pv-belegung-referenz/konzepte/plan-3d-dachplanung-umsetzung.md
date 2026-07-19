# Umsetzungsplan — 3D-Dachplanung (Geometrie zuerst härten)

**Status: PLAN zur Freigabe. KEINE Implementierung vor Freigabe.**
Grundlage: `vorgabe-3d-dachplanung.md` + Fachprüfberichte 2026-06-12
(`pruefbericht-zimmermannmeister-…`, `…dachdeckermeister-…`, `…pv-installateur-…`).
Fokus **ausschließlich 3D-Dachplanung**. KEIN Produktdatenbank-/Import-/Herstellercode.
**Kein großer Rewrite** — `roofModel.ts` (Kern), `RoofScene3D.tsx` (Renderer),
`PvPlanungPage.tsx` (UI) bleiben; alle Änderungen additiv.

---

## 1. Analyse Ist-Zustand (verifiziert + Fachprüfer)

**Bausteine:** `roofModel.ts` (RoofModel, deriveRoofGeometry, validateRoof, deriveBauteilliste,
Vorlagen) · `RoofScene3D.tsx` (Three.js, Schicht-Layer) · `PvPlanungPage.tsx` (Vorlagen, Eingaben,
Toggles, Kennzahlen, Bauteilliste). Build aktuell grün.

**Bestätigte Schwächen (Fachprüfer + Vorgabe §3):**
- **Geometrie:** Walm/Krüppelwalm = **degenerierte Quads mit Doppeleckpunkt** (`roofModel.ts:188-189`), **kein Gratsparren**, beide Typen identisch behandelt (Zimmermann, HOCH). Nur **eine** PV-Hauptfläche; Nordseite/mehrere Flächen nicht belegbar.
- **Eingaben:** **Firsthöhe nicht eingebbar** (nur Neigung führend); Überstand nur Traufe/Ortgang gemeinsam, **nicht** vorne/hinten bzw. links/rechts getrennt.
- **Störflächen:** `RoofObject` nur `u/v/breite/höhe` — **keine Sperrfläche, kein Sicherheitsabstand, keine Verschattung**, Module werden nicht ausgespart (PV, HOCH).
- **Unterkonstruktion:** eindeckungsunabhängig, „2 Dachhaken/Modul" pauschal (Dachdecker/PV, HOCH).
- **Modulmaße hartcodiert** 1,134×1,8 m (PV, HOCH) → kWp/Belegung unzuverlässig.
- **Eindeckung** nur 3 statt 8 Typen; Flachdach-Vorlage fälschlich „trapezblech" (Dachdecker, HOCH).
- **Keine Geometrietests** für `deriveRoofGeometry`.

(Produktbezogene/normbezogene Befunde — Regeldachneigung, Wind-/Schneelast, Modulmaße aus
Hersteller — gehören in die **artikelbasierte** Spur und sind hier nur Schnittstelle, keine Umsetzung.)

---

## 2. Datenmodell-Erweiterung (additiv in `roofModel.ts`)

> Bestehende Typen/Exports bleiben; neue Felder optional, damit nichts bricht.

```
RoofPlane {                      // robust, je Fläche vollständig
  id; label; rolle:"haupt"|"nebendach"|"walm"|"gaube";
  corners: Vec3[];              // 3–4 echte Eckpunkte (kein Doppelpunkt)
  normal: Vec3; flaeche: number;
  edges: Array<{ typ:"traufe"|"ortgang"|"first"|"grat"|"kehle"; a:Vec3; b:Vec3 }>;
  pvZone?: { origin:Vec3; uDir:Vec3; vDir:Vec3; widthU:number; lengthV:number; randAbstand:Rand };
  belegbar: boolean;
}
RoofGeometry {
  planes: RoofPlane[];          // MEHRERE belegbare Flächen
  eaveHeight; ridgeHeight; firsthoehe; rafterLengths: Record<planeId,number>;
  footprint; warnings: CheckResult[];
}
Rand { traufe; first; ortgang; grat; kehle }   // getrennte Randabstände
```

**Getrennte Überstände** (ersetzt das gemeinsame Feld additiv):
```
ueberstand: { traufeVorne; traufeHinten; ortgangLinks; ortgangRechts }
```
**Firsthöhe alternativ:** `hoeheModus:"neigung"|"firsthoehe"`; bei `firsthoehe` wird die
Neigung daraus abgeleitet (und umgekehrt) — beide konsistent gehalten, Plausibilitätswarnung
bei Widerspruch.

**Störflächen (echte Objekte):**
```
RoofObstacle {
  id; type:"gaube"|"dachfenster"|"schornstein"|"dachluke"|"sanitaerluefter"
        |"dunstrohr"|"satschuessel"|"antenne"|"schneefang"|"solarthermie";
  planeId; u; v; breite; hoehe; hoeheUeberDach;
  sicherheitsabstand; sperrflaeche: Polygon2D (abgeleitet); verschattungsprofil?;
}
```

**Modulfelder je Dachfläche:**
```
ModuleField {
  id; planeId; modulRefArticleId?; modulMass:{b;h};  // Maße aus Artikel-Spec (sonst Default + Warnung)
  ausrichtung:"hoch"|"quer"; reihen; spalten; ursprungU; ursprungV; abstaende;
}
PvBelegung { felder: ModuleField[]; pruef: CheckResult[] }  // Kollisionen/Sperrflächen
```

**Walm/Krüppelwalm korrekt:** echte Gratkanten + Walmflächen (Trapez/Dreieck) statt
degenerierter Quads; Krüppelwalm mit Teil-Gable unterhalb des Walms. Gratsparren in `edges`.

---

## 2b. Pflichtanforderung — Vollwertiges Dach auf ALLEN Flächen (Inhaber, 2026-06-12)

**Der Dachstuhl (Tragwerk) und der komplette Dachaufbau gehören IMMER auf JEDE Dachfläche —
nicht nur dort, wo PV-Module liegen.** Heute baut die Szene Sparren/Pfetten/Lattung/Dämmung/
Bahn/Eindeckung nur auf der PV-Hauptfläche; Nord-/Walm-/Nebenflächen sind nur dünne Quads.

Soll:
- Sparren, Pfetten, Kehlbalken, Dämmung, Unterspannbahn, Konter-/Eindecklattung, Eindeckung,
  First-/Grat-/Traufdetails auf **allen** `RoofPlane` (Süd, Nord, Walm, Gaube), durchgehend
  und bündig (gemeinsamer First, durchlaufende Pfetten über beide Schrägen).
- Tragwerk ist **immer sichtbar standardmäßig** (nicht an die PV-Fläche gekoppelt); PV-Module/
  Montage liegen zusätzlich nur auf den belegten Flächen.
- Konsequenz fürs Datenmodell: Konstruktion wird je `RoofPlane` (bzw. am Gesamttragwerk)
  geführt, nicht nur an der einen `pvZone`.

## 2c. Pflichtanforderung — Jede Dachfläche bearbeit- und belegbar (Inhaber, 2026-06-12)

**Alle Dachflächen müssen auswählbar, bearbeitbar und mit PV belegbar sein** — nicht nur die
Süd-/Hauptfläche. Heute trägt nur eine Fläche eine `pvZone`.

Soll:
- Jede `RoofPlane` ist im 3D/in der UI **anwählbar** (Klick/Selektion) und einzeln editierbar
  (eigene Maße/Eindeckung soweit sinnvoll, eigene Randabstände).
- **PV-Belegung je Fläche**: eigene `ModuleField`(er) pro `RoofPlane` mit eigener Ausrichtung/
  Neigung → **eigener Teilertrag je Fläche**; Gesamtanlage = Summe über alle belegten Flächen
  (verknüpft mit Workflow §9 „mehrere Dächer/Dachflächen zusammenführen").
- Belegung/Modulfelder je Fläche **hinzufügen, kopieren, verschieben, entfernen**; Kollisions-/
  Sperrflächenprüfung gilt je Fläche.
- Kennzahlen (Module, kWp, Ertrag) je Fläche **und** als Summe.

## 3. Renderer-Erweiterung (additiv in `RoofScene3D.tsx`)

- Über **alle** `planes` iterieren und auf **jeder** Fläche den **vollständigen** Dachstuhl +
  Dachaufbau bauen (s. §2b) — PV-Module/Montage nur auf belegten Flächen.
- Neue **Layer-Gruppen**: `Maßketten` (Firsthöhe/Traufhöhe/Sparrenlänge/Überstände/Modulabstände als Linien+Labels), `Sperrflächen` (Sicherheitszonen, halbtransparent rot), `Verschattung` (Schattenpolygone je Störer), `Prüfhinweise` (Kollisions-Highlight), `UK detailliert` (Haken/Halter/Schienen/Klemmen/Verbinder/Kabelwege).
- Störflächen 3D inkl. Sperrzone; Module in Sperrzone werden **ausgespart**/markiert.
- Unterkonstruktion **eindeckungsabhängig** darstellen (Ziegelhaken/Stockschraube/Falzklemme/Aufständerung) — **konfigurierbare Vorstufe mit Warnhinweis**, solange keine Artikel-/Herstellerdaten (kein Hartcodieren von Herstellerregeln).
- Echte Modulmaße aus `modulMass` (später Artikel-Spec) statt fix.

---

## 4. UI-Erweiterung (additiv in `PvPlanungPage.tsx`)

- Eingabe **Firsthöhe ODER Neigung** (Umschalter); 4 getrennte Überstandsfelder.
- **Dachflächen-Auswahl**: jede Fläche anklick-/auswählbar, einzeln bearbeitbar und belegbar (§2c); aktive Fläche hervorgehoben. **Modulfeld-Werkzeuge je Fläche**: hinzufügen, kopieren/duplizieren, verschieben, entfernen. Kennzahlen je Fläche + Gesamtsumme.
- **Störflächen-Liste**: Typ/Position/Maße/Sicherheitsabstand, hinzufügen/duplizieren.
- Layer-Toggles um die neuen Gruppen erweitern (Maßketten/Sperrflächen/Verschattung/Prüfhinweise).
- **Prüfhinweis-Panel**: Kollisionen (Modul außerhalb Fläche, in Sperrfläche, Haken nicht auf Sparren, Schiene ohne ausreichend Haken), Plausibilität, „Herstellerfreigabe offen".

---

## 5. Prüf-/Testmatrix (Tests ZUERST, vor sichtbarer Erweiterung)

| Test | Inhalt |
|---|---|
| Unit `deriveRoofGeometry` je Dachform | Eckpunkte nicht-degeneriert, Flächen>0, First-/Traufhöhe, rafterLength, Anzahl belegbarer Flächen (Walm=4, Sattel=2, Pult/Flach=1) |
| Unit Überstände | getrennte Überstände wirken korrekt auf Eckpunkte/Höhen |
| Unit Firsthöhe↔Neigung | beide Modi konsistent (Hin-/Rückrechnung) |
| Unit PV-Nutzfläche | Modulfläche liegt innerhalb nutzbarer Zone, nicht auf Überstand, nicht in Sperrfläche |
| Unit Plausibilität | Überstand/Firsthöhe/Neigung/Sparrenlänge-Warnungen |
| Build/Regression | TSC + `vite build` grün; bestehende Seiten unverändert |
| Visuelle Abnahme | Desktop+mobil; Sattel/Sattel-Gaube/Walm/Pult/Flach; Layer-Toggles; Module nicht auf Überstand/Störer |

**Testframework:** Vitest additiv einführen (nur falls noch nicht vorhanden) — reine
Funktionstests gegen `roofModel.ts`, kein Three.js im Test.

---

## 6. Umsetzungsphasen (minimal-invasiv, Reihenfolge lt. Vorgabe §5)

- **P-1 Tests + Geometrie härten:** Vitest, RoofPlane/RoofGeometry robust, Walm/Krüppelwalm echte Grate, getrennte Überstände, Firsthöhe-Modus. (Kein neues Visual.)
- **P-2 Mehrere belegbare Flächen + nutzbare PV-Zonen.**
- **P-3 Störflächenmodell mit Sperrflächen** (Datenmodell + Aussparung).
- **P-4 Layer Sperrflächen/Maßketten/Prüfhinweise** im Renderer.
- **P-5 Modulfelder auf mehreren Flächen** (kopieren/verschieben/entfernen, Kollisionen).
- **P-6 Unterkonstruktion nach Eindeckung unterscheiden** (Vorstufe + Warnhinweis).
- **P-7 Verschattung als einfache Schattenpolygone.**
- **P-8 (separate Spur):** Anbindung an **artikelbasierte** Produktdaten (Modulmaße/UK aus `article_technical_specs`) — erst nach Freigabe der Artikel-Spur.

Jede Phase: Tests grün, Build grün, kurze visuelle Abnahme, dann nächste Phase.

---

## 7. Regeln
- Additiv, kein Rewrite; bestehende Exporte/Seiten erhalten.
- Keine Herstellerdaten erfinden; UK/Eindeckungsregeln nur als **konfigurierbare Vorstufe mit Warnhinweis**, final erst über die Artikel-/Produktspur.
- „Skizze, keine Statik — Nachweis durch Tragwerksplaner/Fachbetrieb"-Disclaimer sichtbar halten (Zimmermann-Empfehlung).
- AGENTS.md: additiv, Build grün vor Commit, menschliche Freigabe je Phase.

---
*Erstellt 2026-06-12 · Fokus 3D-Dachplanung · Umsetzung gesperrt bis Freigabe.*
