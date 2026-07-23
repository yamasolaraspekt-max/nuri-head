# Planner-Werkzeug-Benchmark — Hausplaner vs. professionelles Niveau (InDesign als Maßstab)

> **Rolle:** Planner. **Stand:** 2026-07-23. **Anlass:** Yama (InDesign-Screenshot): Magnet/automatische
> Ausrichtung, Kompass, „wie viele Werkzeuge InDesign hat". Ziel: ehrlicher Abgleich der Werkzeugtiefe.
> **Legende:** ✅ als Werkzeug da · 🟡 Engine/Modell da, aber KEIN Bedien-Werkzeug · ❌ fehlt ganz.

## Kurzbild
Aktueller Hausplaner-Werkzeugsatz: **6** (`auswahl · wand · fenster · tür · dach · treppe`).
Professionelles Ziel (InDesign): **20+ Werkzeuge** + Steuerungsleiste (Position/Größe/Drehung/
Skalierung/Ausrichten) + ~12 Panels. Viel Substanz ist als reine Engine schon da (Fang, Verschieben,
Spiegeln, Bemaßung, Azimut/Nord) — sie muss als Werkzeug **sichtbar und bedienbar** werden.

## 1. Auswahl & Navigation
| Werkzeug | Status | Beleg / Lücke |
|---|:--:|---|
| Auswahl (Objekt) | ✅ | `werkzeug='auswahl'` |
| Direktauswahl (Knoten/Punkt bearbeiten) | ❌ | keine Knoten-Bearbeitung |
| Hand / Pan (verschieben der Ansicht) | ❌ | fehlt als Werkzeug |
| Zoom-Werkzeug (Lupe, Zoom-Rahmen) | ❌ | fehlt als Werkzeug |
| Fläche markieren (Marquee/Rubber-Band) | ❌ | (siehe Lückenspec Sichtbarkeit/Sperre/Fläche) |

## 2. Erstellen / Zeichnen
| Werkzeug | Status | |
|---|:--:|---|
| Wand, Fenster, Tür, Dach, Treppe | ✅ | vorhanden |
| Objekte (Heizkörper etc. via Wizard) | ✅ | ConfigWizard → ADD_NODE |
| Freihand / Polylinie / Pfad (Pen) | ❌ | keine freie Kontur |
| Rechteck/Ellipse/Bemaßungs-Primitive | ❌ | keine generischen Formen |

## 3. Transformieren
| Werkzeug | Status | Beleg / Lücke |
|---|:--:|---|
| Verschieben (Move/Offset) | 🟡 | `editierGeometrie.versetzePunkt/versetzteWand` — Engine da |
| Spiegeln (Mirror) | 🟡 | `spiegelePunkt/spiegelteWand` — Engine da |
| **Drehen (Rotate)** | ❌ | keine Rotationslogik |
| **Skalieren (Scale)** | ❌ | fehlt |
| Frei transformieren | ❌ | fehlt |

## 4. Ausrichten & Verteilen (Yama: „automatische Ausrichtung")
| Werkzeug | Status | |
|---|:--:|---|
| Ausrichten (links/rechts/oben/unten/zentriert) | ❌ | **fehlt komplett** (`achsenMitte` ist nur Baustein) |
| Verteilen (gleiche Abstände) | ❌ | fehlt |
| An Objekt/Kante/Referenz ausrichten | ❌ | fehlt |

## 5. Fang / Magnet & Führung (Yama: „Magnetfunktion")
| Werkzeug | Status | Beleg / Lücke |
|---|:--:|---|
| Fang: Endpunkt / Ortho / Raster | 🟡 | `fangKern.fange()`, `FangArt='endpunkt'|'ortho'|'raster'` — Engine da |
| **Magnet-Umschalter (an/aus, global)** | 🟡 | `snapSettings` im Konzept (F1); im UI-State noch nicht verdrahtet |
| Fang: Mittelpunkt / Schnittpunkt / Lot / Tangente | ❌ | nur endpunkt/ortho/raster |
| Hilfslinien / Lineal-Guides (ziehen, einrasten) | ❌ | fehlt |
| Raster sichtbar/konfigurierbar | ❌ | Raster-Fang ja, aber kein sichtbares/einstellbares Raster |

