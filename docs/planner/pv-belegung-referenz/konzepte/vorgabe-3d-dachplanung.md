# Vorgabe — 3D-Dachplanung PV

**Status: Vorgabe zur Freigabe. Keine Implementierung ohne Freigabe.**

Diese Vorgabe fokussiert ausschließlich die 3D-Dachplanung. Produktdatenbank, Excel-Import und Herstellerkataloge sind eigene Spuren und dürfen die 3D-Grundlogik nicht überlagern. Ziel ist eine fachlich nachvollziehbare 3D-Planung für Zimmermannmeister, Dachdeckermeister und PV-Installateur.

## 1. Aktueller Zustand

Die 3D-Dachplanung ist aktuell in drei Hauptbausteine getrennt:

- `src/utils/roofModel.ts`: fachliches Dachmodell, Geometrieableitung, Plausibilitätswarnungen, Bauteilliste, Systemvorlagen.
- `src/pages/energie/RoofScene3D.tsx`: Three.js-Darstellung mit Dachflächen, Tragwerk, Dachaufbau, Eindeckung, PV-Unterkonstruktion, Modulen und Dachdetails.
- `src/pages/energie/PvPlanungPage.tsx`: UI mit Vorlagenleiste, Eingaben, Layer-Toggles, Kennzahlen und Bauteilliste.

Vorhanden:

- Dachformen: Satteldach, Pultdach, Walmdach, Krüppelwalmdach, Flachdach.
- Dachüberstand: Traufe und Ortgang sind im Modell vorhanden.
- Layer-Gruppen: Tragwerk, Dachaufbau, Montage, Details.
- 3D-Darstellung: Wände, Dachflächen, Giebel/Walmflächen, Sparren, Pfetten, Kehlbalken, Dämmung, Unterspannbahn, Konterlattung, Eindecklattung, Eindeckung, Firstziegel, Dachrinne, Ortgang, Schneefang, Dachhaken, Schienen, Module, Kabelweg.
- Aufbauten: Gaube, Dachfenster, Schornstein als einfache Modellobjekte.
- Vorlagenleiste: Systemvorlagen für mehrere Dachformen.
- Grobe Bauteilliste: Dachfläche, Sparren, Latten, Unterspannbahn, Dämmung, Eindeckung, First, PV-Module, Dachhaken.

## 2. Fachliche Bewertung

### Zimmermannmeister

Gut:

- Sparren, Pfetten, Kehlbalken und Überstände sind sichtbar.
- Sparrenabstand und Querschnitte sind als Eingaben vorhanden.
- Dachform und Grundmaße sind modellgetrieben.

Fehlt:

- Firsthöhe als alternative führende Eingabe.
- getrennte Überstände für Traufe vorne/hinten und Ortgang links/rechts.
- Auflagerpunkte, Fußpfette/Mittelpfette/Firstpfette als echte Bauteile mit Position.
- Wechsel/Zangen/Auswechslungen um Dachfenster, Gauben und Schornstein.
- korrekte Grat-/Kehlsparren bei Walm/Krüppelwalm.
- Sparrenlängen, Ausklinkungen und Maßketten.

### Dachdeckermeister

Gut:

- Dachaufbau-Schichten sind sichtbar ein-/ausblendbar.
- Eindeckung, First, Ortgang und Rinne sind als visuelle Ebenen vorhanden.
- Lattenabstand und Eindecklattung sind einstellbar.

Fehlt:

- Deckmaß, Deckbreite, Überdeckung und Regeldachneigung je konkreter Eindeckung.
- realistische Darstellung von Ziegelreihen statt einfacher Flächen/Boxreihen.
- Traufdetail mit Traufblech, Rinnenhalter, Lüftungsebene.
- Ortgangdetail mit Ortgangziegel/Blech/Ortgangbrett.
- Kehle, Grat, Wandanschluss, Kaminanschluss.
- Dachfenster-/Gaubenanschluss als dachdeckerrelevantes Detail.

### PV-Installateur

Gut:

- Module, Schienen, Dachhaken und Kabelweg sind sichtbar.
- Randabstand wird bei der Modulposition berücksichtigt.
- Überstand bleibt für Modulbelegung frei.

