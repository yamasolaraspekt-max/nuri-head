# Planner-Spec — Gebäudeakte (Welle A4 · Fundament)

**Datum:** 2026-07-16 · **Rolle:** Planner (kein Produktionscode) · **Heimat-App:** ticket.
**Ziel-Idee (Navi/Fusion):** „Gebäude einmal erfassen — PLZ/Klima, Hülle, Heizung, Dach, Verbräuche — alle Rechner lesen daraus."

---

## 0 · Ist-Stand (belegt) — die Akte existiert schon, nur ohne Gesicht

1. **Das Objekt IST die Gebäudeakte im Datenmodell:** `LeadAlternativeAdd` trägt bereits ~90 Felder in genau den Akte-Kapiteln: Standort (street/postcode/lat/lon/**elevation**), **Verbräuche** (annual_consumption, annual_heating_energy_consumption[_kwh], heating_energy_unit, total_electricity_consumption, electricity_price, feed_in_tariff), **Dach** (roof_type/age/pitch/direction/covering/remark), **Gebäudehülle** (house_year, building_type/condition, building_length/width, facade_height, total_window_area, living_space, number_stories, number_we), **Heizung** (heating_system_type/year/age, old_heating_power, heat_distribution, **flow_temperature**, heating_load_calculation), **E-Mobilität** (electric_car*, wallbox*), Kamin (fireplace*).
2. **Die Verankerung ist schon entschieden:** `Anforderungsprofil` (versioniert, Status, `gebaeude_geometrie` = Rechen-Input raeume[]+bauteile[]) ist **polymorph kanonisch am Objekt** verankert — Code-Kommentar wörtlich: „kanonisch am Objekt (LeadAlternativeAdd) — Heizlast gehört ans Objekt" (Anforderungsprofil.php:16/44).
3. **Der Ketten-Eingang liegt fertig da:** `WpAuslegungsEingabe` (Services/Auslegung) will exakt: phiHl (Heizlast), qHeiz, qWw, Vorlauf, **PLZ**, wpTyp, Heizsystem, lat/lon — alles Felder, die Objekt + Anforderungsprofil tragen. Der `WpAuslegungsketteService` hat **keinen Aufrufer außerhalb seines Ordners** (grep) — dieselbe Lage wie bei der FiBu: Engine ohne Zündschlüssel.

**Folge:** Die Gebäudeakte ist KEIN Datenmodell-Projekt. Sie ist (a) eine **Akte-Fläche** über dem bestehenden Objekt und (b) ein **Vorbefüllungs-Adapter** Objekt→Rechner. Ein neues Schema wäre eine verbotene zweite Wahrheit.

## 1 · Ziel & Entscheidung

1. **Objektliste** (Kontakte → Kunden & Objekte → Gebäudeakte): alle Objekte mit Kunde, Adresse, **Vollständigkeits-Grad je Kapitel** (Standort · Hülle · Dach · Heizung · Verbräuche) als Fünf-Punkte-Ampel — der Blick „wo fehlen Operanden".
2. **Akte-Seite je Objekt:** die Kapitel als Karten mit den vorhandenen Werten; leere Felder werden als „fehlt" gelistet (Operanden-Gate: Lücken sichtbar machen, nie füllen); dazu die Kapitel **Profile & Berechnungen** (aktives Anforderungsprofil + Heizlast-Stand, verlinkt in deren Heimat-Flächen) und **Auslegungs-Reife**: welche der WpAuslegungsEingabe-Operanden das Objekt heute liefern kann (PLZ ✓, Vorlauf ✓, phiHl aus Profil ✓/fehlt …).
3. **V1 ist LESEND** + Sprunglinks in die bestehende Erfassung (Lead-/Objekt-Formulare bleiben die Schreib-Wahrheit). Schreiben direkt in der Akte = V2, eigener Durchgang.
4. **Adapter `GebaeudeakteAuslegungsAdapter`** (V1.5, direkt nach der Fläche): baut aus Objekt + aktivem Anforderungsprofil eine `WpAuslegungsEingabe`; fehlende Operanden ⇒ benannt zurückgegeben, nie geraten. Das ist der Zündschlüssel für die WP-Kette.

## 2 · Nahtstellen

**Neu:** `ObjektakteController@index/@show` · Views `admin/objekte/index.blade.php` + `akte.blade.php` · Routen (auth + Customer-Gate) · Navi „Gebäudeakte · geplant" → live.
**Bewusst NICHT angefasst:** `LeadAlternativeAdd`-Schema (0 neue Spalten in V1) · Lead-Erfassungs-Formulare · Anforderungsprofil-Versionierung · Heizlast-Module · `WpAuslegungsketteService` selbst.
**Erweiterungspunkte:** V2 Inline-Pflege in der Akte · 3D/Dachplaner-Kapitel (B-Stufe dockt als Karte an) · Projekt-Akte (B) liest dieselbe Objekt-Basis · „Mein Tag" zeigt die Akte dem Monteur mobil.

## 3 · Kantenliste

1. **Objekt ohne Kunde/Lead** (verwaist) → Akte trägt „ohne Kundenzuordnung", kein Crash.
2. **Mehrere Objekte je Kunde** (`main`-Flag) → Liste gruppiert nicht, zeigt Haupt-Objekt-Kennung.
3. **Fast alles leer** (Alt-Objekte): Vollständigkeit 0 % ist ein ehrlicher Zustand — Karten zeigen die 3 wichtigsten fehlenden Operanden zuerst, keine Wand leerer Zeilen.
4. **Einheiten-Falle Verbrauch:** `heating_energy_unit` (kWh/Liter/m³) MUSS neben jedem Verbrauchswert stehen — nie stillschweigend kWh annehmen.
5. **PLZ fehlt/ungültig** → Klima-Kapitel „unbestimmt", Auslegungs-Reife rot.
6. **Anforderungsprofil mehrfach/abgelöst** → nur `status`-aktives Profil zählt; Historie verlinkt.
7. **3000-Kunden-Skala:** Liste paginiert/begrenzt, Vollständigkeit ohne N+1 (ein Select, berechnet in PHP je Zeile aus geladenen Spalten).

## 4 · Abnahmekriterien (Evaluator, je mit Gegen-Beweis)

1. **Eine Wahrheit:** Feld im Lead-Formular ändern → Akte zeigt denselben Wert (gleiche Spalte, kein Sync-Code). Gegen-Beweis: grep — kein `create/update` auf ein neues Gebäude-Schema, 0 neue Migrationen.
2. **Vollständigkeits-Probe:** ein Referenz-Objekt von Hand ausgezählt = Anzeige der Ampel.
3. **Auslastungs-Reife-Probe:** für ein Referenz-Objekt entspricht die angezeigte Operanden-Liste exakt den WpAuslegungsEingabe-Feldern (Cent-genau: phiHl nur aus aktivem Profil, nie aus `heating_load_calculation`-Freitext).
4. **Kante 3/4:** leeres Objekt rendert; Verbrauch zeigt immer Einheit.
5. **Wächter:** Customer-Gate Route+Navi · keine Regression an new_leads/alternatives (reine SELECTs) · Styleguide-Konformität.

## 5 · Offene Punkte an Yama

- **(a) V1 lesend** mit Sprunglinks (meine Empfehlung — null Risiko fürs Live-System, Fläche sofort nutzbar) **oder gleich mit Inline-Pflege** (mehr Nutzen, aber Schreibpfad auf das zentrale Objekt-Modell = eigener Evaluator-Durchgang)?
- **(b) Einstieg:** eigene Objektliste unter Kontakte (Spec-Annahme) — zusätzlich Verweis-Karte in der Kundenakte („Objekte des Kunden")?
