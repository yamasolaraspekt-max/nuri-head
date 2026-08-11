# REGISTER DER WERKZEUGE

> Eine Zeile je Werkzeug. **Hier steht, was es gibt und woran es hängt** —
> nicht, wie weit es ist. Der Auftragszustand steht in `docs/STATUS.md`.
>
> Reifegrad: `LEER` (nur Ordner) · `BESCHRIEBEN` (Blätter gefüllt) ·
> `GEBAUT` (Code vorhanden) · `GEPRÜFT` (Kriterien grün, Grenzen belegt)

---

## Stufe 1 — Fundament (alles andere hängt daran)

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-01 | Raster und Fang | **BESCHRIEBEN** | — | F-040 ✓, F-041 ✓, F-001 ✓, F-003 ✓, ~~F-004~~ ⓝ |
| W-02 | Wand zeichnen | **BESCHRIEBEN** | W-01 | F-001, F-002, F-030 |
| W-13 | Auswahl und Griffe | LEER | W-02 | **keine** ⓝ (~~F-012~~, ~~F-003~~) |
| W-12 | Ansicht und Kamera | LEER | — | F-032 |

## Stufe 2 — Grundriss

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-03 | Wand bearbeiten | LEER | W-02, W-13 | F-003, F-004, F-030 |
| W-04 | Öffnung (Tür/Fenster) | **BESCHRIEBEN** | W-02 | **keine** ⓝ (~~F-003~~, ~~F-031~~) |
| W-05 | Raum erkennen | **BESCHRIEBEN** | W-02 | F-010, F-011 (ohne Betrag), F-012 ⚠, F-013 ⚠, **F-001** |
| W-10 | Decke und Boden | LEER | W-05 | F-011, F-030 |
| W-16 | Grundriss unterlegen | LEER | W-12 | F-032 |