Fehlt:

- Unterkonstruktion abhängig von Dachform und Eindeckung.
- Dachhakenposition auf tatsächlichen Sparren.
- Endklemmen, Mittelklemmen, Schienenverbinder, Kragarme.
- Störflächen mit Sperrzone und Mindestabstand.
- Verschattung durch Schornstein, Gaube, Dachfenster, SAT, Lüfter.
- mehrere Modulfelder.
- Kopieren/Duplizieren/Verschieben von Modulen, Modulfeldern und Dachaufbauten.
- Kollisionsprüfung: Modul außerhalb Dachfläche, Modul in Sperrfläche, Haken nicht auf Sparren, Schiene ohne ausreichend Haken.

## 3. Zentrale Schwächen im aktuellen 3D-Modell

1. Die 3D-Szene baut fast alles auf nur einer PV-Hauptfläche auf. Für Walmdach, Krüppelwalm, Nordseite oder mehrere Dachflächen ist das zu grob.
2. Dachobjekte haben nur `u/v`, Breite und Höhe. Es fehlen Sperrflächen, Sicherheitsabstände, Höhe über Dach, Anschlussart und Verschattung.
3. Die Unterkonstruktion ist visuell immer ähnlich. Sie unterscheidet nicht sauber zwischen Ziegel, Trapezblech, Stehfalz, Flachdach oder Solarhalteziegel.
4. Die Layer sind sichtbar, aber noch nicht vollständig fachlich steuerbar. Es fehlen Maßketten, Schatten, Sperrflächen und Prüfhinweise als eigene Layer.
5. Die Bauteilliste ist ein Richtwert und noch nicht produkt-/regelbasiert.
6. Es gibt keine 3D-/Geometrietests für `deriveRoofGeometry`.

## 4. Vorgabe für die nächste 3D-Planungsphase

### 4.1 Kein großer Rewrite

Die vorhandene Struktur bleibt erhalten:

- `roofModel.ts` bleibt fachlicher Kern.
- `RoofScene3D.tsx` bleibt Renderer.
- `PvPlanungPage.tsx` bleibt Bedienoberfläche.

Änderungen sind additiv vorzunehmen. Keine komplette Neuschreibung der 3D-Seite ohne gesonderte Freigabe.

### 4.2 Geometrie zuerst härten

Vor neuen visuellen Details muss die Geometrie fachlich sauberer werden:

- `RoofPlane` muss für jede Dachfläche vollständige Daten liefern: Fläche, Kanten, Normalen, Typ, nutzbare PV-Zone.
- Dachflächen dürfen nicht nur ein `pv`-Hauptfeld haben. Es braucht mehrere belegbare Flächen.
- Überstände müssen getrennt modellierbar werden:
  - Traufe vorne
  - Traufe hinten
  - Ortgang links
  - Ortgang rechts
- Firsthöhe muss alternativ zur Dachneigung führend sein.
- Walm/Krüppelwalm braucht echte Gratkanten und Walmflächen, keine degenerierten Quads.

### 4.3 Störflächen als echte 3D-Objekte

`RoofObject` muss erweitert werden zu fachlichen Störflächen:

- Typ: Gaube, Dachfenster, Schornstein, Dachluke, Sanitärlüfter, Dunstrohr, SAT-Schüssel, Antenne, Schneefang, Solarthermie.
- Zuordnung zu Dachfläche.
- Position auf Dachfläche.
- Maße.
- Sicherheitsabstand.
- Sperrfläche als abgeleitetes Polygon.
- Höhe/Aufbau über Dach.
- Verschattungsprofil.

Die 3D-Ansicht muss Sperrflächen sichtbar machen können.

### 4.4 PV-Belegung als editierbare Modulfelder

Die Belegung darf nicht nur aus Reihen/Spalten auf einer Fläche bestehen. Erforderlich:

- mehrere Modulfelder je Dachfläche.
- Modulgruppe kopieren/duplizieren.
- Modulgruppe verschieben.
- Modul entfernen/auslassen.
- Kollisionen sichtbar markieren.
- Sperrflächen automatisch freihalten.
- Randabstände zu Traufe, First, Ortgang, Grat, Kehle und Störflächen getrennt führen.

