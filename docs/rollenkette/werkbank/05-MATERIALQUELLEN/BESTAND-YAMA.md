# MATERIALQUELLEN — Yamas eigener Bestand

> Was auf dem Schreibtisch und im Wissensregister liegt, ausgewertet am 07.08.2026.
> **Verweise, keine Kopien** — dieselbe Regel wie im `~/wissensregister`.
> Diese Datei ist die Brücke zwischen Yamas Material und der Werkbank.

---

## Der Anschluss an das vorhandene Wissensregister

`~/wissensregister/` ist **bereits die richtige Methode** und wird nicht ersetzt:

| Datei dort | Rolle |
|---|---|
| `register.md` | Index + Eintragsschema (ID · Pfad · Typ · Auswertung · Tags · Nutzbarkeit · Aktualität · Vertraulichkeit) |
| `kategorien/*.md` | ~130 Einträge über 6 Kategorien |
| `ideensammlung.md` | nennt sich selbst „Input-Quelle des Planner-Agenten" |
| `scan-log.md` · `rescan.md` | inkrementeller Nachscan |

**Arbeitsteilung:** Das Wissensregister sagt, *was es gibt*. Diese Datei sagt,
*was davon in die Werkbank eingearbeitet ist und wo*. Kein zweiter Katalog.

---

## Ausgewertet und eingearbeitet

### M-01 · `dachdecker_pro_3d.tsx` — der wertvollste Fund

- **Pfad:** `~/Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d/dachdecker_pro_3d.tsx`
- **Umfang:** 2.173 Zeilen · 52 Stellen mit Trigonometrie · Three.js
- **Register-ID:** CODE-023

| Was drin steckt | Wohin eingearbeitet |
|---|---|
| `analyzeTopology` — Eckenwinkel + Erkennung einspringender Ecken | **F-014** |
| Kantentypen `TRAUFE·GIEBEL·PULT_WAND·WALM·TEILWALM` → Grat/Kehle/Ortgang | **F-025** |
| Grundformen inkl. `l-shape`, `t-shape`, `buildCompoundPitched` | **F-026** |
| Gauben: Schlepp · Trapez · Flach · Giebel, mit Anstieg und Ausrichtung | **F-027** |
| Materialkennwerte (kg/m², Stück/m²) für 4 Deckungen | **F-050** |
| Zeitwerte je Gewerk (11 Positionen) | **F-051** |
| Sparrenwerk: Abstand, Breite, Höhe, Kerve, Konterlattung, Membran | → neues Werkzeug W-21 |
| VELUX-Fensterbibliothek (4 Größen) | → Katalog, W-04 |
| Modultypen Trina/JA/LONGi mit Maß, Gewicht, Preis | → W-19 |
| Befestigungszuordnung K2 je Deckung | → W-20 |

> **Der entscheidende Befund:** Dieser Code kann **L- und T-Grundrisse** —
> genau den Fall, an dem Auftrag Z-07 scheiterte. Nicht über Straight Skeleton,
> sondern über vorgegebene Grundformen mit typisierten Kanten.
> Der Vergleich beider Wege steht in `01-MATHEMATIK/FORMELSAMMLUNG.md`, Gruppe 6.

### M-02 · Weitere 3D-Prototypen — **HOLZBAU AUSGEWERTET 12.08.**, vier Dateien offen

> **Bericht: `docs/BERICHT-M02-AUSGEWERTET.md`.** *Existenz, Umfang und Gleichheit für **alle fünf**
> gemessen; der Holzbau-Teil (`profi_holzbau_solar_cad.tsx`) inhaltlich ausgewertet.*
>
> ```text
> EXISTENZ     alle fuenf vorhanden. Zeilenzahlen unten stimmen EXAKT — erste
>              Bestandsangabe in diesem Projekt, die punktgenau haelt.
> UMFANG       13.852 Zeilen zusammen
> GLEICHHEIT   jede liegt 2-3x, ALLE Kopien byte-identisch. Keine divergenten Fassungen.
>              ACHTUNG: VORGEHEN.md:43 nennt "fuenfmal" — gemessen sind DREI. Grund unbekannt.
> ERGEBNIS     der BESTAND ist bei jedem gemessenen Holzbau-Begriff weiter.
>              Schifter, Kehlbalken, Zange, Aufschiebling hat NUR die Insel.
> EIN FUND     die Abbund-ZEICHNUNG (Canvas). Das Fachwissen hat die Insel schon —
>              dachformVorlagen traegt DREIZEHN ausgefuellte 'abbundhinweis'-Texte.
> KEIN GEWINN  TIME_VARS ist F-051 woertlich (zweite Quelle, Sperre bestaetigt) ·
>              battenDist 34 ohne Quelle (loest W-21Ls Operanden-Gate NICHT)
> OFFEN        11.831 Zeilen in vier Dateien — Zieladresse W-31 (PV) und W-23 (Deckung),
>              beide heute blockiert. Plus Pruefkandidat: solarconstructapp "Statik?"
> ```