## 6. Orientierung / Kompass (Yama: „Kompassfunktion")
| Werkzeug | Status | Beleg / Lücke |
|---|:--:|---|
| Azimut / Nord im Modell | 🟡 | `dachGeometrie`: Nord = +y, Azimut aus `firstAzimutGrad` |
| **Kompass-Werkzeug / Nordpfeil (Anzeige + drehen)** | ❌ | keine UI, keine Ausrichtung-nach-Nord |
| „nach Nord ausrichten" / Azimut-Drehscheibe | ❌ | fehlt (wichtig für PV/Verschattung) |

## 7. Messen / Bemaßung
| Werkzeug | Status | Beleg |
|---|:--:|---|
| Maßketten / Grundriss-Bemaßung | 🟡 | `masskette.ts`, `bemassung.ts` — Engine da |
| Mess-Werkzeug (Strecke/Winkel/Fläche interaktiv) | ❌ | kein interaktives Messen |

## 8. Sichtbarkeit / Sperre / Ebenen / Fläche
Siehe eigene Lückenspec `docs/planner/werkzeuge-sichtbarkeit-sperre-flaeche.md`:
Auge (🟡 Modell da), Schloss (🟡 Modell+Gate da), Layer-Panel (❌), Fläche markieren/sperren/freigeben/
eingrenzen (❌).

## 9. Eigenschaften / Stil
| Werkzeug | Status | |
|---|:--:|---|
| Pipette / Eigenschaften übertragen (Eyedropper) | ❌ | fehlt |
| Eigenschaftenpanel (Werte je Selektion) | 🟡 | rudimentär vorhanden; Tabs/Mehrfach fehlen (UI-5) |

## 10. Ansicht
| Werkzeug | Status | |
|---|:--:|---|
| 2D / Split / 3D | ✅ | `store.modus` |
| Zoom/Pan als Werkzeug + Zoom-zu-Auswahl | ❌ | fehlt |

---

## Prioritäts-Empfehlung (kausal, günstig-zuerst)

**Stufe A — billig, weil Engine/Modell schon steht (nur Werkzeug + Command sichtbar machen):**
1. **Magnet-Umschalter** + Fang-Endpunkt/Ortho/Raster bedienbar (Engine `fangKern` verdrahten, `snapSettings` in UI-State).
2. **Auge + Schloss** (Sichtbarkeit/Sperre) — Commands `SET_VISIBLE/LOCKED`.
3. **Verschieben/Spiegeln** als sichtbare Werkzeuge (Engine `editierGeometrie`).
4. **Bemaßung** als Werkzeug (Engine `masskette/bemassung`).
5. **Zoom/Pan/Hand** (reine Ansicht, kein Modell).

**Stufe B — neue Logik, mittlerer Aufwand:**
6. **Ausrichten & Verteilen** (Align/Distribute) — neue Rechenlogik auf Selektion.
7. **Fläche markieren** (Marquee) → dann Massen-Sperre/-Sichtbar/-Eingrenzen.
8. **Drehen / Skalieren**.
9. **Kompass / Nordpfeil** (Anzeige + „nach Nord ausrichten"; nutzt Azimut).
10. **Layer-/Ebenen-Panel** (UI-8/§33).

**Stufe C — größer / später:**
11. Direktauswahl/Knoten-Bearbeitung, Pen/Polylinie, Pipette, Hilfslinien/Lineal-Guides, sichtbares Raster.

**Guardrails:** additiv; `visible`/`locked`/`snapSettings` sind bereits im Modell/Konzept — kein Schema-Bruch,
kein 422. Jede Stufe eigener Planner→Generator→Evaluator-Zyklus; Barrierefreiheit (Werkzeugzustand nie nur
über Farbe/Form — Icon + Tooltip/aria).
