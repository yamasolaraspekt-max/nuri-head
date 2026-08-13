# ANFORDERUNGEN — was der Software-Architekt festlegen muss

> Recherchiert aus CAD-/BIM-Fachquellen (siehe `04-QUELLEN/QUELLEN.md`).
> Jede Zeile ist eine **Entscheidung, die getroffen sein muss, bevor gebaut wird**.
> Wo eine Entscheidung noch offen ist, steht `OFFEN — Yama`.

---

## A. Fundament — die sechs Entscheidungen, die alles andere binden

### A1 · Einheit und Genauigkeit

Ein Planer, der intern in verschiedenen Einheiten rechnet, produziert Rundungsfehler,
die sich über Geschosse aufaddieren.

| Festlegung | Wert | Warum |
|---|---|---|
| Interne Einheit | **Millimeter, ganzzahlig** | Kein Fließkomma-Drift bei Additionen entlang einer Wandkette |
| Anzeige-Einheit | Meter mit 2–3 Nachkommastellen | Was der Handwerker liest |
| Winkel intern | Radiant (`double`) | Trigonometrie rechnet in Radiant |
| Winkel angezeigt | Grad | Was der Anwender denkt |
| Fangtoleranz | 20 mm (einstellbar) | Kleiner als eine Wandstärke, größer als Mauszittern |
| Gleichheitstoleranz ε | 0,5 mm | Zwei Punkte darunter sind derselbe Punkt |

> **Die Regel dahinter:** Ganzzahlige Millimeter im Datenmodell, Fließkomma nur in
> der Geometrieberechnung, und beim Zurückschreiben wieder runden. Wer Fließkomma
> speichert, bekommt Wände, die sich „fast" berühren.

### A2 · Was ist die führende Wahrheit?

**Entscheidung: das Datenmodell, nicht die 3D-Szene.**

Die 3D-Darstellung ist eine *Ableitung* des Datenmodells, nie umgekehrt. Wenn der
Anwender ein Objekt in der 3D-Ansicht verschiebt, ändert das den Datensatz, und die
Szene wird daraus **neu erzeugt**. Nie die Szene direkt manipulieren und hoffen, dass
das Modell folgt.

*Konsequenz:* Jedes Werkzeug hat zwei Seiten — eine, die den Datensatz ändert
(`2-FUNKTION.md`), und eine, die daraus Geometrie baut (`3-FORMELN.md`).

### A3 · Wie wird rückgängig gemacht?

**Entscheidung: Kommando-Muster mit umkehrbarem Kommando.**

Jede Anwenderhandlung wird ein Objekt mit `ausfuehren()` und `zuruecknehmen()`.
Der Stapel dieser Objekte ist die Historie.

- Kein „Zustand kopieren und zurückspielen" — bei einem Gebäudemodell zu teuer.
- Kommandos, die aus mehreren Schritten bestehen (Wand ziehen = Punkt + Punkt +
  Stärke), werden zu **einem** Kommando gebündelt, sonst nimmt Strg-Z Halbfertiges zurück.

### A4 · Wo endet die Insel?

**Entscheidung (bereits durch Yama gesetzt):** React/TypeScript bleibt auf die
Hausplaner-Insel begrenzt. Das übrige CRM bleibt Blade/jQuery. Der Planer ist ein
gekapseltes Bündel, kein Einfallstor.

*Konsequenz für die Architektur:* Der Planer darf **nichts** aus dem CRM importieren.
Der Austausch läuft über eine schmale, benannte Schnittstelle (Dokument laden,
Dokument speichern) und sonst nichts.

### A5 · Was passiert bei Unmöglichem?

**Entscheidung: lesbare Absage, kein stilles Nichts.**

Wenn die Domäne eine Eingabe nicht verarbeiten kann (nicht-rechteckige Kontur,
sich kreuzende Wände, Öffnung größer als Wand), dann:

1. wirft die Domänenschicht einen **benannten** Fehler,
2. fängt die Darstellungsschicht ihn und
3. **zeigt ihn dem Anwender in seiner Sprache.**

