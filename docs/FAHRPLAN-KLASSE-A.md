# Fahrplan Klasse A — sieben Blätter, drei Runden, ein Sonderfall

```yaml
art: "PLANNER-FAHRPLAN — Antwort auf Yamas Ansage 'A vor C, aber A auch fertigstellen'"
vorgelegt_von: planner
gemessen_am: "2026-08-10 abends, alle sieben in EINEM Durchgang"
zweck: "Klasse A abschliessen, ohne sie hinzuziehen — mit benannter Rundenzahl"
ballbesitz: "planner (Schnitt), dann plan-pruefer je Blatt"
```

## Die Messung — alle sieben offenen Werkzeuge in einem Durchgang

*Damit ist der Engpass erledigt: die Anbindung ist gemessen, die Blätter selbst sind danach knapp,
weil W-01 und W-02 das Muster gesetzt haben.*

```text
W      Zeilen  Exporte  Registry           Zusagen(ded/erw)  Module
W-08      286        8  flaeche-messen             2 / 4          2
W-04      277       28  fenster + tuer             3 / 5          3
W-11      395       22  bemassen                   3 / 4          3
W-05      371       16  KEINE                      2 / 4          3
W-21      496       26  KEINE                      5 / 3          5
W-22      498       26  KEINE                      1 / 1          1
W-09      698       30  treppe                     6 / 13         7
W-07     3626      140  dach                       7 / 10         8   <- Sonderfall
                                                   ------
zusammen 6647      296                             29 / 44
```

**Bereits durch oder laufend:** W-01 (`CODE_FERTIG`), W-02 (`IN_ARBEIT`), W-13 (`ENTWURF`, DoR-Rest
behoben). *Klasse A umfasst zehn Werkzeuge; sieben sind hier offen.*

## Die drei Runden

```text
RUNDE 1 — die drei mit Registry und wenig Umfang
  W-08  Dachflaeche messen    286 Z · Registry da · 2 Module
  W-04  Oeffnung Tuer/Fenster 277 Z · ZWEI Registry-Werkzeuge (fenster, tuer)
  W-11  Mass und Bemassung    395 Z · Registry da · 3 Module
  -> zusammen 958 Zeilen. Alle drei haben ein bedienbares Werkzeug, alle drei
     haben dedizierte Zusagen. Der einfachste Fall: beschreiben, verlinken, fertig.

RUNDE 2 — die drei OHNE Registry-Werkzeug
  W-05  Raum erkennen         371 Z · keine Registry · laeuft roomDetection automatisch?
  W-21  Sparren und Lattung   496 Z · keine Registry · 5 Module, 5 dedizierte Zusagen
  W-22  Gaube                 498 Z · keine Registry · 1 Modul
  -> alle drei sind DIESELBE LAGE WIE W-01: Rechenschicht gebaut, Werkzeugschicht
     fehlt. Sie beruehren damit die offene Werkzeug-oder-Schicht-Frage
     (VORLAGE-WERKZEUG-ODER-SCHICHT.md) — aber sie sind NICHT davon blockiert:
     W-01 wurde ohne die Entscheidung fertig, indem das Blatt die Lage BENANNTE.
     Jedes der drei tut dasselbe: beschreiben, Lage benennen, Frage nicht entscheiden.

RUNDE 3 — der Sonderfall
  W-07  Dach aus Kontur      3626 Z · 140 Exporte · 8 Module · schon BESCHRIEBEN
  -> 55 % der ganzen Klasse A in einem Werkzeug. Und es ist das EINZIGE, das
     bereits BESCHRIEBEN ist (das Musterwerkzeug). Zu tun ist hier NICHT
     "Blaetter fuellen", sondern:
       (a) die drei Werkbank-Nachtraege N1/N2/N3 betreffen W-07s Formeln
       (b) W-07 beschreibt den F-020-Weg, die Insel baut den roof.anbau-Weg
           (Befund db1dc3b6) — die Wegfrage haengt an A-12
       (c) 5-CODE muss auf die NEUN Dach-Module zeigen, heute nennt das Register drei
  -> W-07 ist damit KEIN Klasse-A-Blatt wie die anderen, sondern der Anschluss
     an die Dachkonstruktion. Es gehoert HINTER A-12, nicht in Runde 1 oder 2.
```

