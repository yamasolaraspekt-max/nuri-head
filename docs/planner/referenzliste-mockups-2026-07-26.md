# Referenzliste: die Dashboard-Mockups gegen den gebauten Stand

**Planner · 26.07.2026 · Quelle: `~/Downloads/dashboard-*.html` · gemessen gegen HEAD `09eb0c5`**

Yamas Auftrag: *alle Layouts mit dem aktuellen vergleichen, auflisten was fehlt, und erst einmal
eine Referenzliste — was haben wir, was fehlt, was haben wir zusätzlich.* **Nur diese Datei und die
ihr ähnlichen**, nichts sonst aus dem Ordner.

---

## 0. Zuerst eine Zahl, die Arbeit spart

Im Ordner liegen **16** Dateien dieser Familie — aber nur **9 verschiedene**. Nach Prüfsumme:

| Prüfsumme identisch | Dateien |
|---|---|
| `151a027c…` | `dashboard-wizard-v1` = `_1` = `_2` |
| `733f0f68…` | `dashboard-tools-v1` = `_1` = `_2` |
| `00eb6f13…` | `dashboard-import-v3` = `v3_1` |
| `05344535…` | `dashboard-v1-mockup_1` = `_2` |

**Die Datei, die Yama genannt hat (`dashboard-wizard-v1_2.html`), ist byte-gleich mit
`dashboard-wizard-v1.html`.** Sieben Downloads sind Wiederholungen desselben Stands; wer sie
einzeln durchsieht, prüft dreimal dasselbe.

---

## 1. Die Kette — was worauf aufbaut (nach Änderungszeit, 24.07.)

Jede Stufe **enthält** die vorige und legt genau eine Sache oben drauf. Das ist die Reihenfolge, in
der übernommen werden sollte; sie umzustellen heißt, eine Grundlage nachzubauen.

| # | Datei | Titel | Was **neu** dazukommt |
|---|---|---|---|
| 1 | `dashboard-v1-mockup` | Dashboard v1 (Vorschau) | Grundgerüst: drei Ebenen (Übersicht · Geführt · Experte), Werkzeugleiste mit **ehrlichen Zuständen** („Verfügbar / In Vorbereitung / Voraussetzung fehlt" statt „schläft"), 2D·Split·3D, rechtes Eigenschaftenpanel, Prüfung mit Befund |
| 2 | `dashboard-v1-mockup_1` | v1 korrigiert | Links wird aus der Werkzeugliste ein **Projektbaum** (Projekt · Ebenen · Filter → Geschoss → Räume/Wände/Öffnungen/Dach/Aufbauten) |
| 3 | `dashboard-pro-mockup` | App-Shell **Pro** | **Befehlspalette ⌘K** mit Suche, „Springe zu" und „Probier:"; Geschoss-Menü (EG/1.OG/2.OG/DG); Gruppen-Kurznamen (Zeich./Bearb./Rechn.) |
| 4 | `dashboard-modes-mockup` | **Modi + Wizard** | Der **geführte Ablauf**: „Projekt aufbauen", **Schritt 9 von 20**, Fehlende-Angabe-Karte mit zwei Wegen (automatisch übernehmen / manuell); **Prüfungscenter** (1 Fehler, 2 Warnungen, 4 Hinweise); **Übersichtsseite** mit Projektkarte, Fortschritt 45 %, Sprung in Arbeitsbereiche |
| 5 | `dashboard-import-mockup` | **Import & Nachzeichnen** | Ein ganzer **Workspace**: Quelle (PDF/PNG/DWG mit Seiten und Layern) · Statuskette · **Kalibrierung** (1 px = 11,74 mm) · **Erkennung** mit Vertrauensstufen (sicher/wahrscheinlich/prüfen/unsicher) · Referenz-Deckkraft, S/W, Invertieren · Overlay·Split·Differenz |
| 6 | `dashboard-import-mockup_1` | dito + Flyout | **Nachzeichnen-Flyout** (Wandachse · Wandkanten · Polylinie · Raumkontur · Öffnung · Dachkontur · Hilfslinie · Korrekturstift) und **Tab blendet alle Panels aus** |
| 7 | `dashboard-import-v2` | + **Kontextleiste** | Eine **zweite obere Zeile**, die zum aktiven Werkzeug gehört (Ansicht · Bearbeiten · Messen & Export · Zoom), plus Einzelprüfung „Objekt 2/7 — Bestätigen / Typ ändern / Ablehnen" |
| 8 | `dashboard-import-v3` | + **Projektidentität** | Kopfzeile **H-2041 · Neubau EFH · Kd. 2041 · Fam. Berger**; **Projektwahl aus ticket**; Speichern/**Auto-Speichern**/**Versionen & Verlauf**; **Anwesenheit** (2 Personen, Schreibrecht); Gruppen aufgefächert (Transformieren · Anordnen · Bemaßen · Exportieren) |
| 9 | `dashboard-wizard-v1` | **Technischer Wizard** | Der Ablauf **nach** der Geometrie: „Heizung planen", **Schritt 8/15**, Abhängigkeitskette (Bauphysik → Lüftung → Heizlast → Übergabe → Abgleich → WP), **Datencheck** als Zeilenliste mit Soll/Ist (10/12, 8/10, „geschätzt", „fehlen"), **Übergabepaket an Fachmodul** mit „Noch nicht startbereit" |
| 10 | `dashboard-tools-v1` | **Personalisierbare Werkzeugleiste** | **Sechs Werkzeug-Zustände** (Angeheftet · Vom Wizard empfohlen · Aktiv · Gesperrt mit Grund · im Overflow · System/Pflicht); **Workspace-Vorlagen** (Architektur/Dach/Bauphysik/Heizlast/Heizung/Elektro/PV) mit persönlicher Kopie; **Leisten-Editor** mit Drag-and-drop aus der Registry (54 verfügbar) |

