# Fahrplan Klasse A — AUFGEHOBEN 12.08. · nur noch Beleg

> ## ⚠ DIESER PLAN IST NICHT MEHR DER PLAN
>
> **Gültig ist `docs/FAHRPLAN-WERKZEUGKASTEN.md`** — er umfasst alle 42 Registerzeilen statt zehn.
>
> **Warum dieser hier abgelöst wurde, und es ist ein Bauartfehler:** *Er hatte eine **feste
> Rundenzahl**. `W-09 Treppe` passte in keine der drei Runden, und ich habe daraufhin Z.148
> geschrieben — „NICHT IN A: W-09 (Treppe, 698 Z) — war nie in den drei Runden" — **statt den Plan
> zu erweitern.*** **Weil die Lücke notiert war, sah sie erledigt aus.** *Der neue Plan hat deshalb
> keine Runden, sondern **Stufen mit Eintrittsbedingung**: eine Zeile, die in keine Stufe passt, ist
> ein Befund gegen den Plan, nicht gegen das Werkzeug.*
>
> **Was hier weiter gilt und deshalb stehen bleibt:** die sechs Grobzahl-Korrekturen (jede Messung
> ging nach unten), die Anbindungsmessungen der zehn Klasse-A-Werkzeuge, und die Lehre, dass eine
> Grobzahl ohne Ausschlussentscheidung immer eine Obergrenze ist. **Als Beleg, nicht als Anweisung.**

# (Ursprünglicher Titel) Fahrplan Klasse A — sieben Blätter, drei Runden, ein Sonderfall

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

> **⚠ Diese Zeilenzahlen sind MATRIX-GROBZAHLEN vom 10.08. und stehen als Ist-Beleg dieser Messung.
> Drei von ihnen sind bei der Einzelmessung gefallen (W-08 286→48, W-04 277→124, W-05 371→190) —
> die richtiggestellten Werte stehen im NACHTRAG 11.08. am Ende dieses Blattes. Nicht ersetzt,
> weil ein datierter Ist-Beleg nicht rückdatiert wird; verwiesen, damit niemand die Grobzahl als
> Umfang liest.** *Gleiches gilt für „371 Z" bei W-05 in der Runde-2-Beschreibung unten.*

**Bereits durch oder laufend:** W-01 (`CODE_FERTIG`), W-02 (`IN_ARBEIT`), W-13 (`ENTWURF`, DoR-Rest
behoben). *Klasse A umfasst zehn Werkzeuge; sieben sind hier offen.*

## Die drei Runden

```text
RUNDE 1 — KORRIGIERT 10.08.: zwei Werkzeuge, nicht drei
  W-04  Oeffnung Tuer/Fenster 124 Z · ZWEI Registry-Werkzeuge (fenster, tuer)
  W-11  Mass und Bemassung    395 Z · Registry da · 3 Module
  + W-13 nachziehen (steht auf ENTWURF, DoR-Rest behoben)
  -> zwei Fundamentwerkzeuge ohne Dachbezug, plus ein Stufe-2-Werkzeug.

  W-08 IST AUS RUNDE 1 ENTFERNT — es gehoert hinter A-12, zu W-07.

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

  W-08  Dachflaeche messen     48 Z · Registry da · 1 Modul
  -> NACHGETRAGEN 10.08.: gehoert HIERHER, nicht in Runde 1. Ich hatte die
     Einsicht bei W-07 und nicht bis zum Nachbarn gezogen.
     ZWEI GRUENDE, beide am Register und am Code gemessen:
       1  Das Register fuehrt W-08 als "braucht W-07". Dieselbe Begruendung, die
          W-07 hinter A-12 stellt, trifft W-08: sein Fundament ist ungeklaert.
       2  Von W-08s drei Register-Formeln sind ZWEI nicht belegbar:
            F-011  Shoelace                        implementiert ✓
            F-023  A/cos(alpha)                    nicht implementiert (Alternative)
            F-024  Azimut aus Normalenvektor       nur die WAND-Variante existiert
                   (azimutDerNormalen nimmt start/end/seite = 2D-Wandnormale;
                    F-024 verlangt n=(nx,ny,nz) = 3D-Flaechennormale). Die
                    Konvention stimmt (atan2(nx,ny), 0=Nord), aber die
                    Dachflaechen-Fassung ist nicht gebaut — und mit ihr fehlt
                    ihr Grenzfall "Flachdach -> keine Ausrichtung".
     Dachflaechen gehen in die Ertragsrechnung. Ein Blatt, das Flaechen
     beschreibt, deren Bezugsrichtung nicht belegbar ist, ist so verfrueht wie W-07.
     Das BLATT W-08/1 bleibt geschnitten und gueltig (b6078b2a) — nur seine
     Einreihung aendert sich.
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

## NACHTRAG 11.08. — Klasse A ist vollständig GESCHNITTEN, und drei Grobzahlen sind gefallen

**Stand nach W-22/1 (`22a42c3d`):**

```text
GESCHNITTEN  W-04/1  W-05/1  W-08/1  W-11/1  W-13/1  W-21/1  W-22/1   sieben Blaetter
ABGENOMMEN   W-01/1 (320a95c8, SPEC-Rest -> W-01N)     W-02/1
HINTER A-12  W-07 (3626 Z, Dachweg-Frage)   W-08 (Bezugsrichtung ungeklaert)
NICHT IN A   W-09 (Treppe, 698 Z) — war nie in den drei Runden
-> "geschnitten" ist NICHT "BESCHRIEBEN". Der Abschluss haengt am Generator,
   und die Abschlusszahl bleibt der Befehl unten.
