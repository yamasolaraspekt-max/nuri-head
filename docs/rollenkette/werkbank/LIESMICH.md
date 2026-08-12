# WERKBANK 3D-HAUSPLANER

> Wissensordner für den Bau des 3D-Hausplaners.
> **Losgelöst vom laufenden Betrieb** — hier wird nichts gebaut, hier wird festgelegt,
> *wie* gebaut wird. Der Planner bedient sich hier, bevor er einen Auftrag schneidet.

---

## Wofür dieser Ordner da ist

Der Hausplaner ist kein einzelnes Programm, sondern eine **Werkbank aus Werkzeugen**.
Jedes Werkzeug ist ein eigenständiger Handgriff des Anwenders: eine Wand ziehen,
ein Fenster setzen, ein Dach aufsetzen, eine Fläche messen.

Bisher lag das Wissen dazu verstreut: die Mathematik im Code, die Bedienung im Kopf,
die Begründung im Auftragsblatt. **Wenn ein Werkzeug rot wird, muss man drei Orte
lesen, um zu verstehen warum.** Dieser Ordner dreht das um: pro Werkzeug ein Ordner,
in dem alles steht — Zweck, Funktion, Formel, Bedienung, Code, Prüfung, Grenzen.

---

## Der Aufbau

```
hausplaner-werkbank/
│
├── LIESMICH.md              ← dieses Blatt
│
├── 00-ARCHITEKTUR/          Was ein Software-Architekt festlegen MUSS,
│                            bevor die erste Zeile Code entsteht.
│   ├── ANFORDERUNGEN.md     Die Pflichtentscheidungen, je mit Begründung
│   ├── SCHICHTEN.md         Domäne / Geometrie / Renderer / Oberfläche / Speicher
│   ├── DATENMODELL.md       Projekt → Gebäude → Geschoss → Raum → Bauteil
│   └── TECHNIK.md           Sprache, Bibliotheken, warum diese und keine anderen
│
├── 01-MATHEMATIK/           Die Formeln — nummeriert, einzeln prüfbar.
│   ├── FORMELSAMMLUNG.md    F-001 … F-nnn, jede mit Eingabe/Ausgabe/Grenzfall
│   ├── POLYGON.md           Orientierung, Fläche, Punkt-im-Polygon, Offset
│   ├── DACH.md              Straight Skeleton — der Kern der Dachkonstruktion
│   └── KOERPER.md           Extrusion, CSG, Transformationen
│
├── 02-WERKZEUGE/            Ein Ordner je Werkzeug. DAS ist das Herzstück.
│   ├── REGISTER.md          Liste aller Werkzeuge + Zustand + Abhängigkeiten
│   ├── _VORLAGE/            Muster — jedes neue Werkzeug wird hiervon kopiert
│   ├── W-01-raster-und-fang/
│   ├── W-02-wand-zeichnen/
│   └── …                    (20 Werkzeuge, siehe REGISTER.md)
│
├── 03-PLANNER-SKILLS/       EXISTIERT NICHT — und wird nicht angelegt.
│                            Berichtigt vom planner 13.08.: dieses Verzeichnis war
│                            hier beschrieben und wurde NIE angelegt (git-Historie:
│                            0 Treffer auf ein hinzugefuegtes Blatt darin). Die Sache
│                            existiert, nur an einem anderen Ort — und ein Verzeichnis
│                            anzulegen, das eine zweite Wahrheit erzeugt, waere
│                            schlimmer als die falsche Beschreibung.
│                            WO ES WIRKLICH LIEGT: docs/rollenkette/rollen/1-planner/
│                              1-AUFTRAG.md            Auftrag, Befugnis, Grenzen
│                              2-WANN-BIN-ICH-DRAN.md  + die neun Pflichtpruefungen
│                              3-WAS-ICH-LESE.md         mit ihren Erweiterungen
│                              4-WAS-ICH-ABLIEFERE.md
│                              5-WAS-ICH-NICHT-DARF.md
│                              SKILL-formel-pruefen.md
│                              SKILL-werkzeug-anlegen.md
│                            EINE ECHTE LUECKE BLEIBT, und sie ist benannt statt
│                            stillschweigend: SKILL-auftrag-schneiden.md gibt es
│                            NICHT — die Kernfaehigkeit der Rolle hat kein eigenes
│                            Blatt. Ihre Regeln stehen verstreut in 1-AUFTRAG.md, das
│                            am 12./13.08. um vier Pflichtpruefungs-Erweiterungen
│                            gewachsen ist (zwei Bauformen bei Abwesenheit, zwei
│                            Muster je Zahl, Exporte vor dem Scope zaehlen, kann der
│                            Nachweis rot werden, ist die KENNUNG frei).
│                            WARUM ICH ES NICHT EINFACH SCHREIBE: ein zweites Blatt,
│                            das dieselben Pruefungen wiederholt, ist eine zweite
│                            Wahrheit — und die verbietet die Bauordnung ausdruecklich.
│                            Ein SKILL-auftrag-schneiden.md ist nur dann etwas wert,
│                            wenn 1-AUFTRAG.md die Pruefungen danach NICHT mehr
│                            traegt. Das ist ein eigener Vorgang und keine
│                            Doku-Berichtigung.
│
└── 04-QUELLEN/              Belegte Fundstellen, keine Behauptungen.
    └── QUELLEN.md
```