## Stufe 3 — Aufbau in der Höhe

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-06 | Geschoss verwalten | LEER | W-02 | F-032 |
| W-07 | **Dach aus Kontur** | **BESCHRIEBEN** | W-05, W-06 | F-010, F-013, **F-014, F-025, F-026**, F-020, F-021, F-022 |
| W-08 | Dachfläche messen | LEER | W-07 | F-011, F-023, F-024 |
| W-09 | Treppe | LEER | W-06 | F-001, F-030 |
| W-21 | **Sparren und Lattung** | **BESCHRIEBEN** | W-07 | F-001 ⚠, F-030 ⚠ · Quelle M-01/**M-02 ungelesen** |
| W-22 | **Gaube** | LEER | W-07 | **F-027** ✓, ~~F-031~~ ⓝ |
| W-23 | **Deckung und Material** | LEER | W-07, W-08 | **F-050** |

## Stufe 4 — Darstellung und Ausgabe

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-11 | Maß und Bemaßung | **BESCHRIEBEN** | ~~W-13~~ ⓝ | F-001 ✓, ~~F-002~~ ⓝ, ~~F-003~~ ⓝ |
| W-14 | Kopieren/Spiegeln/Drehen | LEER | W-13 | F-032 |
| W-15 | Material und Farbe | LEER | W-13 | — |
| W-17 | Export und Speichern | LEER | alle | — |

## Stufe 5 — Prüfung und Auswertung

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-18 | Topologie prüfen | LEER | W-02, W-05 | F-004, F-013 |
| W-19 | Sonne und Verschattung | LEER | W-07, W-08 | F-024 |
| W-20 | Stückliste und Mengen | LEER | W-05, W-08 | F-011, F-023 |

---

## Abhängigkeitskette — die Reihenfolge, in der gebaut werden muss

```
W-01 Fang ─┬─► W-02 Wand ─┬─► W-03 bearbeiten
           │              ├─► W-04 Öffnung
           │              ├─► W-05 Raum ──┬─► W-10 Decke
           │              │               └─► W-20 Mengen
           │              └─► W-06 Geschoss ─► W-07 DACH ─┬─► W-08 messen
           │                                  │           └─► W-19 Sonne
           │                                  └─► W-09 Treppe
           └─► W-13 Auswahl ─┬─► W-11 Maß
                             ├─► W-14 Kopieren
                             └─► W-15 Material
W-12 Kamera (quer zu allem)
W-17 Speichern (braucht alles)
W-18 Prüfung (braucht W-02, W-05)
```

> **Was diese Kette bedeutet:** W-01 zuerst. Ohne verlässlichen Fang ist jede
> Wand ungenau, und jede Ungenauigkeit vererbt sich nach oben — bis das Dach
> nicht mehr schließt. **Ein wackliger Fang ist kein Schönheitsfehler,
> er ist ein Fundamentfehler.**

---

## Was schon im Repo existiert

Beim Anlegen dieses Registers gemessen — **noch nicht in die Werkzeugordner
eingearbeitet**, das ist der nächste Schritt:

| Fundstelle im Repo | Gehört zu |
|---|---|
| `resources/planner/hausplaner/geometry/dachGeometrie.ts` | W-07 |
| `resources/planner/hausplaner/renderers/three-d/szene.ts` | W-07, Schicht 4 |
| `resources/planner/hausplaner/app/tools/workspaceIds.ts` | Werkzeugregistrierung |
| `resources/planner/hausplaner/geometry/fangKern.ts` | **W-01** — 276 Zeilen, 11 Ausfuhren, 3 Testdateien mit 45 Zusagen; **eingearbeitet 10.08.2026** |
| `resources/planner/hausplaner/geometry/wallGeometry.ts` | **W-02** — 317 Zeilen, 12 Ausfuhren; Azimut-Konvention Spec ▲K2, mm-Integer-Welt; **eingearbeitet 10.08.2026** |
| `resources/planner/hausplaner/geometry/wandFlaeche.ts` | **W-02** — 238 Zeilen, 6 Ausfuhren; AUF-77 Mengenermittlung M1, „entweder Mengen oder Meldungen"; **eingearbeitet 10.08.2026** |
| `resources/planner/hausplaner/geometry/oeffnungsBauarten.ts` | **W-04** — 75 Zeilen, 5 Ausfuhren; 24 Fenster- und 24 Tür-Bauarten, SVGs unter `public/hausplaner/icons/`; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/oeffnungsTypen.ts` | **W-04** — 49 Zeilen, 7 Ausfuhren; 5 Tür- und 7 Fenster-Vorlagen mit Standardmaßen; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/masskette.ts` | **W-11** — 118 Zeilen, 7 Ausfuhren; Rechenschicht, entdoppelt mit Toleranz; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/bemassung.ts` | **W-11** — 108 Zeilen, 6 Ausfuhren; Rechenschicht, innen Öffnungskette / außen Gesamtmaß; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/masseingabe.ts` | **W-11** — 169 Zeilen, 9 Ausfuhren; **EINGABEschicht**, ohne Importe; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/roomDetection.ts` | **W-05** — 190 Zeilen, 4 Ausfuhren; planares Halbkanten-Verfahren, **kein Registry-Werkzeug**, läuft automatisch; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/sparrenBerechnung.ts` | **W-21** — 131 Zeilen, 7 Ausfuhren; **Eurocode-VORBEMESSUNG**, ersetzt keine prüffähige Statik; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/sparrenTrennung.ts` | **W-21** — 67 Zeilen, 3 Ausfuhren; Trennung an Öffnungen + Sicher-Entscheidung; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/schifterListe.ts` | **W-21** — 152 Zeilen, 9 Ausfuhren; **gemischt** — konstruiert UND aggregiert; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/holzBauteile.ts` | **W-21** — 82 Zeilen, 4 Ausfuhren; trägt `OFFENE_HOLZBAUTEILE` — gebaute Selbstauskunft über die Grenzen; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/holzMengen.ts` | **W-21** — 64 Zeilen, 3 Ausfuhren; Mengen aus der **echten** 3D-Holzliste statt geschätzt; **eingearbeitet 12.08.2026** |

## Was aus Yamas eigenem Bestand kommt

Ausgewertet am 07.08.2026 — Einzelheiten in `../05-MATERIALQUELLEN/BESTAND-YAMA.md`.

| Fundstelle | Gehört zu | Was daraus wurde |
|---|---|---|
| `dachdecker_pro_3d.tsx:139-147` | W-07 | **F-014** Erkennung einspringender Ecken |
| `dachdecker_pro_3d.tsx:153-158` | W-07 | **F-025** Grat · Kehle · Ortgang |
| `dachdecker_pro_3d.tsx:101-131, 1134-1137` | W-07 | **F-026** Dach über Grundform — **kann L und T** |
| `dachdecker_pro_3d.tsx:1190-1255` | W-22 | **F-027** Gaubenaufbau |
| `dachdecker_pro_3d.tsx:186-191` | W-23 | **F-050** Materialkennwerte |
| `dachdecker_pro_3d.tsx:194-198` | W-20 | **F-051** Zeitwerte je Gewerk |
| `profi_holzbau_solar_cad.tsx` | W-21 | noch auszuwerten |
| `solarmaster_konstruktion.tsx` | W-19 | noch auszuwerten |
| Pfettendach-Fachbilder | W-21 | Bauteilbenennung |

> **Der wichtigste Befund:** `F-026` kann **L- und T-Grundrisse** — genau der Fall,
> an dem Auftrag Z-07 scheiterte. Der Code liegt auf Yamas Schreibtisch und läuft.
> Vor jedem weiteren Bau an W-07 ist zu entscheiden, welcher der beiden Dachwege
> zuerst kommt. Der Vergleich steht in der Formelsammlung, Gruppe 6.

### F-SPALTE UND ABHÄNGIGKEITEN GEGEN DEN CODE GEMESSEN — Planner 11.08.

**Grundlage:** `ARBEITSREGELN:102` — *der Planner ist Eigentümer von Spezifikationsfehlern.* Der
Generator hat zweimal gemeldet statt korrigiert (`a44e5fdd`, `0299e5ca`) und ausdrücklich
zurückgegeben, dass die Zuordnung dem Planner gehört. **Claim `d0adbec5`, gesetzt vor der ersten
Änderung.**

```text
ZEICHEN
  ✓   am Code BELEGT, mit Fundstelle unten
  ⓝ   am Code NICHT belegt -> durchgestrichen. Die Formel bleibt gueltig, sie steht
      nur am falschen Werkzeug. NICHTS ist aus der Formelsammlung geloescht.
  ohne Zeichen  UNGEPRUEFT — kein Blatt benennt das Modul, also ist die Zuordnung
      nicht messbar. Ich habe sie NICHT geraten (13 der 23 Zeilen).
```

**Die Messungen, je Formel einmal, in `resources/planner/hausplaner/`:**

```text
F-001  Abstand         Math.hypot|Math.sqrt   fangKern 4x ✓ · bemassung 1x ✓ ·
                                              masseingabe 1x ✓ · (14 Module insgesamt)
F-002  Winkel          Math.atan2             dachAusschnitt · roomDetection · wallGeometry
                                              -> in W-11s DREI Modulen: 0x  ⓝ
F-003  Lotfusspunkt    lotAufGerade|lotFuss   NUR fangKern (W-01) ✓
                                              -> W-04: 0x · W-11: 0x · W-13: 0x  ⓝ
F-004  Schnittpunkt    schnittpunkt           schifterListe:71 · wallGeometry:62,106
                                              -> fangKern (W-01): 0x  ⓝ
F-012  Punkt-in-Polygon punktInPolygon|strahl szene.ts · dachAusschnitt
                                              -> W-13s trefferSuche: Math. 0x  ⓝ
F-027  Gaubenaufbau    Math.tan               gaubeGeometrie 6x ✓
F-031  CSG-Differenz   csg|CSG                NUR EIN Treffer, und er ist ein KOMMENTAR:
                                              dachAusschnitt.ts:10 " * - Stufe C (NICHT
                                              hier): echte Polygonloch-/CSG-Operationen"
                                              -> in der ganzen Insel NICHT gebaut  ⓝ
F-040  Raster          raster|snap            fangKern ✓        F-041  fangKern ✓
```

**Die falsche ABHÄNGIGKEIT, und sie ist der schwerste der Befunde:**

```text
"W-11 braucht W-13"   auswahl|select|markiert in bemassung.ts + masskette.ts:  0x
                      bemassung() hat keinen Auswahl-Parameter
                      die einzige Aufrufstelle uebergibt ALLE Waende und ALLE Oeffnungen
                      -> die Abhaengigkeit traegt nicht, gestrichen
```

> **Warum die Abhängigkeit schwerer wiegt als jede F-Nummer:** *Eine falsche Formelangabe führt
> beim Schreiben eines Blattes in die Irre — ärgerlich, aber der Bauende merkt es (und hat es
> gemerkt).* **Eine falsche Abhängigkeit steuert die REIHENFOLGE: sie sperrt ein Werkzeug hinter
> ein anderes, das es nicht braucht.** *Das ist strukturell dieselbe Klasse wie die erfundene
> §3-Sperre, die ich heute in vier eigenen Blättern gemeldet und korrigiert habe
> (`docs/MELDUNG-ERFUNDENE-SPERRE-A-12.md`) — nur an einem Ort, der **jede künftige Planung**
> steuert statt nur vier Blätter.*

**Drei verschiedene Ursachen, kein einheitliches Muster — meine erste Hypothese ist gefallen:**

```text
W-04   das Modul ist ein KATALOG (Math. 0x). Ein Katalog braucht keine Formel.
W-11   die Formeln wurden nach THEMA zugeordnet ("Bemassung braucht Winkel und Lot")
       statt am Modul gemessen. F-001 stimmt, F-002/F-003 nicht.
W-13   das Modul liegt in app/tools (Schicht 3), nicht in geometry (Schicht 2).
       Ein Anwendungswerkzeug RUFT Formeln, es enthaelt sie nicht.
```

> *Ich hatte gehofft, die Schicht erklärt alles — sie tut es nicht: **W-04 und W-11 liegen beide in
> `geometry` und tragen trotzdem falsche Zuordnungen.** Die Hypothese fällt, und ich schreibe sie
> als gefallen hin, statt die drei Fälle in ein Muster zu pressen, in das sie nicht passen.*

**Was ausdrücklich NICHT geändert ist:**

```text
KEINE Formel geloescht, keine umgeschrieben — FORMELSAMMLUNG.md ist nicht im Scope.
KEIN Reifegrad angefasst (die Spalte gehoert dem Generator).
13 der 23 Zeilen UNGEPRUEFT gelassen, weil kein Blatt ihr Modul benennt.
Die alten Generator-Befunde (unten) bleiben stehen — sie sind der Anlass und
werden nicht durch meine Korrektur unsichtbar gemacht.
```

> ⚠ **W-04s F-Zuordnung stimmt nicht mit dem Code überein.** Das Register nennt F-003 und F-031; gemessen enthalten `oeffnungsBauarten.ts` und `oeffnungsTypen.ts` **keine Rechnung** — `Math.` kommt in beiden **null mal** vor, die einzigen Operationen sind `Array.find()` und `??`. **Der Generator ändert die Zuordnung nicht** (sie gehört dem Planner) und meldet sie als Befund. *Siehe `1-ZWECK`/`3-FORMELN` des Werkzeugs.*

> ⚠ **W-11s Registerangaben halten der Messung nicht stand — zwei Stellen.** (1) **F-002 und F-003 stehen nicht im Code:** kein `atan2`, kein `lotAufGerade` in den drei Modulen; nur F-001 ist belegt (`bemassung.ts:77`, `masseingabe.ts:58`). (2) **Die Abhängigkeit „braucht W-13" trägt nicht:** `auswahl`/`select`/`markiert` kommen in `bemassung.ts` und `masskette.ts` **null mal** vor, `bemassung()` hat keinen Auswahl-Parameter, und die einzige Aufrufstelle übergibt alle Wände und alle Öffnungen. **Der Generator ändert beides nicht** — die Zuordnung gehört dem Planner.

> ⚠ **W-21: beide genannten F-Nummern stehen nicht im Code.** `Math.hypot`/`Math.sqrt` kommen in **keinem** der fünf Module vor (F-001), und es wird nichts extrudiert (F-030) — die Stäbe kommen fertig aus der 3D-Engine. **Das ist stimmig:** drei der fünf Module aggregieren aus einer bereits erzeugten Liste und brauchen keine Geometrieformel. **Umgekehrt fehlt eine Nummer:** `bodenschneelast()` und `formbeiwertSchnee()` rechnen, sind aber **normative** Größen (DIN EN 1991-1-3 / EN 1995-1-1) — die Sammlung kennt sie zu Recht nicht. *Eine erfundene F-Nummer wäre schlimmer als die gemeldete Lücke.* **Und M-02 (2.021 Zeilen) ist nicht ausgewertet.** Der Generator ändert nichts davon.