### 4.5 Unterkonstruktion in 3D fachlich unterscheiden

Die Darstellung muss je Eindeckung anders wirken und anders geprüft werden:

- Ziegeldach: Dachhaken oder Solarhalteziegel auf Sparrenlage.
- Trapezblech: Kurzschiene/Klemme/Stockschraube abhängig vom Profil.
- Stehfalz: Falzklemmen.
- Flachdach: Aufständerung mit Neigungsdreiecken, Ballast und Wartungsgängen.
- Schiefer: Sonderhaken/Ersatzplatten als geprüfte Sonderlösung.

Bis Herstellerdaten vorhanden sind, darf dies nur als konfigurierbare Vorstufe mit Warnhinweis dargestellt werden.

### 4.6 Layer erweitern

Neue Layer-Gruppen:

- Maßketten: Firsthöhe, Traufhöhe, Sparrenlänge, Überstände, Modulabstände.
- Sperrflächen: Sicherheitszonen um Störer.
- Verschattung: Schattenflächen je Störer.
- Prüfhinweise: Kollisionen, Warnungen, fehlende Herstellerfreigabe.
- PV-Unterkonstruktion detailliert: Haken/Halter, Schienen, Klemmen, Verbinder, Kabelwege.

### 4.7 Tests und Abnahme

Vor sichtbarer Erweiterung:

- Unit-Tests für `deriveRoofGeometry` je Dachform.
- Plausibilitätsprüfung für Überstände, Firsthöhe, Neigung, Sparrenlänge.
- Tests für Modulfläche innerhalb nutzbarer Dachfläche.
- Build muss grün bleiben.

Visuelle Abnahme:

- Desktop und mobil prüfen.
- Satteldach, Satteldach mit Gaube, Walmdach, Pultdach, Flachdach prüfen.
- Layer-Toggles prüfen.
- Modulbelegung darf nicht auf Überstand oder Störfläche liegen.

## 5. Empfohlene Reihenfolge

1. **3D-Geometrietests und Dachflächenmodell härten.**
2. **Mehrere Dachflächen und nutzbare PV-Zonen vorbereiten.**
3. **Störflächenmodell mit Sperrflächen einführen.**
4. **3D-Layer für Sperrflächen, Maßketten und Prüfhinweise ergänzen.**
5. **Modulfelder auf mehreren Dachflächen ermöglichen.**
6. **Unterkonstruktionsdarstellung nach Eindeckung unterscheiden.**
7. **Verschattung als einfache Schattenpolygone ergänzen.**
8. **Erst danach Produktdatenbank und Herstellerdaten mit der 3D-Auswahl verbinden.**

## 6. Arbeitsauftrag an Claude Code oder Frontend-Agent

```
Fokus ausschließlich 3D-Dachplanung.

Bitte analysiere den aktuellen Stand in:
- src/utils/roofModel.ts
- src/pages/energie/RoofScene3D.tsx
- src/pages/energie/PvPlanungPage.tsx

Arbeite nicht an Produktdatenbank, Excel-Import oder Herstellerkatalog.

Erstelle zuerst eine technische Umsetzungsvorlage für:
1. robustes RoofPlane-/RoofGeometry-Modell mit mehreren belegbaren Dachflächen,
2. getrennte Überstände,
3. Firsthöhe als alternative Eingabe,
4. echte Störflächen mit Sperrflächen,
5. Modulfelder je Dachfläche,
6. Layer für Maßketten, Sperrflächen, Verschattung und Prüfhinweise,
7. Unit-Tests für Geometrie und PV-Nutzfläche.

Keine große Neuschreibung.
Keine Herstellerdaten erfinden.
Keine Montagesystem-Regeln final aktivieren.
Erst Phase 3D-Geometrie + Tests vorschlagen, dann Freigabe abwarten.
```

---
*Erstellt 2026-06-12 · Fokus: 3D-Dachplanung · Umsetzung gesperrt bis Freigabe*