---

## Was in JEDEM Werkzeugordner steht

Sieben Teile, immer in derselben Reihenfolge, immer gleich benannt:

| Datei | Beantwortet die Frage |
|---|---|
| `1-ZWECK.md` | Welches Problem des **Anwenders** löst das Werkzeug? |
| `2-FUNKTION.md` | Was tut es technisch — Eingabe, Verarbeitung, Ausgabe |
| `3-FORMELN.md` | Welche Formeln aus `01-MATHEMATIK` werden benutzt (F-Nummern) |
| `4-BEDIENUNG.md` | Wie bedient der Anwender es — Klicks, Tasten, Rückmeldungen |
| `5-CODE/` | Der tatsächliche Code + Verweis auf den Ort im Repo |
| `6-PRUEFUNG.md` | Woran erkennt man, dass es funktioniert — messbare Kriterien |
| `7-GRENZEN.md` | Was es **nicht** kann, und wie es das dem Anwender sagt |

**`7-GRENZEN.md` ist Pflicht, nicht Kür.** Der teuerste Fehler in diesem Projekt
bisher war ein Dach, das bei nicht-rechteckiger Kontur unsichtbar verschwand,
statt eine lesbare Absage zu geben. Ein Werkzeug ohne benannte Grenze baut
genau diesen Fehler wieder ein.

---

## Wie der Planner damit arbeitet

1. **Vor jedem Auftrag**: `02-WERKZEUGE/REGISTER.md` lesen — existiert das Werkzeug
   schon, hängt es an einem anderen?
2. **Beim Schneiden**: den Werkzeugordner lesen. `3-FORMELN.md` sagt, ob die
   Mathematik überhaupt trägt. `7-GRENZEN.md` sagt, ob die Anforderung erfüllbar ist.
3. **Bei einer Behauptung über Machbarkeit**: nicht schätzen — in `01-MATHEMATIK`
   nachschlagen oder messen. Eine unbelegte Machbarkeitsaussage hat schon einmal
   einen ganzen Auftrag rot gemacht.
4. **Nach dem Bau**: den Werkzeugordner nachführen. Was gebaut wurde, steht hier —
   sonst driftet der Ordner und wird wertlos.

---

## Was dieser Ordner NICHT ist

- **Kein Prozessregelwerk.** Wie gearbeitet wird, steht in `docs/ARBEITSREGELN.md`.
  Hier steht nur, *was* gebaut wird und *womit*.
- **Kein Statusordner.** Der Zustand der Aufträge steht in `docs/STATUS.md`.
  `REGISTER.md` führt nur, welches Werkzeug es gibt und woran es hängt.
- **Kein Archiv.** Was hier steht, gilt. Überholtes wird ersetzt, nicht angehängt.