**Der rote Faden:** 1–3 bauen die *Hülle*, 4 den *Ablauf*, 5–8 den *Eingang* (wie kommt ein
Bestandsplan ins Modell), 9 die *Fach-Übergabe*, 10 die *Personalisierung*.

---

## 2. Was wir haben — gemessen, nicht schätzungsweise

| Modul aus den Mockups | Im Code | Beleg |
|---|---|---|
| Drei Ebenen: Übersicht · Geführt · Experte | **ja** | `StartView.tsx` · `GuidedView.tsx` · `HausplanerApp.tsx` |
| 2D · Split · 3D | **ja** | `HausplanerApp.tsx`, Modus `split` misst die Bühne selbst |
| Werkzeugleiste, gruppiert, eine Zeile | **ja** | AUF-70 abgenommen; 16 Knöpfe, 5 Gruppen, Abstand 21 gegen 6 px gemessen |
| **Ehrliche Werkzeug-Zustände** | **ja** | `werkzeugZustand.ts` · `vorbedingungen.ts` · `gesperrtStil.ts`; heute gemessen: `aria-disabled` + Opazität 0,6 |
| **Befehlspalette ⌘K** | **ja** | `dashboard/palette.ts` — Quelle ist die Registry, **keine zweite Aktivierungslogik** |
| Suche über Werkzeuge | **ja** | `tools/trefferSuche.ts` |
| **Arbeitsbereiche/Workspaces** | **ja** | `dashboard/arbeitsbereiche.ts` — fünf: **Import**, Architektur, Bauphysik, Heizung, Elektro/PV |
| **Angeheftete Werkzeuge** | **ja** | `state/angeheftet.ts` + `state/arbeitsbereichSpeicher.ts` |
| Overflow / „Weitere" | **ja** | `werkzeugGruppen.ts` (14 Fundstellen `Overflow`) |
| Projektbaum links | **ja** | `dashboard/projektBaum.ts` |
| Geschoss-Stapel (EG/OG/DG) | **ja** | `dashboard/geschossStapel.ts` · `GeschossFlaeche.tsx` |
| Panel-Reiter rechts | **ja** | `dashboard/panelTabs.ts` · `ReiterLeiste.tsx` · `enginePanels.ts` |
| Speicher-Anzeige „Ungespeichert" | **ja** | `dashboard/speicherAnzeige.ts` |
| Geführte Schritte | **ja** | `dashboard/fahrschritte.ts` (6 Abschnitte) · `tools/naechsterSchritt.ts` |
| **Prüfungscenter** | **teilweise** | `dashboard/befunde.ts` — **liefert heute 0 oder 1 Befund**, weil der Store genau **eine** Meldung hält (`letzteAblehnung`). Das Mockup zeigt 1 Fehler + 2 Warnungen + 4 Hinweise |
| Werkzeug-Registry | **ja** | `werkzeugPaket.ts`, **102 Einträge**; Vertrag in `werkzeugVertrag.ts` |
| Fachflächen / Engines | **teilweise** | `fachFlaechen.ts` · `EngineFlaeche.tsx`; **1 von 13** Engines angeschlossen (AUF-52 offen) |
| Projektliste aus ticket | **in Arbeit** | **AUF-78**, gerade im Bau (`app/state/projekte.ts` neu) |

