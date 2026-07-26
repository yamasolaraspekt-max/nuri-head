# Fahrplan Hausplaner — Stand 26.07.2026

**Planner · Yamas Entscheidung vom 26.07.: Ziel ist „Hülle fertig", Form ist ein Fahrplan-Dokument.**
**Aus diesem Dokument entsteht kein einziger Posten von selbst.** Erst wenn Yama eine Phase
freigibt, schreibe ich die Aufträge — sonst wächst die Tafel schneller, als sie abgearbeitet wird,
und der Fortschritt fällt rechnerisch, ohne dass jemand langsamer geworden wäre.

**Zwei Quellen, beide gemessen:** die Auftragstafel (83 Zeilen, 65 im Archiv) und die
Referenzliste `docs/planner/referenzliste-mockups-2026-07-26.md`.

---

## Die Entscheidung, die diesem Fahrplan zugrunde liegt

**„Frontend fertig" heißt: die Hülle ist bedienbar und ehrlich.** Nicht: alles, was in den
Mockups steht. Damit stehen wir bei **78 %** von dem, was wir uns vorgenommen haben — und **nicht**
bei 40 %, wie es aussähe, wenn man Import, technischen Wizard und Leisten-Editor mitzählt.

**Beide Zahlen sind wahr. Sie beantworten nur verschiedene Fragen.** Diese Unterscheidung ist der
eigentliche Ertrag der Referenzliste, und sie gehört an den Anfang, damit später niemand die eine
Zahl gegen die andere ausspielt.

---

## Phase 1 — Hülle fertig · **läuft** · 19 offene Tafelposten

Das ist die aktuelle Staffel plus das, was ohnehin auf der Tafel steht. **Ende der Phase: 83 von
83, also 100 % der heutigen Tafel.**

| Stufe | Posten | Was danach wahr ist | Größe |
|---|---|---|---|
| **1.1 · läuft** | AUF-78 *(berichtet)* · AUF-82 *(aktiv)* · AUF-79 | Echte Projekte auf dem Startbildschirm · der Wächter heilt sich und schweigt nicht mehr · der Fortschritt schreibt sich selbst | klein |
| **1.2** | AUF-81 | Konfigurator-Pakete liegen serverseitig (DB · Migration · Routing · Pagination) — **die erste Migration des Projekts** | mittel |
| **1.3** | AUF-66 | „Letztes Projekt fortsetzen" in **einem** Klick. Braucht AUF-78 | klein |
| **1.4** | AUF-76 → AUF-77 | Wandschichten, dann Wandfläche brutto/netto — **die Rechnung, auf der Putz, Dämmung, Anstrich, Fassade und Heizlast alle aufsetzen** | mittel |
| **1.5** | AUF-54 · 55 · 56 · 63 | Farbe als Parameter · Snapshot ehrlich · zwei Elevation-Token · jsdom (**allein**, er ändert den Testläufer) | klein |
| **1.6** | AUF-35b · 42 · 48 | Flächenauswahl · `viewport.ready` ehrlich · `HausplanerApp.tsx` (2 229 Zeilen) zerlegen | mittel |
| **1.7** | AUF-38 | Inline-Styles ablösen, acht Scheiben. **Läuft neben nichts** — er fasst acht Oberflächendateien an | mittel |
| **1.8** | AUF-52 | Zwölf Rechen-Engines anschließen, drei Scheiben. Heute: **1 von 13** | groß |
| **1.9** | **AUF-50** | **Die 110 Werkzeuge funktionstüchtig machen**, vier Stufen. Der größte Brocken der Phase — und der Posten, der „bedienbar" überhaupt erst wahr macht | **groß** |

**Ehrlich zur Zeit:** 1.1 bis 1.5 sind Tagesarbeit. 1.6 bis 1.9 sind es nicht — **AUF-50 allein
ist ein Fahrplan in sich**, und AUF-52 hat drei einzeln abzunehmende Scheiben. Wer „diese Woche"
sagen will, meint 1.1–1.5.

---

## Phase 2 — Der Eingang · **die größte einzelne Lücke** · nicht freigegeben

Wie kommt ein **Bestandsplan** ins Modell? Heute gar nicht. Vorhanden sind Werkzeugnamen
(`bild-importieren`, `grundriss-erkennen`, `kalibrieren`, `erkennung-bestaetigen`,
`nordrichtung-setzen`) und der Arbeitsbereich `WORKSPACE_IMPORT` — **dahinter ist nichts**.

| Stufe | Gegenstand | Warum in dieser Reihenfolge | Größe |
|---|---|---|---|
| **2.1** | **Quelle** — Datei laden, Seiten wählen, Geschoss zuordnen, Layer lesen (PDF · PNG · DWG) | Ohne Quelle gibt es nichts zu kalibrieren | mittel |
| **2.2** | **Kalibrierung** — zwei Punkte, reales Maß, `1 px = x mm` | Ohne Maßstab ist jede erkannte Wand eine Zahl ohne Einheit | klein |
| **2.3** | **Referenz-Layer** — Deckkraft, S/W, Invertieren, gesperrt | Der Plan muss sichtbar bleiben und darf nichts erzeugen. **Das ist der Zustand, in dem manuell nachgezeichnet wird** — und damit der erste Punkt, an dem der Import allein schon nützt | klein |
| **2.4** | **Nachzeichnen** — Wandachse · Wandkanten · Polylinie · Raumkontur · Öffnung · Dachkontur · Korrekturstift | Manuell vor automatisch: **ein Werkzeug, das der Mensch führt, ist prüfbar; eine Erkennung, die niemand nachzeichnen kann, ist es nicht** | mittel |
| **2.5** | **Erkennung** mit **Vertrauensstufen** (sicher · wahrscheinlich · prüfen · unsicher), Einzelprüfung „Objekt 2/7", „Alle sicheren übernehmen" | Zuletzt, weil es auf 2.1–2.4 aufsetzt. **Kernbedingung: nichts wird ohne Bestätigung Bauteil** | **groß** |
| **2.6** | **Overlay · Split · Differenz** gegen die Referenz | Die Kontrolle, ob das Nachgezeichnete zum Plan passt | klein |