Ein `catch { continue; }` ist ein Fehler, kein Schutz. Genau daran ist Auftrag A-01
gescheitert: Die Domäne verweigerte korrekt, der Renderer schluckte die Absage,
und der Anwender sah ein Haus ohne Dach ohne jede Meldung.

### A6 · Was ist eine gültige Datei?

**Entscheidung: versioniertes Dokument mit Migrationspfad.**

Jedes gespeicherte Dokument trägt eine Schemaversion. Beim Laden gilt:
- gleiche Version → direkt laden
- ältere Version → durch die Migrationskette schicken
- neuere Version → **lesbare Absage**, nicht „bestmöglich raten"

Bestandsdokumente müssen nach jeder Schemaänderung nachweislich noch ladbar sein.

---

### A7 · Wie werden Werkzeuge mit ZWEI Objekten bedient?

**Entscheidung: über die vorhandene Mehrfachauswahl — alle vorgewählten Objekte sind die
Nebenrolle, das ZULETZT angeklickte ist die Hauptrolle. Parameter kommen aus dem
Eigenschaften-Panel. Kein eigener Dialog.**

*Von Yama bestätigt am 13.08.2026, vorgelegt vom Planner nach den Messungen M-1 und M-2.*

Betrifft **acht** Werkzeuge, die zwei Objekte in verschiedenen Rollen brauchen (trimmen,
verlängern, ausrichten, verschneiden …). Bis hierher galten sie als *nicht baubar, weil ein
Bedienmuster fehlt*. **Das Muster fehlte nicht — es war nur nie von einem Werkzeug benutzt
worden.** Vier Belege, je an der tragenden Stelle geöffnet:

- **Die Auswahl ist eine geordnete Liste, kein Set** — `store/hausplanerStore.ts:30`
  `selectedNodeIds: string[]`. Mit Modifikator wird **angehängt**, die Klickreihenfolge bleibt
  erhalten (`app/tools/auswahlModus.ts`, Fall `add`), und `primaerId` ist der **zuletzt**
  geklickte.
- **Die Regel ist im Code bereits begründet**, wörtlich in `auswahlModus.ts`: beim Abwählen
  rückt *„das zuletzt verbliebene"* nach, denn *„die Auswahlreihenfolge bildet ab, woran der
  Nutzer zuletzt gearbeitet hat"*.
- **Es gibt genau EINE Auswahlstelle** — `app/HausplanerApp.tsx:815 waehleAn`, von zwei Stellen
  aufgerufen; Modifikatortasten werden dort schon gelesen.
- **Das Panel hängt an der Auswahl und kann Mehrfachauswahl bereits darstellen** — es führt
  18 Zahlenfelder, bekommt die Auswahlwerte laut eigenem Dateikopf *„als Eigenschaften herein"*,
  und importiert `mehrfachUebersicht` (Mehrfachauswahl-Übersicht, AUF-35a).

**Das ist zugleich das CAD-Standardmuster** (Trimmen: Schnittkanten vorwählen, dann das zu
kürzende Objekt klicken) — die Bedienung, die Anwender aus jedem anderen Planungswerkzeug kennen.

*Konsequenz 1:* **Ein `ConfigWizard`-Dialog ist für diese acht Werkzeuge NICHT zu bauen.** Er
bleibt die teuerste Variante und damit die letzte; wer ihn zieht, braucht dafür einen Grund,
der über „zwei Objekte" hinausgeht.

*Konsequenz 2 — die eine Zeile, die die Übersetzung festschreibt:*
> **`selectionIds` (Werkzeugvertrag) und `selectedNodeIds` (Store) sind DIESELBE Größe.**
> Der Vertrag nennt sie `selectionIds` — **21 Vorkommen, alle in `app/tools/werkzeugVertrag.ts`**;
> der Store nennt sie `selectedNodeIds` — **20 Dateien**. Die Reihenfolge ist in beiden
> bedeutungstragend, das **letzte** Element ist die Hauptrolle. **Wer ein Werkzeug anschließt,
> übersetzt an der Vertragsgrenze und nirgends sonst.**