---

## 3. Was fehlt — nach Größe sortiert, mit dem Vorschlag, wie es zu schneiden ist

### Groß (eigener Fahrplan, nicht ein Posten)

1. **Der ganze Workspace „Import & Nachzeichnen"** (Mockups 5–7). Vorhanden sind **Namen**, nicht
   Funktion: `WORKSPACE_IMPORT` ist als Bereich definiert, und in der Registry stehen
   `bild-importieren`, `datei-importieren`, `grundriss-erkennen`, `kalibrieren`,
   `erkennung-bestaetigen`, `nordrichtung-setzen`, `beschneiden`. **Es fehlt alles dahinter:**
   Quellenliste mit Seiten/Layern · Kalibrierung mit Maßstabsrechnung · Erkennung mit
   **Vertrauensstufen** (`Vertrauen` = **0 Fundstellen** im Code) · Referenz-Layer mit Deckkraft/
   S-W/Invertieren · Overlay·Split·Differenz · Einzelprüfung „Objekt 2/7" · Nachzeichnen-Flyout.
2. **Der technische Wizard mit Abhängigkeitskette** (Mockup 9). `fahrschritte.ts` führt die
   *Geometrie*-Schritte; die Kette Bauphysik → Lüftung → Heizlast → Übergabe → Abgleich → WP mit
   **Datencheck-Zeilen** (10/12, 8/10, „geschätzt") gibt es nicht. `Datencheck`: **1 Fundstelle**,
   `Fachmodul`: **1**.
3. **Der Leisten-Editor** (Mockup 10): Drag-and-drop aus der Registry, Workspace-Vorlagen,
   persönliche Kopie, „Als Vorlage exportieren". Die *Daten* dafür liegen (`angeheftet.ts`,
   `arbeitsbereichSpeicher.ts`) — die **Bedienoberfläche** dazu fehlt.

### Mittel (je ein Posten)

4. **Projektidentität in der Kopfzeile** — `H-2041 · Neubau EFH · Kd. 2041 · Fam. Berger`.
   `Projektident`: **0 Fundstellen**. Baut direkt auf AUF-78 auf, das gerade die Projektliste holt.
5. **Kontextleiste** (Mockup 7): eine zweite obere Zeile, die zum aktiven Werkzeug gehört.
   Achtung: AUF-70 hat gerade **eine** Werkzeugzeile hergestellt — eine zweite Zeile ist eine
   **Willensfrage an Yama**, kein Selbstläufer, und sie kostet Höhe (Grundlinie: Oberkante 369/405).
