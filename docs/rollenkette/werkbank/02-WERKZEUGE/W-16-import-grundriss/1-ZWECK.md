# W-16 · Grundriss unterlegen — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er hat einen Grundriss auf Papier oder als PDF und will darüber zeichnen — maßhaltig.**

Zwei Dinge, die zusammengehören:

1. **Das Bild unter die Zeichnung legen** — als Ebene, die nicht im Weg ist.
2. **Den Maßstab setzen**, indem er eine bekannte Strecke im Bild anklickt und ihre wahre Länge
   eingibt.

## Der tragende Punkt: dieses Werkzeug hat ZWEI HÄLFTEN, und die zweite liegt nicht in der Insel

**Wer nur `app/unterlage/` liest, beschreibt die Hälfte** — *und die nächste Rolle sucht die
Speicherung in der Insel, wo sie nicht ist.*

```text
INSEL (drei Module, 349 Z., SECHS Ausfuhren)
  app/unterlage/kalibrierung.ts          44 Z.
  app/unterlage/UnterlagenEbene.tsx      66 Z.
  app/unterlage/UnterlagenWerkzeuge.tsx 239 Z.

SERVER
  app/Http/Controllers/Energie/PlanUploadController.php   178 Z.
  app/Models/PlanUpload.php                                88 Z.
  routes/web.php:5679-5692                    SECHS Routen
  database/migrations/  2026_07_08_180006_create_plan_uploads_table.php
                        2026_07_30_105516_add_projektbezug_to_plan_uploads.php
```

> **Das Hochladen, das Speichern, das Ausliefern des Bildes und das Merken des Maßstabs passieren
> auf dem Server.** *Die Insel zeigt an und rechnet den Maßstab — mehr nicht.*

## Und die Einordnung ist doppelt — festgehalten, nicht entschieden

**Der Gegenstand liegt unter `Energie`:** *alle sechs Routen heißen `energie.plan-upload.*`, der
Controller steht in `app/Http/Controllers/Energie/`.* **Das Register führt W-16 als
Hausplaner-Werkzeug.**

> ***Beides trifft zu, und dieses Blatt entscheidet nicht, ob es so bleiben soll.*** *Es hält fest,
> **wo** es steht — damit die nächste Rolle nicht in `app/Http/Controllers/Hausplaner/` sucht und
> nichts findet.*

## `LEER` im Register heißt NICHT „kein Code"

**Wörtlich aus `REGISTER.md:87`:**

> *„`LEER` heißt hier **„kein Blatt gefüllt"**, nicht „kein Code vorhanden"."*

**Und `:6` nennt die Spalte:** *Reifegrad — `LEER` (nur Ordner) · `n/7 BLÄTTER` · …*

> **Die Registerzeile war also richtig, solange kein Blatt gefüllt war.** *Sie beschreibt den Stand
> der **Beschreibung**, nicht den des Codes — und der Code ist vollständig gebaut und angeschlossen.*

## Wann greift der Anwender danach?

**Ganz am Anfang.** *Bevor die erste Wand gezogen wird: Bild rein, eine bekannte Strecke kalibrieren,
dann darüber zeichnen.*

## Woran merkt er, dass es fehlt?

**Er misst den Grundriss von Hand ab und tippt Maße ein** — *oder er zeichnet nach Augenmaß und
merkt beim ersten Aufmaß, dass nichts passt.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

- **Keine automatische Wanderkennung.** *Aus dem Bild werden keine Wände abgeleitet; die Unterlage
  ist eine Vorlage zum Nachzeichnen.*
- **Kein Eingriff ins Modell.** *Die Unterlage ist nicht auswählbar, trägt keinen Klick-Handler und
  rührt weder Befehle noch Auswahl an — durch Zusagen gesichert (siehe `6-PRUEFUNG`).*
- **Keine zweite Pixel-Rechnung.** *Siehe `2-FUNKTION` — der Maßstab wird **korrigiert**, nicht neu
  aus Bildpixeln bestimmt.*