**Der Satz, an dem diese Phase hängt:** *erkannte Objekte werden erst nach Bestätigung zu
Bauteilen.* Steht so im Mockup, und er ist die einzige Zusage, die eine Erkennung überhaupt
verantwortbar macht.

---

## Phase 3 — Fach-Übergabe · nicht freigegeben

Der Ablauf **nach** der Geometrie: „Heizung planen", Schritt 8/15, Abhängigkeitskette
Bauphysik → Lüftung → Heizlast → Übergabe → hydraulischer Abgleich → Wärmepumpe.

| Stufe | Gegenstand | Größe |
|---|---|---|
| **3.1** | **Datencheck** als Soll/Ist-Liste (Räume 12/12 · Solltemperaturen 10/12 · Fensterwerte 8/10 · Wärmebrücken *geschätzt* · Lüftung *fehlt*) | mittel |
| **3.2** | **Abhängigkeitskette** — ein Schritt ist gesperrt, **solange seine Voraussetzung fehlt, und der Grund steht daneben** | mittel |
| **3.3** | **Übergabepaket an das Fachmodul** mit „Noch nicht startbereit" und benanntem Fehlgrund | mittel |

**Diese Phase ist der Punkt, an dem der Hausplaner aufhört, ein Zeichenprogramm zu sein.**
Sie setzt Phase 1.8 (Engines) voraus — ohne angeschlossene Rechenwege gibt es nichts zu übergeben.

---

## Phase 4 — Personalisierung · nicht freigegeben

| Stufe | Gegenstand | Größe |
|---|---|---|
| **4.1** | **Leisten-Editor** — Drag-and-drop aus der Registry, Einfügeposition, Zurücksetzen | mittel |
| **4.2** | **Workspace-Vorlagen** (Architektur · Dach · Bauphysik · Heizlast · Heizung · Elektro · PV) mit persönlicher Kopie | klein |
| **4.3** | „Als Vorlage exportieren" / Layout speichern | klein |

**Die Daten liegen bereits** (`state/angeheftet.ts`, `state/arbeitsbereichSpeicher.ts`) — es fehlt
die Bedienoberfläche. **Deshalb ist diese Phase kleiner, als sie im Mockup aussieht.**

---

## Querschnitt — passt in keine Phase, gehört aber irgendwohin

| Gegenstand | Wo es hingehört | Bemerkung |
|---|---|---|
| **Projektidentität** in der Kopfzeile (H-2041 · Kd. · Familienname) | direkt nach AUF-66 | Baut auf AUF-78 auf, ist klein, und macht jeden Screenshot ab dann eindeutig |
| **Prüfungscenter** mit echter Befundliste | eigener Posten, **verlangt eine Store-Änderung** | Heute liefert `befunde.ts` **0 oder 1** Befund, weil der Store **eine** Meldung hält. In `befunde.ts` bereits als offener Posten benannt |
| **Versionen · Verlauf · Auto-Speichern** | nach Phase 2 | Wird erst wichtig, wenn viel Arbeit in einem Dokument steckt |
| **Projektfortschritt „9 von 20 Schritten"** | nach Phase 3 | **Nicht mit AUF-79 verwechseln** — das zählt *Aufträge*, dieses zählt *Planungsschritte* |
| **Tab blendet Panels aus · Zoom-Menü · Bibliothek-Reiter** | Sammelposten, klein | Drei Kleinigkeiten, ein Auftrag |

---

## Ausdrücklich **nicht** im Fahrplan

**Anwesenheit / Mehrbenutzer** („2 anwesend · Schreibrecht · Live"). Das ist **kein
Layout-Posten, sondern ein Nebenläufigkeits-System**: Sperren, Konfliktauflösung, Übertragungsweg,
Wiederherstellung nach Verbindungsabbruch. **Wer das nebenbei baut, baut es falsch** — und es
gehört in dieselbe Größenordnung wie Phase 2, nicht in eine Zeile.

---

## Was ich **nicht** weiß, und als Schätzung kennzeichne

Die **Größenangaben in den Phasen 2 bis 4 sind Schätzungen, keine Messungen.** Gemessen ist nur,
was **fehlt** (Fundstellen im Code) — nicht, was es kostet, es zu bauen. Sobald Yama eine Phase
freigibt, wird die erste Stufe **vor** dem Bauen gemessen, wie bei AUF-78: erst der Befund, dann
der Auftrag.

**Die Phasen 2 bis 4 zusammen sind größer als die gesamte bisherige Arbeit am Hausplaner.** Das ist
kein Grund, sie nicht zu machen — aber ein Grund, sie nicht nebenbei anzufangen.