6. **Prüfungscenter mit echter Liste** — verlangt eine **Store-Änderung** (Befund-Historie mit
   Grund, Zeitstempel, Bauteilbezug). Steht als offener Posten schon in `befunde.ts` benannt.
7. **Versionen & Verlauf / Auto-Speichern** — `Auto-Speichern`: **0**, `Autospeicher`: **0**.
   `Revision` kommt 33-mal vor, aber als Datenfeld, nicht als Bedienung.
8. **Fortschrittsanzeige im Projekt** („45 %, 9 von 20 Schritten"). Verwandt mit AUF-79, aber das
   zählt *Aufträge*, nicht *Planungsschritte* — **nicht verwechseln**.

### Klein

9. **Tab blendet alle Panels aus** (Canvas maximal) — ein Tastendruck, ein Zustand.
10. **Zoom-Menü mit Prozentwert** in der Kopfzeile (heute nur Anzeige rechts).
11. **Bibliothek-Reiter** („Biblio.") — 3 Fundstellen, also benannt, nicht gebaut.

### Bewusst **nicht** übernehmen (mein Vorschlag, Yamas Entscheidung)

12. **Anwesenheit / Mehrbenutzer** („2 anwesend · Schreibrecht · MB", `Live`). `Anwesend`: **0**,
    `Schreibrecht`: **0**. Das ist **kein Layout-Posten, sondern ein Nebenläufigkeits-System** —
    Sperren, Konfliktauflösung, Übertragungsweg. **Wer das nebenbei baut, baut es falsch.**

---

## 4. Was wir **zusätzlich** haben — im Code, in keinem Mockup

| Zusätzlich | Was es ist | Warum es zählt |
|---|---|---|
| **Funktionsvertrag** je Werkzeug | `werkzeugVertrag.ts`, 1 419 Zeilen | Die Mockups zeigen Knöpfe; der Vertrag sagt, **was ein Knopf verspricht** und wann er es nicht darf |
| **Rechte-Durchreichung** | `state/rechte.ts` + `HausplanerRechteTest.php` | In den Mockups gibt es keine Rechte. In einem System mit 3 000 Kunden ist das der Unterschied zwischen Vorschau und Produkt |
| **Vorbedingungen als Daten** | `vorbedingungen.ts` | Mockup 10 *zeigt* „Voraussetzungen: Heizflächen vollständig…" als Text — bei uns ist es auswertbar |
| **Bühnenhöhe / Überstand** | `buehnenHoehe.ts`, AUF-72/73 | Kein Mockup kennt das Problem; gemessen waren **227–273 px** unerreichbar |
| **Stillgelegte Kataloge** | `toolCatalogStillgelegt.ts` | Erfundenes wird stillgelegt statt gelöscht — die Mockups zeigen durchweg **erfundene** Beispieldaten |
| **Schema-Prüfung + Wächter** | `schema:hausplaner:check`, `scripts/waechter.sh` | Kein Mockup hat ein Datenmodell |
| **Mengenermittlung** | `polygonFlaeche` · `roomDetection` · `wandaufbau` · `holzMengen` | In keinem Mockup vorgesehen, und Yamas eigentliches Ziel |

---

## 5. Der ehrliche Satz zum Schluss

**Die Hülle ist weitgehend gebaut, der Eingang fehlt ganz.** Was in den Mockups 1–4 steht, steht
zu großen Teilen im Code; was in 5–8 steht — **wie ein Bestandsplan überhaupt ins Modell kommt** —
existiert als Werkzeugnamen und sonst nicht. Das ist die größte einzelne Lücke zwischen Entwurf und
Stand, und sie ist größer als alle offenen Layout-Posten zusammen.

**Nichts davon ist hiermit beauftragt.** Diese Liste ist die Referenz, gegen die Yama entscheidet,
was ein Posten wird.