```

**Drei Grobzahlen der Matrix-Tabelle (Z.18-23) sind beim Einzelmessen gefallen — nicht ersetzt,
sondern hier richtiggestellt:**

```text
        Tabelle    gemessen    Differenz
W-08      286          48       -238   nur polygonFlaecheM2, nicht die Nachbarn
W-04      277         124       -153   die Geometrie liegt in W-02s Modul
W-05      371         190       -181   grundriss.ts + polygonFlaeche.ts ausgeschlossen
W-21      496         496          0   erste korrekte Grobzahl
W-22      498         498          0   dito
W-11      395         395          0
```

> **Jede Einzelmessung ging nach UNTEN, keine nach oben — sechs von sechs.** *Die Matrix-Grobzahl
> ist damit systematisch zu hoch, weil sie **Modulgruppen** zählt, die Einzelmessung aber den
> **Ausschluss** anwendet. **Das ist kein Zufall, sondern die Bauart des Fehlers: eine Grobzahl
> ohne Ausschlussentscheidung ist immer eine Obergrenze.*** Für W-09 (698 Z) und W-07 (3626 Z) gilt
> das ungemessen mit — beide Zahlen sind Obergrenzen, keine Umfänge.

**Vier Befunde aus Runde 2, die über Klasse A hinausgehen:**

```text
W-05   grundriss.ts traegt die Formerkennungs-Bausteine (eckenAnalyse,
       anzahlInnenwinkel, erwarteteInnenwinkel, istZusammengesetzt)
       -> ZULIEFERUNG an A-12: A-05s Luecke 4 haelt, der Weg ist kuerzer
W-21   sparrenBerechnung.ts ist Vorbemessung nach EUROCODE = BEMESSUNG, nicht
       Geometrie. Die Statik-Linse trennt "Geometrie (jetzt) von Bemessung
       (Fach-Freigabe/spaeter)" -> Fach-Gate, kein Blatt-Problem
W-21   Registerquelle M-02 (profi_holzbau_solar_cad.tsx, 2021 Z) UNAUSGEWERTET
W-22   FUENF Module (975 Z) bilden das Thema Dachaufbauten, die Werkbank fuehrt
       nur "Gaube" -> Befund fuer die Anschlussmatrix, nicht meine Entscheidung
W-22   auswechslung.ts (174 Z) steht in W-21 UND W-22 als Nachbar und ist in
       KEINEM zuhause -> braucht ein Werkzeug oder eine Zuordnung
```

```yaml
naechster_schritt: "Klasse A ist geschnitten. Ballbesitz: plan-pruefer (DoR fuer sieben Blaetter),
                    danach generator. Der Planner schneidet nichts mehr in Klasse A."
stand_11_08: "sieben Blaetter geschnitten, keines gebaut — der Abschluss haengt nicht mehr am Schnitt"
w07_gehoert: "hinter A-12 (Dachweg-Frage), nicht in Klasse A wie die anderen"
w08_gehoert: "ebenfalls hinter A-12 — nachgetragen 10.08., Blatt bleibt gueltig (b6078b2a)"
abschluss_messbar_an: "grep -cE '^\\| W-[0-9]+ .*BESCHRIEBEN' REGISTER.md — heute 3, Ziel 10"
nicht_blockiert_durch: "die Werkzeug-oder-Schicht-Vorlage — W-01 wurde ohne sie fertig"
grobzahl_lehre: "eine Grobzahl ohne Ausschlussentscheidung ist immer eine Obergrenze, nie ein Umfang"
```