*Konsequenz 3:* Ein Werkzeug, das **mehr** als Auswahl + Zahlenfelder braucht, ist damit **nicht**
abgedeckt — das ist dann eine eigene Entscheidung und keine Auslegung dieser hier.

## B. Funktionale Anforderungen — was der Planer können muss

| Nr | Anforderung | Werkzeug |
|---|---|---|
| F-01 | Grundriss zeichnen: Wände als Linienzug mit Stärke | W-02 |
| F-02 | Wände fangen an Raster, Endpunkt, Mittelpunkt, Verlängerung, Lot | W-01 |
| F-03 | Wände nachträglich verschieben, verlängern, teilen, löschen | W-03 |
| F-04 | Türen und Fenster in Wände setzen, mit Brüstungs- und Sturzhöhe | W-04 |
| F-05 | Räume automatisch aus geschlossenen Wandzügen erkennen | W-05 |
| F-06 | Mehrere Geschosse, übereinander ausgerichtet, einzeln sichtbar | W-06 |
| F-07 | Dach aus der Gebäudekontur erzeugen (Sattel, Walm, Pult, Flach) | W-07 |
| F-08 | Dachflächen einzeln messen: Fläche, Neigung, Ausrichtung | W-08 |
| F-09 | Treppen zwischen Geschossen mit Steigungsverhältnis | W-09 |
| F-10 | Decken und Böden aus Raumumriss | W-10 |
| F-11 | Bemaßung setzen und drucken | W-11 |
| F-12 | Ansicht wechseln: Grundriss, 3D, Schnitt, Wandansicht | W-12 |
| F-13 | Objekte auswählen und über Griffe verändern | W-13 |
| F-14 | Kopieren, spiegeln, drehen, in Reihe legen | W-14 |
| F-15 | Material und Farbe je Bauteil | W-15 |
| F-16 | Bestehenden Grundriss als Bild unterlegen und abpausen | W-16 |
| F-17 | Speichern, laden, exportieren | W-17 |
| F-18 | Modell auf Widersprüche prüfen | W-18 |
| F-19 | Sonnenstand und Verschattung (für PV) | W-19 |
| F-20 | Mengen ermitteln: Wandfläche, Dachfläche, Umfang | W-20 |

---

## C. Nichtfunktionale Anforderungen — woran es scheitert, wenn man sie vergisst

| Nr | Anforderung | Messbar woran |
|---|---|---|
| N-01 | **Flüssiges Drehen** der 3D-Ansicht | ≥ 30 Bilder/s bei einem Haus mit 2 Geschossen, 40 Wänden, Dach |
| N-02 | **Antwortzeit beim Zeichnen** | Wandvorschau folgt der Maus ohne merkliche Verzögerung (< 50 ms) |
| N-03 | **Kein Datenverlust** | Absturz oder Neuladen kostet höchstens die letzte Handlung |
| N-04 | **Drei Bildbreiten** | Bedienbar bei 1440, 1024 und 375 Pixeln |
| N-05 | **Nachvollziehbarkeit** | Jede berechnete Zahl muss auf eine F-Nummer zurückführbar sein |
| N-06 | **Bestandsschutz** | Jedes vor der Änderung gespeicherte Dokument lädt danach noch |
| N-07 | **Keine Fremdwirkung** | Der Planer verändert keine Produktdaten als Nebenwirkung |

---

## D. Offene Entscheidungen

| Nr | Frage | Wer entscheidet |
|---|---|---|
| O-01 | Werden gekrümmte Wände unterstützt, oder nur gerade? | OFFEN — Yama |
| O-02 | Ist IFC-Export ein Ziel, oder reicht ein eigenes Format? | OFFEN — Yama |
| O-03 | Wird die Statik geprüft, oder ist der Planer rein geometrisch? | OFFEN — Yama |
| O-04 | Mehrbenutzer gleichzeitig am selben Dokument? | OFFEN — Yama |

> Diese vier Fragen sind nicht akademisch. **O-01 entscheidet über das Datenmodell
> der Wand** (Strecke oder Kurve), **O-02 über das Datenmodell insgesamt**
> (eigenes vs. IFC-nah). Wer sie später beantwortet, baut zweimal.