## Was „fertig" für Klasse A heißt — damit es nicht hingezogen wird

```text
Runde 1 fertig   drei Blaetter geschnitten, DoR durch, Generator hat sie gebaut,
                 Register traegt W-04/W-08/W-11 als BESCHRIEBEN mit Fundstellen
Runde 2 fertig   dito fuer W-05/W-21/W-22, jedes mit benannter Schicht-Lage
Klasse A fertig  die zehn Werkzeuge stehen im Register als BESCHRIEBEN, jedes mit
                 5-CODE auf seine Module und 7-GRENZEN am Code gemessen.
                 AUSNAHME W-07: es haengt an A-12 und wird als solche gefuehrt,
                 nicht als offener Rest.
```

> **Der Abschluss ist an einer Zahl messbar — mit dem PRÄZISEN Befehl:**
>
> ```text
> grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN' docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md
> heute  3    (W-01, W-02, W-07)      Ziel  10
> ```
>
> **⚠ Korrigiert beim Committen dieses Blattes.** Ich hatte „heute 1 (nur W-07)" geschrieben und
> `grep -c 'BESCHRIEBEN'` als Befehl genannt. **Beides falsch:** der Befehl zählt die
> Reifegrad-Legende mit (4 statt 3), und die Zahl war veraltet — W-01 und W-02 sind inzwischen
> gebaut. *Dritter Fall heute, in dem mein `grep`-Muster die Legende mitzählt; beim Register-Stand
> „23 LEER" war es dasselbe (richtig: 22). **Ein Zählbefehl über eine Tabelle muss an die
> Zeilenform gebunden sein, nicht an das Wort.***

## Was diesen Fahrplan schneller macht als die ersten drei Blätter

```text
1  Anbindung ist GEMESSEN — der Engpass der ersten drei Blaetter lag in der Messung,
   nicht im Schreiben. Sie ist jetzt fuer alle sieben erledigt.
2  Das Muster steht — W-01 und W-02 haben Struktur, Kriterienform und Rot-Form
   festgelegt. Die folgenden Blaetter VERWEISEN darauf statt sie zu wiederholen.
3  Kriterien sind wortgleich uebertragbar — 1-1 bis 1-9 unterscheiden sich nur in
   den Modulnamen und Zahlen. Was NICHT uebertragbar ist: 7-GRENZEN und die
   Ausschluesse; die sind je Werkzeug eigen und werden gemessen.
```

**Was NICHT abgekürzt wird — ausdrücklich:**

- **Keine Sammelblätter.** §3 lässt einen `IN_ARBEIT` zu; vier Werkzeuge in einem Blatt umgehen die
  Regel statt sie zu erfüllen. *Yamas Punkt 7.2.*
- **Keine übernommene Zuordnung.** Drei von vier geprüften Matrix-Zeilen waren falsch. Jede Zeile
  wird am Export gemessen, auch wenn es langsamer ist.
- **Kein Kriterium ohne Rot-Lage.** Die Platzhalterzahl wird je Werkzeug gezählt, nicht geschätzt —
  meine erste Zählung war zu klein (8 statt 26), das wiederhole ich nicht.

```yaml
naechster_schritt: "Runde 1 schneiden: W-08, W-04, W-11 — knapp, mit Verweis auf W-01/W-02"
w07_gehoert: "hinter A-12 (Dachweg-Frage), nicht in Klasse A wie die anderen"
abschluss_messbar_an: "grep -c 'BESCHRIEBEN' REGISTER.md — heute 1, Ziel 10"
nicht_blockiert_durch: "die Werkzeug-oder-Schicht-Vorlage — W-01 wurde ohne sie fertig"
```