| Datei | Zeilen | Trig-Stellen | Erwarteter Gewinn |
|---|---|---|---|
| `dachdecker_pro.tsx` | 2.993 | 48 | Vorgänger von M-01, evtl. andere Dachformen |
| `profi_holzbau_solar_cad.tsx` | 2.021 | 46 | **Holzbau/Sparrenkonstruktion** — für W-21 |
| `solarmaster_konstruktion.tsx` | 3.045 | 33 | PV-Belegung, Reihen/Spalten-Logik |
| `solarconstructapp.tsx` | 3.321 | 33 | Konstruktion + Statik? |
| `solar_master_pro.tsx` | 2.472 | 11 | Angebots-/Ertragsseite |

Alle unter `~/Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d/`.

### M-03 · HTML-Prototypen und Fachbilder

- `~/Desktop/03_Code_Prototypen/HTML_Prototypen_Dach_PV/` — `Dach3D-1.html`,
  `dach.html`, `dachplan.html`, `Frank.glb` (3D-Modell)
- `~/Desktop/03_Code_Prototypen/Entwuerfe_RTF_Code/` — **Pfettendach-Fachbilder**:
  Fußpfette · Mittelpfette · Firstpfette · First-Trauflinie · Schwelle.
  **Fachlich wertvoll für W-21** (Sparrenkonstruktion) — zeigt die
  Bauteilbenennung, die ein Zimmerer erwartet.
- `html. dachstuhl.rtf`, `3D.rtf`, `3d2.rtf`, `3d3.rtf`, `3D1.rtf`, `3d31.rtf`

### M-04 · Kataloge und Assets

| Was | Pfad | Für |
|---|---|---|
| Dachziegel-DB-Schema (MySQL 8) | `~/Desktop/_Code_lose/dachziegel_db_schema.sql` | Deckungskatalog |
| STL Frankfurter Pfanne | `~/Downloads/dachziegel_frankfurter_pfanne.stl` | 3D-Asset |
| ALTEC PV-Montagesysteme (4 PDF) | `~/Desktop/ALTEC_PV_*.pdf` | W-19/W-20 |
| Screenshots „DACHDECKER PRO" | `~/Desktop/_Screenshots/…2026-06-14…` (6 Stück) | **Bedienreferenz** — zeigt die gebaute Oberfläche mit Dachform-/Maß-Schiebern, Sparren, Haus mit Gaube |

---

## Vorbehalt: Duplikate

Dieselben Dateien liegen mehrfach:

```
Gemini-Code-Ideen-2026-05-25/
├── 00-komplettimport/                     ← Vollständige Kopie
├── 03-energie-pv-dach-3d/                 ← thematisch sortiert  ★ hier arbeiten
├── 20-themen-und-ideen-sortierung/09-…/   ← nochmal
├── 98-aussortiert/exakte-duplikate/       ← nochmal
└── 98-aussortiert/varianten-pruefen/      ← nochmal
```

`dachdecker_pro_3d.tsx` existiert **fünfmal**. Vor jeder Zählung deduplizieren,
sonst wird dieselbe Idee mehrfach gewertet.

**Festlegung: `03-energie-pv-dach-3d/` ist die Arbeitsfassung.** Alles andere
gilt als Kopie, auch wenn es neuer datiert ist — dann erst vergleichen.

---

## Was noch aussteht

| Nr | Aufgabe | Ergebnis |
|---|---|---|
| 1 | `profi_holzbau_solar_cad.tsx` auswerten | Sparrenkonstruktion → W-21 |
| 2 | `solarmaster_konstruktion.tsx` auswerten | PV-Belegung → W-19 |
| 3 | Pfettendach-Bilder lesen | Bauteilbenennung für W-21 |
| 4 | Dachziegel-Schema auswerten | Katalogmodell |
| 5 | Screenshots „DACHDECKER PRO" ansehen | Bedienreferenz für W-07/W-08 |
| 6 | Deduplizieren, Ergebnis ins Wissensregister zurückschreiben | sauberer Katalog |
